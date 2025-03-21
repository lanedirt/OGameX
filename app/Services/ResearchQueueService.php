<?php

namespace OGame\Services;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use OGame\Models\ResearchQueue;
use OGame\Models\Resources;
use OGame\ViewModels\Queue\ResearchQueueListViewModel;
use OGame\ViewModels\Queue\ResearchQueueViewModel;

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
     * The queue model where this class should get its data from.
     *
     * @var ResearchQueue
     */
    private ResearchQueue $model;

    /**
     * ResearchQueueService constructor.
     */
    public function __construct()
    {
        $this->model = new ResearchQueue();
    }

    /**
     * Retrieve queued items that are not being built yet.
     *
     * @return \Illuminate\Support\Collection
     *  Array when an item exists. False if it does not.
     */
    public function retrieveQueuedFromQueue(\Illuminate\Support\Collection $queue_items): \Illuminate\Support\Collection
    {
        foreach ($queue_items as $key => $record) {
            if ($record['building'] === 1) {
                unset($queue_items[$key]);
            }
        }

        return $queue_items;
    }

    /**
     * Retrieve all build queue items that already should be finished for a planet.
     *
     * @param int $planet_id
     * The planet ID for which to retrieve the finished items.
     *
     * @return Collection<int, ResearchQueue>
     */
    public function retrieveFinished(int $planet_id): Collection
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
     * Retrieve all build queue items that already should be finished for a given user.
     */
    public function retrieveFinishedForUser(PlayerService $player): \Illuminate\Support\Collection
    {
        // Fetch queue items from model
        return $this->model
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
    }

    /**
     * Add a research object to the research queue for the current planet.
     *
     * @param PlayerService $player
     * @param PlanetService $planet
     * @param int $research_object_id
     * @return void
     * @throws Exception
     */
    public function add(PlayerService $player, PlanetService $planet, int $research_object_id): void
    {
        $research_queue = $this->retrieveQueue($planet);

        // Max amount of research items that can be in the queue at a given time.
        // TODO: refactor throw exception into a more user-friendly message.
        if ($research_queue->isQueueFull()) {
            // Max amount of research queue items already exist, throw exception.
            throw new Exception('Maximum number of items already in queue.');
        }

        $object = ObjectService::getResearchObjectById($research_object_id);

        // @TODO: add checks that current logged in user is owner of planet
        // and is able to add this object to the research queue.
        $current_level = $player->getResearchLevel($object->machine_name);

        // Check to see how many other items of this technology there are already
        // in the queue, because if so then the level needs to be higher than that.
        $amount = $this->activeResearchQueueItemCount($player, $research_object_id);
        $next_level = $current_level + $amount + 1;

        // Check if user satisifes requirements to research this object.
        // TODO: refactor throw exception into a more user-friendly message.
        $requirements_met = ObjectService::objectRequirementsMetWithQueue($object->machine_name, $next_level, $planet);
        if (!$requirements_met) {
            throw new Exception('Requirements not met to build this object.');
        }

        $queue = new $this->model();
        $queue->planet_id = $planet->getPlanetId();
        $queue->object_id = $research_object_id;
        $queue->object_level_target = $next_level;

        // Save the new queue item
        $queue->save();

        // Set the new queue item to start (if applicable)
        $this->start($player);
    }

    /**
     * Retrieve current research queue for a player.
     *
     * @param PlanetService $planet
     * @return ResearchQueueListViewModel
     * @throws Exception
     */
    public function retrieveQueue(PlanetService $planet): ResearchQueueListViewModel
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

        // Convert to ViewModel array
        $list = [];
        foreach ($queue_items as $item) {
            $object = ObjectService::getResearchObjectById($item->object_id);
            $planetService = $planet->getPlayer()->planets->getById($item['planet_id']);
            $time_countdown = $item->time_end - (int)Carbon::now()->timestamp;
            if ($time_countdown < 0) {
                $time_countdown = 0;
            }

            $viewModel = new ResearchQueueViewModel(
                $item['id'],
                $object,
                $time_countdown,
                $item['time_end'] - $item['time_start'],
                $planetService,
                $item['building'],
                $item['object_level_target'],
            );

            $list[] = $viewModel;
        }

        return new ResearchQueueListViewModel($list);
    }

    /**
     * Get the amount of player active research queue items.
     *
     * @param PlayerService $player
     * @param int $tech_id
     * @return int
     */
    public function activeResearchQueueItemCount(PlayerService $player, int $tech_id = 0): int
    {
        // Fetch queue items from model
        return $this->model::query()
            ->join('planets', 'research_queues.planet_id', '=', 'planets.id')
            ->join('users', 'planets.user_id', '=', 'users.id')
            ->where([
                ['users.id', $player->getId()],
                ['research_queues.processed', 0],
                ['research_queues.canceled', 0],
            ])
            ->when($tech_id, function ($q) use ($tech_id) {
                return $q->where('research_queues.object_id', '=', $tech_id);
            })
            ->count();
    }

    /**
     * Start researching the next item in the queue (if available).
     *
     * This actually starts the research process and deducts the resources
     * from the planet. If there are not enough resources the build attempt
     * will fail.
     *
     * @param PlayerService $player
     *
     * @param int $time_start
     *  Optional parameter to indicate when the new item should start, this
     *  is used for when a few research queue items are finished at the exact
     *  same time, e.g. when a user closes its session and logs back in
     *  after a while.
     * @return void
     * @throws Exception
     */
    public function start(PlayerService $player, int $time_start = 0): void
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
            $planet = $player->planets->getById($queue_item->planet_id);
            $object = ObjectService::getResearchObjectById($queue_item->object_id);

            // See if the planet has enough resources for this research attempt.
            $price = ObjectService::getObjectPrice($object->machine_name, $planet);
            $research_time = $player->planets->current()->getTechnologyResearchTime($object->machine_name);

            // Only start the queue item if there are no other queue items researching
            // for this planet.
            $research_queue = $this->retrieveQueue($planet);
            $currently_researching = $research_queue->getCurrentlyBuildingFromQueue();

            if ($currently_researching !== null) {
                // There already is something else researching, don't start a new one.
                break;
            }

            // Sanity check: check if the Research Lab is being upgraded
            if ($player->isBuildingObject('research_lab')) {
                // Error, cancel research queue item.
                $this->cancel($player, $queue_item->id, $queue_item->object_id);

                continue;
            }

            // Sanity check: check if the target level as stored in the database
            // is 1 higher than the current level. If not, then it means something
            // is wrong.
            $current_level = $player->getResearchLevel($object->machine_name);
            if ($queue_item->object_level_target !== ($current_level + 1)) {
                // Error, cancel research queue item.
                $this->cancel($player, $queue_item->id, $queue_item->object_id);

                continue;
            }

            // Sanity check: check if the planet has enough resources. If not,
            // then cancel research request.
            if (!$planet->hasResources($price)) {
                // Error, cancel research queue item.
                $this->cancel($player, $queue_item->id, $queue_item->object_id);

                continue;
            }

            // Sanity check: check if the researching requirements are still met. If not,
            // then cancel research request.
            if (!ObjectService::objectRequirementsWithLevelsMet($object->machine_name, $queue_item->object_level_target, $planet)) {
                $this->cancel($player, $queue_item->id, $queue_item->object_id);

                continue;
            }

            // All OK, deduct resources and start researching process.
            $planet->deductResources($price);

            if (!$time_start) {
                $time_start = (int)Carbon::now()->timestamp;
            }

            $queue_item->time_duration = (int)$research_time;
            $queue_item->time_start = $time_start;
            $queue_item->time_end = $queue_item->time_start + $queue_item->time_duration;
            $queue_item->building = 1;
            $queue_item->metal = $price->metal->get();
            $queue_item->crystal = $price->crystal->get();
            $queue_item->deuterium = $price->deuterium->get();
            $queue_item->save();

            // If the calculated end time is lower than the current time,
            // we force that the planet is updated again which will grant
            // the research immediately without having to wait for a refresh.
            if ($queue_item->time_end < Carbon::now()->timestamp) {
                $player->updateResearchQueue();
            }
        }
    }

    /**
     * Cancels an active research queue record.
     *
     * @param PlayerService $player
     * @param int $research_queue_id
     * @param int $research_id
     * @throws Exception
     */
    public function cancel(PlayerService $player, int $research_queue_id, int $research_id): void
    {
        $queue_item = $this->model
            ->join('planets', 'research_queues.planet_id', '=', 'planets.id')
            ->join('users', 'planets.user_id', '=', 'users.id')
            ->where([
                ['users.id', $player->getId()],
                ['research_queues.id', $research_queue_id],
                ['object_id', $research_id],
                ['processed', 0],
                ['canceled', 0],
            ])
            ->select('research_queues.*')
            ->first();

        // If object is found: add canceled flag.
        if ($queue_item) {
            // Typecast to a new object to avoid issues with the model.
            $queue_item = $queue_item instanceof ResearchQueue ? $queue_item : new ResearchQueue($queue_item->getAttributes());
            $planet = $player->planets->getById($queue_item->planet_id);

            // Gets all research queue records of this target level and all that
            // come after it. So e.g. if user cancels build order for metal mine
            // level 5 then any other already queued build orders for lvl 6,7,8 etc.
            // will also be canceled. Same applies to research requirements,
            // if user cancels research order for Energy Technology which is requirement
            // for Impulse Drive then Impulse Drive will also be canceled.

            // Give back resources if the current entry was already building.
            if ($queue_item->building === 1) {
                $planet->addResources(new Resources($queue_item->metal, $queue_item->crystal, $queue_item->deuterium, 0));
            }

            // Add canceled flag to the main entry.
            $queue_item->building = 0;
            $queue_item->canceled = 1;

            $queue_item->save();

            $this->cancelItemMissingRequirements($player, $planet);

            $build_queue = resolve(BuildingQueueService::class);
            $build_queue->cancelItemMissingRequirements($planet);

            // Set the next queue item to start (if applicable)
            $this->start($player);
        }
    }

    /**
     * Get is object in research queue
     *
     * @param PlayerService $player
     * @param string $machine_name
     * @param int $level
     * @return bool
     */
    public function objectInResearchQueue(PlayerService $player, string $machine_name, int $level): bool
    {
        // Fetch queue items from model
        $queue_items =  $this->model
            ->join('planets', 'research_queues.planet_id', '=', 'planets.id')
            ->join('users', 'planets.user_id', '=', 'users.id')
            ->where([
                ['users.id', $player->getId()],
                ['research_queues.canceled', 0],
                ['research_queues.processed', 0],
            ])
            ->select('research_queues.*')
            ->orderBy('research_queues.time_start', 'asc')
            ->get();

        foreach ($queue_items as $item) {
            $object = ObjectService::getObjectById($item->object_id);

            if ($object->machine_name === $machine_name && $item->object_level_target === $level) {
                return true;
            }
        }

        return false;
    }

    /**
     * Cancel first research queue item missing requirements.
     * This function will be called recursively when it cancels the item.
     *
     * @return void
     */
    public function cancelItemMissingRequirements(PlayerService $player, PlanetService $planet): void
    {
        // Fetch queue items from model
        $research_queue_items = $this->model
            ->join('planets', 'research_queues.planet_id', '=', 'planets.id')
            ->join('users', 'planets.user_id', '=', 'users.id')
            ->where([
                ['users.id', $player->getId()],
                ['research_queues.canceled', 0],
            ])
            ->select('research_queues.*')
            ->orderBy('research_queues.time_start', 'asc')
            ->get();

        foreach ($research_queue_items as $research_queue_item) {
            $object = ObjectService::getObjectById($research_queue_item->object_id);

            if (!ObjectService::objectRequirementsMetWithQueue($object->machine_name, $research_queue_item->object_level_target, $planet)) {
                $this->cancel($player, $research_queue_item->id, $object->id);
                break;
            }
        }
    }
}
