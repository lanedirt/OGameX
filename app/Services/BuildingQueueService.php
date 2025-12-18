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
        // For items not yet started (time_start = 0), order by ID to maintain queue order
        return BuildingQueue::where([
            ['planet_id', $planet->getPlanetId()],
            ['processed', 0],
            ['canceled', 0],
        ])
            ->orderBy('id', 'asc') // Primary sort by ID (insertion order)
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

        // Calculate the level after all queue items (upgrades and downgrades) complete
        // This is the level that the building will be at when this new upgrade starts
        $level_after_queue = $current_level;
        $queue_items = $this->retrieveQueueItems($planet);
        foreach ($queue_items as $item) {
            $item_object = ObjectService::getObjectById($item->object_id);
            if ($item_object->machine_name === $building->machine_name) {
                // Each item sets the level to its target (simulating queue execution)
                $level_after_queue = $item->object_level_target;
            }
        }

        // Next upgrade level should be level_after_queue + 1
        $next_level = $level_after_queue + 1;

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
        $queue->is_downgrade = false; // Explicitly set to false for upgrades

        // Save the new queue item
        $queue->save();

        // Set the new queue item to start (if applicable)
        $this->start($planet);
    }

    /**
     * Add a building downgrade to the building queue for the current planet.
     *
     * @param PlanetService $planet
     * @param int $building_id
     * @throws Exception
     */
    public function addDowngrade(PlanetService $planet, int $building_id): void
    {
        $build_queue = $this->retrieveQueue($planet);

        // Max amount of buildings that can be in the queue in a given time.
        if ($build_queue->isQueueFull()) {
            throw new Exception('Maximum number of items already in queue.');
        }

        // Get the building object
        $building = ObjectService::getObjectById($building_id);

        // Only buildings and stations can be downgraded
        if ($building->type !== \OGame\GameObjects\Models\Enums\GameObjectType::Building &&
            $building->type !== \OGame\GameObjects\Models\Enums\GameObjectType::Station) {
            throw new Exception('This object cannot be downgraded.');
        }

        $current_level = $planet->getObjectLevel($building->machine_name);

        // Cannot downgrade if already at level 0
        if ($current_level <= 0) {
            throw new Exception('Cannot downgrade building at level 0.');
        }

        // Calculate the level after all queue items (upgrades and downgrades) complete
        // This is the level that the building will be at when this new downgrade starts
        // We need to simulate queue execution to get the final level
        $level_after_queue = $current_level;
        $queue_items = $this->retrieveQueueItems($planet);
        foreach ($queue_items as $item) {
            $item_object = ObjectService::getObjectById($item->object_id);
            if ($item_object->machine_name === $building->machine_name) {
                // Simulate queue execution: each item updates the level to its target
                // For upgrades: level increases to target
                // For downgrades: level decreases to target
                $level_after_queue = $item->object_level_target;
            }
        }

        // Check if building can be downgraded (no dependencies)
        // Note: We check based on level_after_queue, not current_level
        if (!ObjectService::canDowngradeBuilding($building->machine_name, $planet)) {
            throw new Exception('Cannot downgrade building: other buildings or research depend on this level.');
        }

        // Check if Research Lab is being downgraded while research is in progress
        if ($building->machine_name === 'research_lab' && $planet->getPlayer()->isResearching()) {
            throw new Exception('Cannot downgrade Research Lab while research is in progress.');
        }

        // Check if Shipyard is being downgraded while ships/defense are being built
        if ($building->machine_name === 'shipyard' && $planet->getPlayer()->isBuildingShipsOrDefense()) {
            throw new Exception('Cannot downgrade Shipyard while ships or defense are being built.');
        }

        // Cannot downgrade if level_after_queue is already 0
        if ($level_after_queue <= 0) {
            throw new Exception('Cannot downgrade building: it will already be at level 0 after queue completes.');
        }

        // Get downgrade cost based on level_after_queue (after all queue items complete)
        $downgrade_price = ObjectService::getObjectDowngradePrice($building->machine_name, $planet, $level_after_queue);

        // Check if planet has enough resources
        if (!$planet->hasResources($downgrade_price)) {
            throw new Exception('Not enough resources to downgrade this building.');
        }

        // Create queue item for downgrade
        // Target level should be level_after_queue - 1 (after all queue items complete)
        $queue = new BuildingQueue();
        $queue->planet_id = $planet->getPlanetId();
        $queue->object_id = $building->id;
        $queue->object_level_target = $level_after_queue - 1;
        $queue->is_downgrade = true;

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

            $is_downgrade = (bool)($item['is_downgrade'] ?? false);
            $viewModel = new BuildingQueueViewModel(
                $item['id'],
                $object,
                $time_countdown,
                $item['time_end'] - $item['time_start'],
                $item['building'],
                $item['object_level_target'],
                $is_downgrade,
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
            $is_downgrade = $queue_item->is_downgrade ?? false;

            // Get price and build time based on whether it's an upgrade or downgrade
            if ($is_downgrade) {
                $price = ObjectService::getObjectDowngradePrice($object->machine_name, $planet);
                $build_time = $planet->getBuildingDowngradeTime($object->machine_name);
            } else {
                $price = ObjectService::getObjectPrice($object->machine_name, $planet);
                $build_time = $planet->getBuildingConstructionTime($object->machine_name);
            }

            // Only start the queue item if there are no other queue items building
            // for this planet.
            $current_queue = $this->retrieveQueue($planet);
            $currently_building = $current_queue->getCurrentlyBuildingFromQueue();
            if (!empty($currently_building)) {
                // There already is something else building, don't start a new one.
                break;
            }

            $current_level = $planet->getObjectLevel($object->machine_name);

            // Sanity check: validate target level
            if ($is_downgrade) {
                // For downgrade: target should be current_level - 1
                // If there was an upgrade before this downgrade in queue,
                // the current_level might have changed, so we always recalculate the target
                $expected_target = $current_level - 1;

                // Always update target level to match current level at start time
                // This handles the case where upgrades completed before this downgrade
                if ($queue_item->object_level_target != $expected_target) {
                    $queue_item->object_level_target = $expected_target;
                    $queue_item->save();
                }

                // Ensure target is valid (cannot downgrade below 0)
                if ($expected_target < 0) {
                    $this->cancel($planet, $queue_item->id, $queue_item->object_id);
                    continue;
                }

                // Check if building can still be downgraded (dependencies might have changed)
                if (!ObjectService::canDowngradeBuilding($object->machine_name, $planet)) {
                    $this->cancel($planet, $queue_item->id, $queue_item->object_id);
                    continue;
                }

                // Check if Research Lab is being downgraded while research is in progress
                if ($object->machine_name === 'research_lab' && $planet->getPlayer()->isResearching()) {
                    $this->cancel($planet, $queue_item->id, $queue_item->object_id);
                    continue;
                }

                // Check if Shipyard is being downgraded while ships/defense are being built
                if ($object->machine_name === 'shipyard' && $planet->getPlayer()->isBuildingShipsOrDefense()) {
                    $this->cancel($planet, $queue_item->id, $queue_item->object_id);
                    continue;
                }
            } else {
                // For upgrade: target should be current_level + 1
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

                // Sanity check: check if the building requirements are still met. If not,
                // then cancel build request.
                if (!ObjectService::objectRequirementsWithLevelsMet($object->machine_name, $queue_item->object_level_target, $planet)) {
                    $this->cancel($planet, $queue_item->id, $queue_item->object_id);
                    continue;
                }
            }

            // Sanity check: check if the planet has enough resources. If not,
            // then cancel build request.
            if (!$planet->hasResources($price)) {
                // Error, cancel build queue item.
                $this->cancel($planet, $queue_item->id, $queue_item->object_id);
                continue;
            }

            // All OK, deduct resources and start building/downgrade process.
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

    /**
     * Get a queue item by ID and planet ID.
     *
     * @param int $queueItemId The queue item ID
     * @param int $planetId The planet ID
     * @return BuildingQueue|null The queue item or null if not found
     */
    public function getQueueItemById(int $queueItemId, int $planetId): BuildingQueue|null
    {
        return BuildingQueue::where('id', $queueItemId)
            ->where('planet_id', $planetId)
            ->where('processed', 0)
            ->where('canceled', 0)
            ->first();
    }

    /**
     * Update the time_end field of a queue item.
     *
     * @param int $queueItemId The queue item ID
     * @param int $planetId The planet ID
     * @param int $newTimeEnd The new time_end value (Unix timestamp)
     * @return void
     */
    public function updateTimeEnd(int $queueItemId, int $planetId, int $newTimeEnd): void
    {
        BuildingQueue::where('id', $queueItemId)
            ->where('planet_id', $planetId)
            ->update(['time_end' => $newTimeEnd]);
    }
}
