<?php

namespace OGame\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Http\Traits\ObjectAjaxTrait;
use OGame\Services\CharacterClassService;
use OGame\Services\HalvingService;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use OGame\Services\ResearchQueueService;
use OGame\ViewModels\ResearchViewModel;

class ResearchController extends OGameController
{
    use ObjectAjaxTrait;

    protected PlanetService $planet;

    /**
     * @var string Index view route (used for redirecting).
     */
    protected string $route_view_index;

    /**
     * ResourcesController constructor.
     */
    public function __construct(/**
     * QueueService
     */
    protected ResearchQueueService $queue)
    {
        $this->route_view_index = 'research.index';

        parent::__construct();
    }

    /**
     * Shows the research index page
     *
     * @param Request $request
     * @param PlayerService $player
     * @return View
     * @throws Exception
     */
    public function index(Request $request, PlayerService $player): View
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

        // Combat research technologies that get General class bonus
        $combat_research = ['weapon_technology', 'shielding_technology', 'armor_technology'];

        // Get character class bonus for combat research
        $characterClassService = app(CharacterClassService::class);
        $combatResearchBonus = $characterClassService->getAdditionalCombatResearchLevels($player->getUser());

        $count = 0;

        // Parse research queue for this planet
        $research_full_queue = $this->queue->retrieveQueue($planet);
        $research_active = $research_full_queue->getCurrentlyBuildingFromQueue();
        $research_queue = $research_full_queue->getQueuedFromQueue();

        // Researching is disallowed when Research Lab is upgrading
        $research_lab_upgrading = $player->isBuildingObject('research_lab');

        $research = [];
        foreach ($screen_objects as $key_row => $objects_row) {
            foreach ($objects_row as $object_machine_name) {
                $count++;

                $object = ObjectService::getResearchObjectByMachineName($object_machine_name);

                // Get current level of technology
                $current_level = $player->getResearchLevel($object->machine_name);
                $next_level = $current_level + 1;

                // Check requirements of this technology
                $requirements_met = ObjectService::objectRequirementsMetWithQueue($object->machine_name, $next_level, $planet);

                // Check if the current planet has enough resources to research this technology.
                $enough_resources = $planet->hasResources(ObjectService::getObjectPrice($object->machine_name, $planet));

                $view_model = new ResearchViewModel();
                $view_model->object = $object;
                $view_model->current_level = $current_level;
                $view_model->requirements_met = $requirements_met;
                $view_model->count = $count;
                $view_model->enough_resources = $enough_resources;
                $view_model->currently_building = (!empty($research_active) && $research_active->object->machine_name === $object->machine_name);
                $view_model->research_lab_upgrading = $research_lab_upgrading;

                // Apply combat research bonus if applicable
                if (in_array($object->machine_name, $combat_research)) {
                    $view_model->bonus_level = $combatResearchBonus;
                }

                $research[$key_row][$object->id] = $view_model;
            }
        }

        // Max amount of research that can be in the queue in a given time.
        $build_queue_max = false;
        if ($research_full_queue->isQueueFull()) {
            $build_queue_max = true;
        }

        // If openTech is in querystring, add client JS to open the technology tab.
        $open_tech_id = 0;
        if ($request->query->has('openTech')) {
            $open_tech_id = $request->query('openTech');
            if (!is_numeric($open_tech_id)) {
                $open_tech_id = 0;
            }
        }

        return view('ingame.research.index')->with([
            'planet_id' => $planet->getPlanetId(),
            'planet_name' => $planet->getPlanetName(),
            'research' => $research,
            'build_active' => $research_active,
            'build_queue' => $research_queue,
            'build_queue_max' => $build_queue_max,
            'research_lab_upgrading' => $research_lab_upgrading,
            'open_tech_id' => $open_tech_id,
        ]);
    }

    /**
     * Handles the research page AJAX requests.
     *
     * @param Request $request
     * @param PlayerService $player
     * @return JsonResponse
     * @throws Exception
     */
    public function ajax(Request $request, PlayerService $player): JsonResponse
    {
        return $this->ajaxHandler($request, $player);
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
        // Check if player is in vacation mode
        // Note: Button is already disabled in frontend, but we check here for security
        if ($player->isInVacationMode()) {
            return response()->json([
                'success' => false,
            ]);
        }

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
    public function cancelBuildRequest(Request $request, PlayerService $player): JsonResponse
    {
        $building_id = $request->input('technologyId');
        $building_queue_id = $request->input('listId');

        $this->queue->cancel($player, $building_queue_id, $building_id);

        return response()->json([
            'status' => 'success',
            'message' => 'Building construction canceled.',
        ]);
    }

    /**
     * Halve a research queue item using Dark Matter.
     *
     * @param Request $request
     * @param PlayerService $player
     * @param HalvingService $halvingService
     * @return JsonResponse
     */
    public function halveResearch(Request $request, PlayerService $player, HalvingService $halvingService): JsonResponse
    {
        try {
            $queueItemId = (int)$request->input('queue_item_id');

            if ($queueItemId <= 0) {
                return response()->json([
                    'success' => false,
                    'error' => true,
                    'message' => 'Invalid queue item ID',
                    'newAjaxToken' => csrf_token(),
                ]);
            }

            $result = $halvingService->halveResearch(
                $player->getUser(),
                $queueItemId,
                $player
            );

            return response()->json([
                'success' => true,
                'error' => false,
                'newAjaxToken' => csrf_token(),
                'new_time_end' => $result['new_time_end'],
                'cost' => $result['cost'],
                'new_balance' => $result['new_balance'],
                'remaining_time' => $result['remaining_time'],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => $e->getMessage(),
                'newAjaxToken' => csrf_token(),
            ]);
        }
    }
}
