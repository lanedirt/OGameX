<?php

namespace OGame\Http\Controllers\Abstracts;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use OGame\Http\Controllers\Controller;
use OGame\Http\Traits\IngameTrait;
use OGame\Http\Traits\ObjectAjaxTrait;
use OGame\Services\BuildingQueueService;
use OGame\Services\ObjectService;
use OGame\Services\PlayerService;
use OGame\Services\UnitQueueService;

abstract class AbstractUnitsController extends Controller
{
    use IngameTrait;
    use ObjectAjaxTrait;

    protected $planet;

    /**
     * @var Index view route (used for redirecting).
     */
    protected $route_view_index;

    /**
     * QueueService
     *
     * @var UnitQueueService
     */
    protected $queue;

    /**
     * AbstractUnitsController constructor.
     */
    public function __construct(UnitQueueService $queue)
    {
        $this->queue = $queue;
    }

    /**
     * Shows the shipyard/defense index page
     *
     * @param int $id
     * @return Response
     */
    public function index(Request $request, PlayerService $player, ObjectService $objects)
    {
        $this->player = $player;
        $this->planet = $player->planets->current();

        $objects_array = $objects->getUnitObjects();

        $count = 0;

        // Parse build queue for this planet.
        $build_queue = $this->queue->retrieveQueue($this->planet);
        $build_queue = $this->queue->enrich($build_queue);

        // Extract active from queue.
        $build_active = [];
        if (!empty($build_queue[0])) {
            $build_active = $build_queue[0];

            // Remove active from queue.
            unset($build_queue[0]);
        }

        // Get total time of all items in queue
        $queue_time_end = $this->queue->retrieveQueueTimeEnd($this->planet);
        $queue_time_countdown = 0;
        if ($queue_time_end > 0) {
            $queue_time_countdown = $queue_time_end - Carbon::now()->timestamp;
        }

        $units = [];
        foreach ($this->objects as $key_row => $objects_row) {
            $buildings[$key_row] = [];

            foreach ($objects_row as $object_id) {
                $count++;

                // Get current level of building
                $amount = $this->planet->getObjectAmount($object_id);

                // Check requirements of this building
                $requirements_met = $objects->objectRequirementsMet($object_id, $this->planet, $player);

                // Check if the current planet has enough resources to build this building.
                $enough_resources = $this->planet->hasResources($objects->getObjectPrice($object_id, $this->planet));

                $units[$key_row][$object_id] = array_merge($objects_array[$object_id], [
                    'amount' => $amount,
                    'requirements_met' => $requirements_met,
                    'count' => $count,
                    'enough_resources' => $enough_resources,
                    'currently_building' => (!empty($build_active['id']) && $build_active['object']['id'] == $object_id),
                ]);
            }
        }

        return view($this->view_name)->with([
            'planet_name' => $this->planet->getPlanetName(),
            'units' => $units,
            'build_active' => $build_active,
            'build_queue' => $build_queue,
            'build_queue_countdown' => $queue_time_countdown,
            'body_id' => $this->body_id, // Sets <body> tag ID property.
        ]);
    }

    /**
     * Handles an incoming add buildrequest.
     */
    public function addBuildRequest(Request $request, PlayerService $player)
    {
        $building_id = $request->input('type');
        $planet_id = $request->input('planet_id');
        $amount = $request->input('amount');

        $planet = $player->planets->childPlanetById($planet_id);
        $this->queue->add($planet, $building_id, $amount);

        return redirect()->route($this->route_view_index);
    }
}
