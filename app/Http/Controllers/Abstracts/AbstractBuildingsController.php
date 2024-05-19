<?php

namespace OGame\Http\Controllers\Abstracts;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Http\Controllers\OGameController;
use OGame\Http\Traits\ObjectAjaxTrait;
use OGame\Services\BuildingQueueService;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use OGame\ViewModels\BuildingViewModel;

abstract class AbstractBuildingsController extends OGameController
{
    use ObjectAjaxTrait;

    protected PlanetService $planet;

    /**
     * @var string Index view route (used for redirecting).
     */
    protected string $route_view_index;

    /**
     * QueueService.
     *
     * @var BuildingQueueService
     */
    protected BuildingQueueService $queue;

    /**
     * Objects that are shown on this building page.
     *
     * @var array<array<string>>
     */
    protected array $objects = [];

    /**
     * Header filename objects.
     *
     * @var array<int>
     */
    protected array $header_filename_objects = [];

    /**
     * Name of view that is returned by this controller.
     *
     * @var string
     */
    protected string $view_name;

    /**
     * AbstractBuildingsController constructor.
     */
    public function __construct(BuildingQueueService $queue)
    {
        $this->queue = $queue;
        parent::__construct();
    }

    /**
     * Shows the building index page.
     *
     * @param Request $request
     * @param PlayerService $player
     * @param ObjectService $objects
     * @return View
     * @throws Exception
     */
    public function index(Request $request, PlayerService $player, ObjectService $objects): View
    {
        $this->planet = $player->planets->current();

        $count = 0;
        $header_filename_parts = [];

        // Parse build queue for this planet
        $build_full_queue = $this->queue->retrieveQueue($this->planet);
        $build_active = $build_full_queue->getCurrentlyBuildingFromQueue();
        $build_queue = $build_full_queue->getQueuedFromQueue();

        $buildings = [];
        foreach ($this->objects as $key_row => $objects_row) {
            $buildings[$key_row] = [];
            foreach ($objects_row as $object_machine_name) {
                $count++;

                // Get object
                $object = $objects->getObjectByMachineName($object_machine_name);

                // Get current level of building
                $current_level = $this->planet->getObjectLevel($object_machine_name);

                // Check requirements of this building
                $requirements_met = $objects->objectRequirementsMet($object_machine_name, $this->planet, $player);

                // Check if the current planet has enough resources to build this building.
                $enough_resources = $this->planet->hasResources($objects->getObjectPrice($object_machine_name, $this->planet));

                // If building level is 1 or higher, add to header filename parts to
                // render the header of this planet.
                if ($current_level >= 1 && in_array($object->id, $this->header_filename_objects, true)) {
                    $header_filename_parts[$object->id] = $object->id;
                }

                $view_model = new BuildingViewModel();
                $view_model->count = $count;
                $view_model->object = $object;
                $view_model->current_level = $current_level;
                $view_model->requirements_met = $requirements_met;
                $view_model->enough_resources = $enough_resources;
                $view_model->currently_building = ($build_active !== null && $build_active->object->machine_name === $object->machine_name);

                $buildings[$key_row][$object->id] = $view_model;
            }
        }

        // Parse header filename for this planet
        ksort($header_filename_parts);
        $header_filename = $this->planet->getPlanetType();
        foreach ($header_filename_parts as $building_id) {
            $header_filename .= '_' . $building_id;
        }

        // Max amount of buildings that can be in the queue in a given time.
        $max_build_queue_count = 4; //@TODO: refactor into global / constant?
        $build_queue_max = false;
        if (count($build_queue) >= $max_build_queue_count) {
            $build_queue_max = true;
        }

        return view($this->view_name)->with([
            'planet_id' => $this->planet->getPlanetId(),
            'planet_name' => $this->planet->getPlanetName(),
            'header_filename' => $header_filename,
            'buildings' => $buildings,
            'build_active' => $build_active,
            'build_queue' => $build_queue,
            'build_queue_max' => $build_queue_max,
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
        $this->queue->add($player->planets->current(), $building_id);

        return response()->json([
            'status' => 'success',
            'message' => 'Building construction started.',
        ]);
    }

    /**
     * Handles an incoming cancel buildrequest.
     *
     * @param Request $request
     * @param PlayerService $player
     * @return JsonResponse
     * @throws Exception
     */
    public function cancelBuildRequest(Request $request, PlayerService $player): JsonResponse
    {
        $building_id = $request->input('technologyId');
        $building_queue_id = $request->input('listId');

        $this->queue->cancel($player->planets->current(), $building_queue_id, $building_id);

        return response()->json([
            'status' => 'success',
            'message' => 'Building construction canceled.',
        ]);
    }
}
