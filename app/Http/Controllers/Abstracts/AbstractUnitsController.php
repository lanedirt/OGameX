<?php

namespace OGame\Http\Controllers\Abstracts;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
     * @return array<string, mixed>
     * @throws Exception
     */
    public function indexPage(Request $request, PlayerService $player): array
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

                $object = ObjectService::getUnitObjectByMachineName($object_machine_name);

                // Get current amount of this unit.
                $amount = $planet->getObjectAmount($object->machine_name);

                // Check requirements of this building
                $requirements_met = ObjectService::objectRequirementsMet($object->machine_name, $planet);

                // Check if the current planet has enough resources to build this building.
                $enough_resources = $planet->hasResources(ObjectService::getObjectPrice($object->machine_name, $planet));

                // Get maximum build amount of this building
                $max_build_amount = ObjectService::getObjectMaxBuildAmount($object->machine_name, $planet, $requirements_met);

                $view_model = new UnitViewModel();
                $view_model->object = $object;
                $view_model->count = $count;
                $view_model->amount = $amount;
                $view_model->requirements_met = $requirements_met;
                $view_model->enough_resources = $enough_resources;
                $view_model->max_build_amount = $max_build_amount;
                $view_model->currently_building = (!empty($build_active) && $build_active->object->machine_name == $object->machine_name);
                $view_model->currently_building_amount = (!empty($build_active) && $build_active->object->machine_name == $object->machine_name) ? $build_active->object_amount_remaining : 0;

                $units[$key_row][$object->id] = $view_model;
            }
        }

        // If openTech is in querystring, add client JS to open the technology tab.
        $open_tech_id = 0;
        if ($request->query->has('openTech')) {
            $open_tech_id = $request->query('openTech');
            if (!is_numeric($open_tech_id)) {
                $open_tech_id = 0;
            }
        }

        return [
            'planet_id' => $planet->getPlanetId(),
            'planet_name' => $planet->getPlanetName(),
            'units' => $units,
            'build_active' => $build_active,
            'build_queue' => $build_queue,
            'build_queue_countdown' => $queue_time_countdown,
            'open_tech_id' => $open_tech_id,
        ];
    }

    /**
     * Handles an incoming add build request.
     *
     * @param Request $request
     * @param PlayerService $player
     * @return JsonResponse
     * @throws Exception
     */
    public function addBuildRequest(Request $request, PlayerService $player): JsonResponse
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
