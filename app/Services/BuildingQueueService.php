<?php

namespace OGame\Services;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use OGame\Models\BuildingQueue;
use OGame\Models\Resources;
use OGame\Services\Objects\ObjectService;

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
     * The planet object from the model.
     *
     * @var BuildingQueue
     */
    protected BuildingQueue $queue_item;

    /**
     * Information about objects.
     *
     * @var ObjectService
     */
    protected ObjectService $objects;

    /**
     * The queue model where this class should get its data from.
     *
     * @var BuildingQueue $model
     */
    protected BuildingQueue $model;

    /**
     * BuildingQueue constructor.
     *
     * @param ObjectService $objects
     */
    public function __construct(ObjectService $objects)
    {
        $this->objects = $objects;

        $model_name = 'OGame\Models\BuildingQueue';
        $this->model = new $model_name();
    }

    /**
     * Retrieve queued items that are not being built yet.
     *
     * @param Collection $queue_items
     *
     * @return Collection
     *  Collection when an item exists. False if it does not.
     */
    public function retrieveQueuedFromQueue(Collection $queue_items) : Collection
    {
        foreach ($queue_items as $key => $record) {
            if ($record['building'] == 1) {
                unset($queue_items[$key]);
            }
        }

        return $queue_items;
    }

    /**
     * Retrieve all build queue items that already should be finished for a planet.
     *
     * @param int $planet_id
     * @return Collection<BuildingQueue>
     */
    public function retrieveFinished(int $planet_id) : Collection
    {
        // Fetch queue items from model
        return $this->model->where([
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
     * Add a building to the building queue for the current planet.
     *
     * @param PlanetService $planet
     * @param int $building_id
     * @throws Exception
     */
    public function add(PlanetService $planet, int $building_id) : void
    {
        $build_queue = $this->retrieveQueue($planet);
        $build_queue = $this->enrich($build_queue);

        $building = $this->objects->getObjectById($building_id);

        // Max amount of buildings that can be in the queue in a given time.
        $max_build_queue_count = 4; //@TODO: refactor into global / constant?
        // TODO: refactor throw exception into a more user-friendly message.
        if (count($build_queue) >= $max_build_queue_count) {
            // Max amount of build queue items already exist, throw exception.
            throw new Exception('Maximum number of items already in queue.');
        }

        // Check if user satisifes requirements to build this object.
        // TODO: refactor throw exception into a more user-friendly message.
        $requirements_met = $this->objects->objectRequirementsMet($building->machine_name, $planet, $planet->getPlayer());
        if (!$requirements_met) {
            throw new Exception('Requirements not met to build this object.');
        }

        // @TODO: add checks that current logged in user is owner of planet
        // and is able to add this object to the building queue.
        $current_level = $planet->getObjectLevel($building->machine_name);

        // Check to see how many other items of this building there are already
        // in the queue, because if so then the level needs to be higher than that.
        $amount = $this->activeBuildingQueueItemCount($planet, $building->id);
        $next_level = $current_level + $amount + 1;

        $queue = new $this->model;
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
     * @return Collection<BuildingQueue>
     */
    public function retrieveQueue(PlanetService $planet) : Collection
    {
        // Fetch queue items from model
        return $this->model->where([
            ['planet_id', $planet->getPlanetId()],
            ['processed', 0],
            ['canceled', 0],
        ])
            ->orderBy('time_start', 'asc')
            ->get();
    }

    /**
     * Enriches one or more queue_items to prepare it for rendering.
     *
     * @param $queue_items
     *  Single queue_item or array of queue_items.
     *
     * @return array
     * @throws Exception
     */
    public function enrich($queue_items) : array
    {
        // Enrich information before we return it
        $return = array();

        if (empty($queue_items)) {
            return $return;
        }

        // Convert single queue_item result to an array because the logic
        // beneath expects an array.
        $return_type = 'array';
        if (!empty($queue_items->id)) {
            $return_type = 'single';
            $queue_items = array($queue_items);
        }

        foreach ($queue_items as $item) {
            $object = $this->objects->getObjectById($item->object_id);

            $time_countdown = $item->time_end - Carbon::now()->timestamp;
            if ($time_countdown < 0) {
                $time_countdown = 0;
            }

            $return[] = [
                'id' => $item->id,
                'object' => [
                    'id' => $object->id,
                    'title' => $object->title,
                    'level_target' => $item->object_level_target,
                    'assets' => $object->assets,
                ],
                'time_countdown' => $time_countdown,
                'time_total' => $item->time_end - $item->time_start,
            ];
        }

        if ($return_type == 'single') {
            return $return[0];
        }

        return $return;
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
        return $this->model->where([
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
        $queue_items = $this->model->where([
            ['planet_id', $planet->getPlanetId()],
            ['canceled', 0],
            ['processed', 0],
            ['building', 0],
        ])
            ->orderBy('id', 'asc')
            ->get();

        foreach ($queue_items as $queue_item) {
            $object = $this->objects->getObjectById($queue_item->object_id);

            // See if the planet has enough resources for this build attempt.
            $price = $this->objects->getObjectPrice($object->machine_name, $planet);
            $build_time = $planet->getBuildingConstructionTime($object->machine_name);

            // Only start the queue item if there are no other queue items building
            // for this planet.
            $current_queue = $this->retrieveQueue($planet);
            $currently_building = $this->retrieveCurrentlyBuildingFromQueue($current_queue);
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

            // Sanity check: check if the planet has enough resources. If not,
            // then cancel build request.
            if (!$planet->hasResources($price)) {
                // Error, cancel build queue item.
                $this->cancel($planet, $queue_item->id, $queue_item->object_id);

                continue;
            }

            // All OK, deduct resources and start building process.
            $planet->deductResources($price);

            if (!$time_start) {
                $time_start = Carbon::now()->timestamp;
            }

            $queue_item->time_duration = $build_time;
            $queue_item->time_start = $time_start;
            $queue_item->time_end = $queue_item->time_start + $queue_item->time_duration;
            $queue_item->building = 1;
            $queue_item->metal = (int)$price->metal->get();
            $queue_item->crystal = (int)$price->crystal->get();
            $queue_item->deuterium = (int)$price->deuterium->get();
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
     * Retrieve the item that is currently being build (if any).
     *
     * @return ?Model
     *  Array when an item exists. False if it does not.
     */
    public function retrieveCurrentlyBuildingFromQueue(Collection $queue_items) : ?Model
    {
        foreach ($queue_items as $record) {
            if ($record['building'] == 1) {
                return $record;
            }
        }

        return null;
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
        $queue_item = $this->model->where([
            ['id', $building_queue_id],
            ['planet_id', $planet->getPlanetId()],
            ['object_id', $building_id],
            ['processed', 0],
            ['time_end', '>', Carbon::now()->timestamp],
        ])->first();

        // If object is found: add canceled flag.
        if ($queue_item) {
            // Gets all building queue records of this target level and all that
            // come after it. So e.g. if user cancels build order for metal mine
            // level 5 then any other already queued build orders for lvl 6,7,8 etc.
            // will also be canceled.
            $queue_items_higher_level = $this->model->where([
                ['planet_id', $planet->getPlanetId()],
                ['object_id', $building_id],
                ['object_level_target', '>', $queue_item->object_level_target],
                ['processed', 0],
            ])->get();

            // Add canceled flag to all entries with a higher level (if any).
            foreach ($queue_items_higher_level as $queue_item_higher_level) {
                $queue_item_higher_level->building = 0;
                $queue_item_higher_level->canceled = 1;

                $queue_item_higher_level->save();
            }

            // Give back resources if the current entry was already building.
            if ($queue_item->building == 1) {
                $planet->addResources(new Resources($queue_item->metal, $queue_item->crystal, $queue_item->deuterium, 0));
            }

            // Add canceled flag to the main entry.
            $queue_item->building = 0;
            $queue_item->canceled = 1;

            $queue_item->save();

            // Set the next queue item to start (if applicable)
            $this->start($planet);
        }
    }
}
