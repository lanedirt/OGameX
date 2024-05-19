<?php

namespace OGame\Services;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use OGame\Models\UnitQueue;
use OGame\ViewModels\Queue\UnitQueueListViewModel;
use OGame\ViewModels\Queue\UnitQueueViewModel;
use OGame\ViewModels\QueueListViewModel;

/**
 * Class UnitQueueService.
 *
 * UnitQueueService object.
 *
 * @package OGame\Services
 */
class UnitQueueService
{
    /**
     * Information about objects.
     *
     * @var ObjectService
     */
    private ObjectService $objects;

    /**
     * The queue model where this class should get its data from.
     *
     * @var UnitQueue
     */
    private UnitQueue $model;

    /**
     * BuildingQueue constructor.
     */
    public function __construct(ObjectService $objects)
    {
        $this->objects = $objects;

        $this->model = new UnitQueue();
    }

    /**
     * Retrieve current building build queue for a planet.
     *
     * @param PlanetService $planet
     * @return UnitQueueListViewModel
     * @throws Exception
     */
    public function retrieveQueue(PlanetService $planet): UnitQueueListViewModel
    {
        // Fetch queue items from model
        $queue_items = $this->model->where([
            ['planet_id', $planet->getPlanetId()],
            ['processed', 0],
        ])
            ->orderBy('time_start', 'asc')
            ->get();

        // Convert to ViewModel array
        $list = array();
        foreach ($queue_items as $item) {
            $object = $this->objects->getObjectById($item['object_id']);

            $time_countdown = $item->time_end - (int)Carbon::now()->timestamp;
            if ($time_countdown < 0) {
                $time_countdown = 0;
            }

            // Calculate when next unit will be processed and given.
            $time_per_unit = ($item->time_end - $item->time_start) / $item->object_amount;

            // Get timestamp where a unit has been presented lastly.
            $last_update = $item->time_progress;
            if ($last_update < $item->time_start) {
                $last_update = $item->time_start;
            }
            $last_update_diff = (int)Carbon::now()->timestamp - $last_update;
            $time_countdown_next_single = $time_per_unit - $last_update_diff;

            $viewModel = new UnitQueueViewModel(
                $item['id'],
                $object,
                $time_countdown,
                $item['time_end'] - $item['time_start'],
                $item['object_amount'],
                $item['object_amount'] - $item['object_amount_progress'],
                $time_countdown_next_single
            );

            $list[] = $viewModel;
        }

        // Create QueueListViewModel
        return new UnitQueueListViewModel($list);
    }

    /**
     * Retrieve current building build queue for a planet.
     *
     * @param int $planet_id
     * @return Collection<UnitQueue>
     */
    public function retrieveBuilding(int $planet_id): Collection
    {
        // Fetch queue items from model
        return $this->model->where([
            ['planet_id', $planet_id],
            ['time_start', '<=', Carbon::now()->timestamp],
            ['processed', 0],
        ])
            ->orderBy('time_start', 'asc')
            ->get();
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
        ])
            ->count();
    }

    /**
     * Add a building to the building queue for the current planet.
     *
     * @param PlanetService $planet
     * @param int $object_id
     * @param int $requested_build_amount
     *
     * @return void
     * @throws Exception
     */
    public function add(PlanetService $planet, int $object_id, int $requested_build_amount): void
    {
        // @TODO: add checks that current logged in user is owner of planet
        // and is able to add this object to the building queue.

        // Only allow positive numbers.
        if ($requested_build_amount < 1) {
            return;
        }

        $object = $this->objects->getUnitObjectById($object_id);

        // Sanity check: check if the planet has enough resources to build
        // the amount requested. If not, then adjust the ordered amount.
        // E.g. if a player orders 100 units but can only afford 60 units,
        // 60 units will be added to the queue and resources will be deducted.
        $max_build_amount = $this->objects->getObjectMaxBuildAmount($object->machine_name, $planet);
        if ($requested_build_amount > $max_build_amount) {
            $requested_build_amount = $max_build_amount;
        }

        if ($requested_build_amount < 1) {
            // If the requested amount is less than 1, then we can't build
            // anything. So we stop here.
            return;
        }

        // Get price per unit
        $price_per_unit = $this->objects->getObjectPrice($object->machine_name, $planet);
        $total_price = $price_per_unit->multiply($requested_build_amount);

        // @TODO: add abstraction and unittest to see if multiplication
        // of resource prices works correctly in unit build orders.

        // Calculate build time per unit
        // should be different from buildings.
        $build_time_unit = $planet->getUnitConstructionTime($object->machine_name);
        $build_time_total = $build_time_unit * $requested_build_amount;

        // Time this order will start
        $time_start = (int)Carbon::now()->timestamp;

        // If there are other orders already in the queue, use the highest
        // time_end as the start time of this order.
        $last_time_end = $this->retrieveQueueTimeEnd($planet);
        if ($last_time_end) {
            $time_start = $last_time_end;
        }

        $queue = new $this->model();
        $queue->planet_id = $planet->getPlanetId();
        $queue->object_id = $object_id;
        $queue->object_amount = $requested_build_amount;
        $queue->time_duration = $build_time_total;
        $queue->time_start = $time_start;
        $queue->time_end = $queue->time_start + $queue->time_duration;
        $queue->metal = (int)$total_price->metal->get();
        $queue->crystal = (int)$total_price->crystal->get();
        $queue->deuterium = (int)$total_price->deuterium->get();

        // All OK, deduct resources.
        $planet->deductResources($total_price);

        // Save the new queue item which will automatically start it.
        $queue->save();
    }

    /**
     * Retrieve the end time of any items that are already in the queue.
     *
     * If there are no items in the queue, FALSE will be returned.
     *
     * @param PlanetService $planet
     * @return int
     */
    public function retrieveQueueTimeEnd(PlanetService $planet): int
    {
        // Fetch queue items from model
        $queue_item = $this->model->where([
            ['planet_id', $planet->getPlanetId()],
            ['processed', 0],
        ])
            ->orderBy('time_end', 'desc')
            ->first();

        if ($queue_item) {
            return $queue_item->time_end;
        }

        return 0;
    }
}
