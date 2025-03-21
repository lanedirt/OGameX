<?php

namespace OGame\Services;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use OGame\Models\BuildingQueue;
use OGame\Models\Resources;
use OGame\ViewModels\Queue\BuildingQueueListViewModel;
use OGame\ViewModels\Queue\BuildingQueueViewModel;

/**
 * Class BuildingQueueService.
 *
 * BuildingQueue object.
 *
 * @package OGame\Services
 */
class BuildingQueueService
{
    /**
     * Retrieve all build queue items that already should be finished for a planet.
     *
     * @param int $planet_id
     * @return Collection<int, BuildingQueue>
     */
    public function retrieveFinished(int $planet_id): Collection
    {
        // Fetch queue items from model
        return BuildingQueue::where([
            ['planet_id', $planet_id],
            ['time_end', '<=', Carbon::now()->timestamp],
            ['building', 1],
            ['processed', 0],
            ['canceled', 0],
        ])
            ->orderBy('time_start', 'asc')
            ->get();
    }

    /**
     * Get building queue items
     *
     * @return Collection<int, BuildingQueue>
     */
    public function retrieveQueueItems(PlanetService $planet): Collection
    {
        // Fetch queue items from model
        return BuildingQueue::where([
            ['planet_id', $planet->getPlanetId()],
            ['processed', 0],
            ['canceled', 0],
        ])
            ->orderBy('time_start', 'asc')
            ->get();
    }

    /**
     * Add a building to the building queue for the current planet.
     *
     * @param PlanetService $planet
     * @param int $building_id
     * @throws Exception
     */
    public function add(PlanetService $planet, int $building_id): void
    {
        $build_queue = $this->retrieveQueue($planet);

        // Max amount of buildings that can be in the queue in a given time.
        // TODO: refactor throw exception into a more user-friendly message.
        if ($build_queue->isQueueFull()) {
            // Max amount of build queue items already exist, throw exception.
            throw new Exception('Maximum number of items already in queue.');
        }

        // Check if user satisfies requirements to build this object.
        $building = ObjectService::getObjectById($building_id);

        // Check if building can be built on this planet type (planet or moon).
        $correct_planet_type = ObjectService::objectValidPlanetType($building->machine_name, $planet);
        if (!$correct_planet_type) {
            throw new Exception('This building can not be built on this planet type (planet or moon specific).');
        }

        // @TODO: add checks that current logged in user is owner of planet
        // and is able to add this object to the building queue.
        $current_level = $planet->getObjectLevel($building->machine_name);

        // Check to see how many other items of this building there are already
        // in the queue, because if so then the level needs to be higher than that.
        $amount = $this->activeBuildingQueueItemCount($planet, $building->id);
        $next_level = $current_level + $amount + 1;

        // Check if user satisfies requirements to build this object.
        // TODO: refactor throw exception into a more user-friendly message.
        $requirements_met = ObjectService::objectRequirementsMetWithQueue($building->machine_name, $next_level, $planet);
        if (!$requirements_met) {
            throw new Exception('Requirements not met to build this object.');
        }

        $queue = new BuildingQueue();
        $queue->planet_id = $planet->getPlanetId();
        $queue->object_id = $building->id;
        $queue->object_level_target = $next_level;

        // Save the new queue item
        $queue->save();

        // Set the new queue item to start (if applicable)
        $this->start($planet);
    }

    /**
     * Retrieve full building queue for a planet (including currently building).
     *
     * @param PlanetService $planet
     * @return BuildingQueueListViewModel
     * @throws Exception
     */
    public function retrieveQueue(PlanetService $planet): BuildingQueueListViewModel
    {
        $queue_items = $this->retrieveQueueItems($planet);

        // Convert to ViewModel array
        $list = array();
        foreach ($queue_items as $item) {
            $object = ObjectService::getObjectById($item['object_id']);

            $time_countdown = $item->time_end - (int)Carbon::now()->timestamp;
            if ($time_countdown < 0) {
                $time_countdown = 0;
            }

            $viewModel = new BuildingQueueViewModel(
                $item['id'],
                $object,
                $time_countdown,
                $item['time_end'] - $item['time_start'],
                $item['building'],
                $item['object_level_target'],
            );

            $list[] = $viewModel;
        }

        // Create BuildingQueueListViewModel
        return new BuildingQueueListViewModel($list);
    }

    /**
     * Get the amount of already existing queue items for a particular
     * building.
     *
     * @param PlanetService $planet
     * @param int $building_id
     * @return int
     */
    public function activeBuildingQueueItemCount(PlanetService $planet, int $building_id): int
    {
        // Fetch queue items from model
        return BuildingQueue::where([
            ['planet_id', $planet->getPlanetId()],
            ['object_id', $building_id],
            ['processed', 0],
            ['canceled', 0],
        ])
            ->count();
    }

    /**
     * Start building the next item in the queue (if available).
     *
     * This actually starts the building process and deducts the resources
     * from the planet. If there are not enough resources the build attempt
     * will fail.
     *
     * @param PlanetService $planet
     * @param int $time_start
     *  Optional parameter to indicate when the new item should start, this
     *  is used for when a few build queue items are finished at the exact
     *  same time, e.g. when a user closes its session and logs back in
     *  after a while.
     * @throws Exception
     */
    public function start(PlanetService $planet, int $time_start = 0): void
    {
        // TODO: add unittest for case described above with $time_start.
        $queue_items = BuildingQueue::where([
            ['planet_id', $planet->getPlanetId()],
            ['canceled', 0],
            ['processed', 0],
            ['building', 0],
        ])
            ->orderBy('id', 'asc')
            ->get();

        foreach ($queue_items as $queue_item) {
            $object = ObjectService::getObjectById($queue_item->object_id);

            // See if the planet has enough resources for this build attempt.
            $price = ObjectService::getObjectPrice($object->machine_name, $planet);
            $build_time = $planet->getBuildingConstructionTime($object->machine_name);

            // Only start the queue item if there are no other queue items building
            // for this planet.
            $current_queue = $this->retrieveQueue($planet);
            $currently_building = $current_queue->getCurrentlyBuildingFromQueue();
            if (!empty($currently_building)) {
                // There already is something else building, don't start a new one.
                break;
            }

            // Sanity check: check if the target level as stored in the database
            // is 1 higher than the current level. If not, then it means something
            // is wrong.
            $current_level = $planet->getObjectLevel($object->machine_name);
            if ($queue_item->object_level_target != ($current_level + 1)) {
                // Error, cancel build queue item.
                $this->cancel($planet, $queue_item->id, $queue_item->object_id);

                continue;
            }

            // Sanity check: check if the Research Lab is tried to upgrade when research is in progress
            if ($object->machine_name === 'research_lab' && $planet->getPlayer()->isResearching()) {
                // Error, cancel build queue item.
                $this->cancel($planet, $queue_item->id, $queue_item->object_id);

                continue;
            }

            // Sanity check: check if the planet has enough resources. If not,
            // then cancel build request.
            if (!$planet->hasResources($price)) {
                // Error, cancel build queue item.
                $this->cancel($planet, $queue_item->id, $queue_item->object_id);

                continue;
            }

            // Sanity check: check if the building requirements are still met. If not,
            // then cancel build request.
            if (!ObjectService::objectRequirementsWithLevelsMet($object->machine_name, $queue_item->object_level_target, $planet)) {
                $this->cancel($planet, $queue_item->id, $queue_item->object_id);

                continue;
            }

            // All OK, deduct resources and start building process.
            $planet->deductResources($price);

            if (!$time_start) {
                $time_start = (int)Carbon::now()->timestamp;
            }

            $queue_item->time_duration = $build_time;
            $queue_item->time_start = $time_start;
            $queue_item->time_end = $queue_item->time_start + $queue_item->time_duration;
            $queue_item->building = 1;
            $queue_item->metal = $price->metal->get();
            $queue_item->crystal = $price->crystal->get();
            $queue_item->deuterium = $price->deuterium->get();
            $queue_item->save();

            // If the calculated end time is lower than the current time,
            // we force that the planet is updated again which will grant
            // the building immediately without having to wait for a refresh.
            if ($queue_item->time_end < Carbon::now()->timestamp) {
                $planet->updateBuildingQueue();
            }
        }
    }

    /**
     * Cancels an active building queue record.
     *
     * @param PlanetService $planet
     * @param int $building_queue_id
     * @param int $building_id
     *
     * @throws Exception
     */
    public function cancel(PlanetService $planet, int $building_queue_id, int $building_id): void
    {
        $queue_item = BuildingQueue::where([
            ['id', $building_queue_id],
            ['planet_id', $planet->getPlanetId()],
            ['object_id', $building_id],
            ['processed', 0],
            ['canceled', 0],
        ])->first();

        // If object is found: add canceled flag.
        if ($queue_item) {
            // Give back resources if the current entry was already building.
            if ($queue_item->building === 1) {
                $planet->addResources(new Resources($queue_item->metal, $queue_item->crystal, $queue_item->deuterium, 0));
            }

            // Add canceled flag to the main entry.
            $queue_item->building = 0;
            $queue_item->canceled = 1;

            $queue_item->save();

            // Check if requirements for all other items in the queue are still met.
            // So e.g. if user cancels build order for metal mine
            // level 5 then any other already queued build orders for lvl 6,7,8 etc.
            // will also be canceled. Same applies to building requirements,
            // if user cancels build order for robotics factory which is requirement
            // for shipyard then shipyard will also be canceled.
            // Requirements are checked only for building queue objects as
            // unit queue objects cannot be canceled.
            $this->cancelItemMissingRequirements($planet);

            $research_queue = resolve(ResearchQueueService::class);
            $research_queue->cancelItemMissingRequirements($planet->getPlayer(), $planet);

            // Set the next queue item to start (if applicable)
            $this->start($planet);
        }
    }

    /**
     * Get is object in building queue
     *
     * @return bool
     */
    public function objectInBuildingQueue(PlanetService $planet, string $machine_name, int $level): bool
    {
        $queue_items = $this->retrieveQueueItems($planet);

        foreach ($queue_items as $item) {
            $object = ObjectService::getObjectById($item->object_id);

            if ($object->machine_name === $machine_name && $item->object_level_target === $level) {
                return true;
            }
        }

        return false;
    }

    /**
     * Cancel first building queue item missing requirements.
     * This function will be called recursively when it cancels the item.
     *
     * @return void
     */
    public function cancelItemMissingRequirements(PlanetService $planet): void
    {
        $build_queue_items = $this->retrieveQueueItems($planet);

        foreach ($build_queue_items as $build_queue_item) {
            $object = ObjectService::getObjectById($build_queue_item->object_id);

            if (!ObjectService::objectRequirementsMetWithQueue($object->machine_name, $build_queue_item->object_level_target, $planet)) {
                $this->cancel($planet, $build_queue_item->id, $object->id);
                break;
            }
        }
    }
}
