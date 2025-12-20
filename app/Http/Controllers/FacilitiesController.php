<?php

namespace OGame\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Http\Controllers\Abstracts\AbstractBuildingsController;
use OGame\Services\BuildingQueueService;
use OGame\Services\HalvingService;
use OGame\Services\PlayerService;
use OGame\Services\SettingsService;
use OGame\Services\WreckFieldService;

class FacilitiesController extends AbstractBuildingsController
{
    private WreckFieldService $wreckFieldService;

    /**
     * ResourcesController constructor.
     */
    public function __construct(BuildingQueueService $queue, WreckFieldService $wreckFieldService)
    {
        $this->route_view_index = 'facilities.index';
        $this->wreckFieldService = $wreckFieldService;
        parent::__construct($queue);
    }

    /**
     * Shows the facilities index page
     *
     * @param Request $request
     * @param PlayerService $player
     * @return View
     * @throws Exception
     */
    public function index(Request $request, PlayerService $player): View
    {
        $this->setBodyId('station');
        $this->planet = $player->planets->current();

        // Prepare custom properties.
        // Header filename objects are the building IDs that make up the header filename
        // to be used in the background image of the page header.
        if ($this->planet->isPlanet()) {
            $this->header_filename_objects = [14, 21, 31, 34];
            $this->objects = [
                ['robot_factory', 'shipyard', 'research_lab', 'alliance_depot', 'missile_silo', 'nano_factory', 'terraformer', 'space_dock'],
            ];
        } elseif ($this->planet->isMoon()) {
            $this->header_filename_objects = [41, 42, 43];
            $this->objects = [
                ['robot_factory', 'shipyard', 'lunar_base', 'sensor_phalanx', 'jump_gate'],
            ];
        }

        return view('ingame.facilities.index')->with(
            $this->indexPageParams($request, $player)
        );
    }

    /**
     * Override indexPageParams to add wreck field data.
     *
     * @param Request $request
     * @param PlayerService $player
     * @return array
     * @throws Exception
     */
    public function indexPageParams(Request $request, PlayerService $player): array
    {
        // Get parent parameters
        $params = parent::indexPageParams($request, $player);

        // Add wreck field data
        $wreckFieldData = $this->wreckFieldService->getWreckFieldForCurrentPlanet($this->planet);
        $params['wreckField'] = $wreckFieldData;

        return $params;
    }

    /**
     * Handles the facilities page AJAX requests.
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
     * Halve a building queue item using Dark Matter.
     *
     * @param Request $request
     * @param PlayerService $player
     * @param HalvingService $halvingService
     * @return JsonResponse
     */
    public function halveBuilding(Request $request, PlayerService $player, HalvingService $halvingService): JsonResponse
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

            $result = $halvingService->halveBuilding(
                $player->getUser(),
                $queueItemId,
                $player->planets->current()
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

    /**
     * Start repairs for the wreck field.
     *
     * @param Request $request
     * @param PlayerService $player
     * @return JsonResponse
     */
    public function startRepairs(Request $request, PlayerService $player): JsonResponse
    {
        try {
            $planetService = $player->planets->current();

            // Create a new WreckFieldService instance with the correct player
            $wreckFieldService = new WreckFieldService($player, app(SettingsService::class));
            $wreckField = $wreckFieldService->getWreckFieldForCurrentPlanet($planetService);

            if (!$wreckField) {
                return response()->json([
                    'success' => false,
                    'error' => true,
                    'message' => 'No wreck field found',
                    'newAjaxToken' => csrf_token(),
                ]);
            }

            // For now, assume space dock exists since we set it to level 1 earlier
            // TODO: Implement proper space dock level checking once we find the correct method
            $spaceDockLevel = 1;

            // Load the wreck field for repairs
            $wreckFieldService->loadForCoordinates($planetService->getPlanetCoordinates());

            $wreckFieldService->startRepairs($spaceDockLevel);

            // Get updated data
            $updatedData = $this->wreckFieldService->getWreckFieldForCurrentPlanet($planetService);

            return response()->json([
                'success' => true,
                'error' => false,
                'newAjaxToken' => csrf_token(),
                'message' => 'Repairs started successfully',
                'wreckField' => $updatedData,
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

    /**
     * Complete repairs and add ships to planet.
     *
     * @param Request $request
     * @param PlayerService $player
     * @return JsonResponse
     */
    public function completeRepairs(Request $request, PlayerService $player): JsonResponse
    {
        try {
            $planetService = $player->planets->current();

            // Create a new WreckFieldService instance with the correct player
            $wreckFieldService = new WreckFieldService($player, app(SettingsService::class));
            $wreckField = $wreckFieldService->getWreckFieldForCurrentPlanet($planetService);

            if (!$wreckField) {
                return response()->json([
                    'success' => false,
                    'error' => true,
                    'message' => 'No wreck field found',
                    'newAjaxToken' => csrf_token(),
                ]);
            }

            if (!$wreckField['is_completed']) {
                return response()->json([
                    'success' => false,
                    'error' => true,
                    'message' => 'Repairs are not yet completed',
                    'newAjaxToken' => csrf_token(),
                ]);
            }

            // Load the wreck field to complete repairs
            $wreckFieldService->loadForCoordinates($planetService->getPlanetCoordinates());

            $repairedShips = $wreckFieldService->completeRepairs();

            // Add repaired ships to planet
            $unitFactory = app(UnitFactory::class);
            foreach ($repairedShips as $ship) {
                if ($ship['repair_progress'] >= 100) {
                    $unitObject = $unitFactory->createUnitFromMachineName($ship['machine_name']);
                    if ($unitObject) {
                        $planetService->addUnit($unitObject, $ship['quantity']);
                    }
                }
            }

            // Delete the wreck field after repairs are completed
            $wreckFieldService->delete();

            return response()->json([
                'success' => true,
                'error' => false,
                'newAjaxToken' => csrf_token(),
                'message' => 'Repairs completed successfully',
                'repaired_ships' => $repairedShips,
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

    /**
     * Burn the wreck field.
     *
     * @param Request $request
     * @param PlayerService $player
     * @return JsonResponse
     */
    public function burnWreckField(Request $request, PlayerService $player): JsonResponse
    {
        try {
            $planetService = $player->planets->current();

            // Create a new WreckFieldService instance with the correct player
            $wreckFieldService = new WreckFieldService($player, app(SettingsService::class));
            $wreckField = $wreckFieldService->getWreckFieldForCurrentPlanet($planetService);

            if (!$wreckField) {
                return response()->json([
                    'success' => false,
                    'error' => true,
                    'message' => 'No wreck field found',
                    'newAjaxToken' => csrf_token(),
                ]);
            }

            if ($wreckField['is_repairing']) {
                return response()->json([
                    'success' => false,
                    'error' => true,
                    'message' => 'Cannot burn wreck field while repairs are in progress',
                    'newAjaxToken' => csrf_token(),
                ]);
            }

            // Load the wreck field to burn it
            $wreckFieldService->loadForCoordinates($planetService->getPlanetCoordinates());

            $wreckFieldService->burnWreckField();

            return response()->json([
                'success' => true,
                'error' => false,
                'newAjaxToken' => csrf_token(),
                'message' => 'Wreck field burned successfully',
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

    /**
     * Get wreck field status for AJAX updates.
     *
     * @param Request $request
     * @param PlayerService $player
     * @return JsonResponse
     */
    public function getWreckFieldStatus(Request $request, PlayerService $player): JsonResponse
    {
        try {
            $planetService = $player->planets->current();

            // Create a new WreckFieldService instance with the correct player
            $wreckFieldService = new WreckFieldService($player, app(SettingsService::class));

            // Debug logging
            \Log::info('getWreckFieldStatus called', [
                'player_id' => $player->getId(),
                'planet_coordinates' => [
                    'galaxy' => $planetService->getPlanetCoordinates()->galaxy,
                    'system' => $planetService->getPlanetCoordinates()->system,
                    'planet' => $planetService->getPlanetCoordinates()->position
                ]
            ]);

            $wreckField = $wreckFieldService->getWreckFieldForCurrentPlanet($planetService);

            return response()->json([
                'success' => true,
                'error' => false,
                'newAjaxToken' => csrf_token(),
                'wreckField' => $wreckField,
                'debug_info' => [
                    'player_id' => $player->getId(),
                    'planet_coordinates' => [
                        'galaxy' => $planetService->getPlanetCoordinates()->galaxy,
                        'system' => $planetService->getPlanetCoordinates()->system,
                        'planet' => $planetService->getPlanetCoordinates()->position
                    ]
                ]
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
