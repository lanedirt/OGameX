<?php

namespace OGame\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Http\Traits\ObjectAjaxTrait;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use OGame\Services\ResearchQueueService;
use OGame\ViewModels\BuildingViewModel;

class ResearchController extends OGameController
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
     * @var ResearchQueueService
     */
    protected ResearchQueueService $queue;

    /**
     * ResourcesController constructor.
     */
    public function __construct(ResearchQueueService $queue)
    {
        $this->route_view_index = 'research.index';
        $this->queue = $queue;
        parent::__construct();
    }

    /**
     * Shows the research index page
     *
     * @param PlayerService $player
     * @param ObjectService $objects
     * @return View
     * @throws Exception
     */
    public function index(PlayerService $player, ObjectService $objects) : View
    {
        $this->setBodyId('research');
        $planet = $player->planets->current();

        // Prepare custom properties
        $screen_objects = [
            0 => ['energy_technology', 'laser_technology', 'ion_technology', 'hyperspace_technology', 'plasma_technology'],
            1 => ['combustion_drive', 'impulse_drive', 'hyperspace_drive'],
            2 => ['espionage_technology', 'computer_technology', 'astrophysics', 'intergalactic_research_network', 'graviton_technology'],
            3 => ['weapon_technology', 'shielding_technology', 'armor_technology'],
        ];

        $count = 0;

        // Parse build queue for this planet
        $research_full_queue = $this->queue->retrieveQueue($planet);
        $build_active = $research_full_queue->getCurrentlyBuildingFromQueue();
        $build_queue = $research_full_queue->getQueuedFromQueue();

        $research = [];
        foreach ($screen_objects as $key_row => $objects_row) {
            foreach ($objects_row as $object_machine_name) {
                $count++;

                $object = $objects->getResearchObjectByMachineName($object_machine_name);

                // Get current level of building
                $current_level = $player->getResearchLevel($object->machine_name);

                // Check requirements of this building
                $requirements_met = $objects->objectRequirementsMet($object->machine_name, $planet, $player);

                // Check if the current planet has enough resources to build this building.
                $enough_resources = $planet->hasResources($objects->getObjectPrice($object->machine_name, $planet));

                $view_model = new BuildingViewModel();
                $view_model->object = $object;
                $view_model->current_level = $current_level;
                $view_model->requirements_met = $requirements_met;
                $view_model->count = $count;
                $view_model->enough_resources = $enough_resources;
                $view_model->currently_building = (!empty($build_active) && $build_active->object->machine_name == $object->machine_name);

                $research[$key_row][$object->id] = $view_model;
            }
        }

        // Max amount of research that can be in the queue in a given time.
        $build_queue_max = false;
        if ($research_full_queue->isQueueFull()) {
            $build_queue_max = true;
        }

        return view('ingame.research.index')->with([
            'planet_id' => $planet->getPlanetId(),
            'planet_name' => $planet->getPlanetName(),
            'research' => $research,
            'build_active' => $build_active,
            'build_queue' => $build_queue,
            'build_queue_max' => $build_queue_max,
        ]);
    }

    /**
     * Handles the research page AJAX requests.
     *
     * @param Request $request
     * @param PlayerService $player
     * @param ObjectService $objects
     * @return JsonResponse
     * @throws Exception
     */
    public function ajax(Request $request, PlayerService $player, ObjectService $objects) : JsonResponse
    {
        return $this->ajaxHandler($request, $player, $objects);
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
        $this->queue->add($player, $player->planets->current(), $building_id);

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
    public function cancelBuildRequest(Request $request, PlayerService $player) : JsonResponse
    {
        $building_id = $request->input('technologyId');
        $building_queue_id = $request->input('listId');

        $this->queue->cancel($player, $player->planets->current(), $building_queue_id, $building_id);

        return response()->json([
            'status' => 'success',
            'message' => 'Building construction canceled.',
        ]);
    }
}
