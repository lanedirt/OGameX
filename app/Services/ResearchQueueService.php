<?php

namespace OGame\Services;

use Exception;
use Illuminate\Support\Carbon;

/**
 * Class ResearchQueueService.
 *
 * ResearchQueueService object.
 *
 * @package OGame\Services
 */
class ResearchQueueService
{
    /**
     * The planet object from the model.
     *
     * @var
     */
    protected $queue_item;

    /**
     * Information about objects.
     *
     * @var ObjectService
     */
    protected $objects;

    /**
     * The queue model where this class should get its data from.
     *
     * @var
     */
    protected $model;

    /**
     * BuildingQueue constructor.
     */
    public function __construct(ObjectService $objects)
    {
        $this->objects = $objects;

        $model_name = 'OGame\ResearchQueue';
        $this->model = new $model_name();
    }

    /**
     * Retrieve queued items that are not being built yet.
     *
     * @return mixed
     *  Array when an item exists. False if it does not.
     */
    public function retrieveQueuedFromQueue($queue_items)
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
     */
    public function retrieveFinished($planet_id)
    {
        // Fetch queue items from model
        $queue_items = $this->model->where([
            ['planet_id', $planet_id],
            ['time_end', '<=', Carbon::now()->timestamp],
            ['building', 1],
            ['processed', 0],
            ['canceled', 0],
        ])
            ->orderBy('time_start', 'asc')
            ->get();

        return $queue_items;
    }

    /**
     * Retrieve all build queue items that already should be finished for a given user.
     */
    public function retrieveFinishedForUser(PlayerService $player)
    {
        // Fetch queue items from model
        $queue_items = $this->model
            ->join('planets', 'research_queues.planet_id', '=', 'planets.id')
            ->join('users', 'planets.user_id', '=', 'users.id')
            ->where([
                ['users.id', $player->getId()],
                ['research_queues.time_end', '<=', Carbon::now()->timestamp],
                ['research_queues.building', 1],
                ['research_queues.processed', 0],
                ['research_queues.canceled', 0],
            ])
            ->select('research_queues.*')
            ->orderBy('research_queues.time_start', 'asc')
            ->get();

        return $queue_items;
    }

    /**
     * Add a building to the building queue for the current planet.
     */
    public function add(PlayerService $player, PlanetService $planet, $building_id)
    {
        $build_queue = $this->retrieveQueue($planet);
        $build_queue = $this->enrich($build_queue);

        // Max amount of buildings that can be in the queue in a given time.
        $max_build_queue_count = 4; //@TODO: refactor into global / constant?
        if (count($build_queue) >= $max_build_queue_count) {
            // Max amount of build queue items already exist, throw exception.
            throw new Exception('Maximum number of items already in queue.');
        }

        // @TODO: add checks that current logged in user is owner of planet
        // and is able to add this object to the building queue.
        $current_level = $player->getResearchLevel($building_id);

        // Check to see how many other items of this building there are already
        // in the queue, because if so then the level needs to be higher than that.
        $amount = $this->activeBuildingQueueItemCount($player, $building_id);
        $next_level = $current_level + $amount + 1;

        $queue = new $this->model;
        $queue->planet_id = $planet->getPlanetId();
        $queue->object_id = $building_id;
        $queue->object_level_target = $next_level;

        // Save the new queue item
        $queue->save();

        // Set the new queue item to start (if applicable)
        $this->start($player);
    }

    /**
     * Retrieve current building build queue for a planet.
     */
    public function retrieveQueue(PlanetService $planet)
    {
        // Fetch queue items from model
        $queue_items = $this->model
            ->join('planets', 'research_queues.planet_id', '=', 'planets.id')
            ->join('users', 'planets.user_id', '=', 'users.id')
            ->where([
                ['users.id', $planet->getPlayer()->getId()],
                ['research_queues.processed', 0],
                ['research_queues.canceled', 0],
            ])
            ->select('research_queues.*')
            ->orderBy('research_queues.time_start', 'asc')
            ->get();

        return $queue_items;
    }

    /**
     * Enriches one or more queue_items to prepare it for rendering.
     *
     * @param $queue_items
     *  Single queue_item or array of queue_items.
     *
     * @return array
     */
    public function enrich($queue_items)
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
            $object = $this->objects->getResearchObjects($item->object_id);

            $time_countdown = $item->time_end - Carbon::now()->timestamp;
            if ($time_countdown < 0) {
                $time_countdown = 0;
            }

            $return[] = [
                'id' => $item->id,
                'object' => [
                    'id' => $object['id'],
                    'title' => $object['title'],
                    'level_target' => $item->object_level_target,
                    'assets' => $object['assets'],
                ],
                'time_countdown' => $time_countdown,
                'time_total' => $item->time_end - $item->time_start,
            ];
        }

        if ($return_type == 'single') {
            return $return[0];
        } elseif ($return_type == 'array') {
            return $return;
        }

        return $return;
    }

    /**
     * Get the amount of already existing queue items for a particular
     * building.
     */
    public function activeBuildingQueueItemCount(PlayerService $player, $building_id)
    {
        // Fetch queue items from model
        $count = $this->model
            ->join('planets', 'research_queues.planet_id', '=', 'planets.id')
            ->join('users', 'planets.user_id', '=', 'users.id')
            ->where([
                ['users.id', $player->getId()],
                ['research_queues.object_id', $building_id],
                ['research_queues.processed', 0],
                ['research_queues.canceled', 0],
            ])
            ->count();

        return $count;
    }

    /**
     * Start building the next item in the queue (if available).
     *
     * This actually starts the building process and deducts the resources
     * from the planet. If there are not enough resources the build attempt
     * will fail.
     *
     * @param $planet_id
     *  The planet ID for which to start the next item in the queue for.
     *
     * @param $time_start
     *  Optional parameter to indicate when the new item should start, this
     *  is used for when a few build queue items are finished at the exact
     *  same time, e.g. when a user closes its session and logs back in
     *  after a while.
     */
    public function start(PlayerService $player, $time_start = false)
    {
        $queue_items = $this->model
            ->join('planets', 'research_queues.planet_id', '=', 'planets.id')
            ->join('users', 'planets.user_id', '=', 'users.id')
            ->where([
                ['users.id', $player->getId()],
                ['building', 0],
                ['processed', 0],
                ['canceled', 0],
            ])
            ->select('research_queues.*')
            ->get();

        foreach ($queue_items as $queue_item) {
            if (empty($queue_item)) {
                continue;
            }

            $planet = $player->planets->childPlanetById($queue_item->planet_id);

            // See if the planet has enough resources for this build attempt.
            $price = $this->objects->getObjectPrice($queue_item->object_id, $planet);
            // TODO: implement technology research separate calculation
            $build_time = $planet->getBuildingConstructionTime($queue_item->object_id);


            // Only start the queue item if there are no other queue items building
            // for this planet.
            $build_queue = $this->retrieveQueue($planet);
            $currently_building = $this->retrieveCurrentlyBuildingFromQueue($build_queue);

            if (!empty($currently_building)) {
                // There already is something else building, don't start a new one.
                break;
            }

            // Sanity check: check if the target level as stored in the database
            // is 1 higher than the current level. If not, then it means something
            // is wrong.
            $current_level = $player->getResearchLevel($queue_item->object_id);
            if ($queue_item->object_level_target != ($current_level + 1)) {
                // Error, cancel build queue item.
                $this->cancel($player, $planet, $queue_item->id, $queue_item->object_id);

                continue;
            }


            // Sanity check: check if the planet has enough resources. If not,
            // then cancel build request.
            if (!$planet->hasResources($price)) {
                // Error, cancel build queue item.
                $this->cancel($player, $planet, $queue_item->id, $queue_item->object_id);

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
            $queue_item->metal = $price['metal'];
            $queue_item->crystal = $price['crystal'];
            $queue_item->deuterium = $price['deuterium'];
            $queue_item->save();

            // If the calculated end time is lower than the current time,
            // we force that the planet is updated again which will grant
            // the building immediately without having to wait for a refresh.
            if ($queue_item->time_end_ < Carbon::now()->timestamp) {
                // @TODO: refactor the planet update logic so this method
                // can call only the part it needs directly without causing
                // a major overhead.
                $planet->update();
            }
        }
    }

    /**
     * Retrieve the item that is currently being build (if any).
     *
     * @return mixed
     *  Array when an item exists. False if it does not.
     */
    public function retrieveCurrentlyBuildingFromQueue($queue_items)
    {
        foreach ($queue_items as $key => $record) {
            if ($record['building'] == 1) {
                return $record;
            }
        }

        return false;
    }

    /**
     * Cancels an active building queue record.
     */
    public function cancel(PlayerService $player, PlanetService $planet, $building_queue_id, $building_id)
    {
        // @TODO: add user owner verify checks.
        $queue_item = $this->model->where([
            ['id', $building_queue_id],
            ['planet_id', $planet->getPlanetId()],
            ['object_id', $building_id],
            ['processed', 0],
            //['time_end', '>', Carbon::now()->timestamp], //@TODO: add back in time_end when building processing logic works.
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
                //['time_end', '>', Carbon::now()->timestamp], //@TODO: add back in time_end when building processing logic works.
            ])->get();

            // Add canceled flag to all entries with a higher level (if any).
            foreach ($queue_items_higher_level as $queue_item_higher_level) {
                $queue_item_higher_level->building = 0;
                $queue_item_higher_level->canceled = 1;

                $queue_item_higher_level->save();
            }

            // Give back resources if the current entry was already building.
            if ($queue_item->building == 1) {
                $planet->addResources([
                    'metal' => $queue_item->metal,
                    'crystal' => $queue_item->crystal,
                    'deuterium' => $queue_item->deuterium,
                ]);
            }

            // Add canceled flag to the main entry.
            $queue_item->building = 0;
            $queue_item->canceled = 1;

            $queue_item->save();

            // Set the next queue item to start (if applicable)
            $this->start($player);
        }
    }
}
