<?php

namespace OGame\Http\Controllers\Abstracts;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use OGame\Http\Controllers\OGameController;
use OGame\Http\Traits\ObjectAjaxTrait;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use OGame\Services\UnitQueueService;
use OGame\ViewModels\UnitViewModel;

abstract class AbstractUnitsController extends OGameController
{
    use ObjectAjaxTrait;

    protected PlanetService $planet;

    /**
     * @var string Index view route (used for redirecting).
     */
    protected string $route_view_index;

    /**
     * QueueService
     *
     * @var UnitQueueService
     */
    protected UnitQueueService $queue;

    /**
     * Objects that are shown on this building page.
     *
     * @var array<array<string>>
     */
    protected array $objects = [];

    /**
     * Name of view that is returned by this controller.
     *
     * @var string
     */
    protected string $view_name;

    /**
     * AbstractUnitsController constructor.
     */
    public function __construct(UnitQueueService $queue)
    {
        $this->queue = $queue;
        parent::__construct();
    }

    /**
     * Shows the shipyard/defense index page
     *
     * @param Request $request
     * @param PlayerService $player
     * @param ObjectService $objects
     * @return View
     * @throws Exception
     */
    public function index(Request $request, PlayerService $player, ObjectService $objects) : View
    {
        $planet = $player->planets->current();

        $count = 0;

        // Parse build queue for this planet.
        $build_queue_full = $this->queue->retrieveQueue($planet);
        $build_active = $build_queue_full->getCurrentlyBuildingFromQueue();
        $build_queue = $build_queue_full->getQueuedFromQueue();

        // Get total time of all items in queue
        $queue_time_end = $this->queue->retrieveQueueTimeEnd($planet);
        $queue_time_countdown = 0;
        if ($queue_time_end > 0) {
            $queue_time_countdown = $queue_time_end - (int)Carbon::now()->timestamp;
        }

        $units = [];
        foreach ($this->objects as $key_row => $objects_row) {
            foreach ($objects_row as $object_machine_name) {
                $count++;

                $object = $objects->getUnitObjectByMachineName($object_machine_name);

                // Get current level of building
                $amount = $planet->getObjectAmount($object->machine_name);

                // Check requirements of this building
                $requirements_met = $objects->objectRequirementsMet($object->machine_name, $planet, $player);

                // Check if the current planet has enough resources to build this building.
                $enough_resources = $planet->hasResources($objects->getObjectPrice($object->machine_name, $planet));

                $view_model = new UnitViewModel();
                $view_model->object = $object;
                $view_model->count = $count;
                $view_model->amount = $amount;
                $view_model->requirements_met = $requirements_met;
                $view_model->enough_resources = $enough_resources;
                $view_model->currently_building = (!empty($build_active) && $build_active->object->machine_name == $object->machine_name);
                $view_model->currently_building_amount = (!empty($build_active) && $build_active->object->machine_name == $object->machine_name) ? $build_active->object_amount_remaining : 0;

                $units[$key_row][$object->id] = $view_model;
            }
        }

        return view($this->view_name)->with([
            'planet_id' => $planet->getPlanetId(),
            'planet_name' => $planet->getPlanetName(),
            'units' => $units,
            'build_active' => $build_active,
            'build_queue' => $build_queue,
            'build_queue_countdown' => $queue_time_countdown,
        ]);
    }

    /**
     * Handles an incoming add buildrequest.
     *
     * @param Request $request
     * @param PlayerService $player
     * @return JsonResponse
     * @throws Exception
     */
    public function addBuildRequest(Request $request, PlayerService $player) : JsonResponse
    {
        // Explicitly verify CSRF token because this request supports both POST and GET.
        if (!hash_equals($request->session()->token(), $request->input('_token'))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token.',
            ]);
        }

        $building_id = $request->input('technologyId');
        $amount = $request->input('amount');

        $this->queue->add($player->planets->current(), $building_id, $amount);

        return response()->json([
            'status' => 'success',
            'message' => 'Added to build order.',
        ]);
    }
}
