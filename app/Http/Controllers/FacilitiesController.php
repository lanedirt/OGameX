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

            $wreckFieldService = new WreckFieldService($player, app(SettingsService::class));
            $wreckField = $wreckFieldService->getWreckFieldForCurrentPlanet($planetService);

            if (!$wreckField) {
                return response()->json([
                    'success' => false,
                    'error' => true,
                    'message' => __('wreck_field.error_no_wreck_field'),
                    'newAjaxToken' => csrf_token(),
                ]);
            }

            // Get the space dock level from the current planet
            $spaceDockLevel = $planetService->getObjectLevel('space_dock');

            // Check if space dock exists (level >= 1)
            if ($spaceDockLevel < 1) {
                return response()->json([
                    'success' => false,
                    'error' => true,
                    'message' => __('wreck_field.error_space_dock_required'),
                    'newAjaxToken' => csrf_token(),
                ])->setStatusCode(400)->header('Content-Type', 'application/json');
            }

            // Load the wreck field for repairs
            $wreckFieldService->loadForCoordinates($planetService->getPlanetCoordinates());
            $wreckFieldService->startRepairs($spaceDockLevel);

            // Get updated data
            $updatedData = $wreckFieldService->getWreckFieldForCurrentPlanet($planetService);

            return response()->json([
                'success' => true,
                'error' => false,
                'newAjaxToken' => csrf_token(),
                'message' => __('wreck_field.repairs_started'),
                'wreckField' => $updatedData,
            ])->header('Content-Type', 'application/json');
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => $e->getMessage(),
                'newAjaxToken' => csrf_token(),
            ])->setStatusCode(400)->header('Content-Type', 'application/json');
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

            $wreckFieldService = new WreckFieldService($player, app(SettingsService::class));
            $wreckField = $wreckFieldService->getWreckFieldForCurrentPlanet($planetService);

            if (!$wreckField) {
                return response()->json([
                    'success' => false,
                    'error' => true,
                    'message' => __('wreck_field.error_no_wreck_field'),
                    'newAjaxToken' => csrf_token(),
                ]);
            }

            $overallProgress = $wreckField['repair_progress'] ?? 0;

            $wreckFieldModel = $wreckField['wreck_field'];
            $repairStartedAt = $wreckFieldModel->repair_started_at ?? null;

            if (!$repairStartedAt) {
                return response()->json([
                    'success' => false,
                    'error' => true,
                    'message' => __('wreck_field.repairs_not_started'),
                    'newAjaxToken' => csrf_token(),
                ])->setStatusCode(400)->header('Content-Type', 'application/json');
            }

            // Load the wreck field to collect completed repairs
            $wreckFieldService->loadForCoordinates($planetService->getPlanetCoordinates());

            // Get current wreck field model
            $wreckFieldModelForUpdate = $wreckFieldService->getWreckField();
            if (!$wreckFieldModelForUpdate) {
                return response()->json([
                    'success' => false,
                    'error' => true,
                    'message' => __('wreck_field.error_no_wreck_field'),
                    'newAjaxToken' => csrf_token(),
                ])->setStatusCode(400)->header('Content-Type', 'application/json');
            }

            $currentShipData = $wreckFieldModelForUpdate->ship_data ?? [];
            $collectedShips = [];
            $remainingShips = [];

            // Calculate repaired ships based on overall repair progress
            $overallProgress = ($wreckField['repair_progress'] ?? 0) / 100;

            foreach ($currentShipData as $ship) {
                $repairedCount = (int) floor($ship['quantity'] * $overallProgress);
                $remainingCount = $ship['quantity'] - $repairedCount;

                if ($repairedCount > 0) {
                    // Add repaired ships to collection
                    $collectedShips[] = [
                        'machine_name' => $ship['machine_name'],
                        'quantity' => $repairedCount,
                        'repair_progress' => 100
                    ];

                    // Add repaired ships to planet
                    $unitObject = app(\OGame\Services\ObjectService::class)->getUnitObjectByMachineName($ship['machine_name']);
                    if ($unitObject) {
                        $planetService->addUnit($unitObject->machine_name, $repairedCount);
                    }
                }

                if ($remainingCount > 0) {
                    // Keep remaining ships in wreck field
                    $remainingShips[] = [
                        'machine_name' => $ship['machine_name'],
                        'quantity' => $remainingCount,
                        'repair_progress' => 0
                    ];
                }
            }

            // Update wreck field with remaining ships
            if (empty($remainingShips)) {
                // All ships collected, delete the wreck field
                $wreckFieldService->delete();
            } else {
                // Update with remaining ships
                $wreckFieldModelForUpdate->ship_data = $remainingShips;
                $wreckFieldModelForUpdate->save();
            }

            return response()->json([
                'success' => true,
                'error' => false,
                'newAjaxToken' => csrf_token(),
                'message' => count($collectedShips) > 0 ? __('wreck_field.all_ships_deployed') : __('wreck_field.no_ships_ready'),
                'collected_ships' => $collectedShips,
                'remaining_ships' => $remainingShips,
            ])->header('Content-Type', 'application/json');
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => $e->getMessage(),
                'newAjaxToken' => csrf_token(),
            ])->setStatusCode(400)->header('Content-Type', 'application/json');
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
                    'message' => __('wreck_field.error_no_wreck_field'),
                    'newAjaxToken' => csrf_token(),
                ]);
            }

            if ($wreckField['is_repairing']) {
                return response()->json([
                    'success' => false,
                    'error' => true,
                    'message' => __('wreck_field.cannot_burn'),
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
                'message' => __('wreck_field.wreck_field_burned_success'),
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

            $wreckFieldService = new WreckFieldService($player, app(SettingsService::class));
            $wreckField = $wreckFieldService->getWreckFieldForCurrentPlanet($planetService);

            return response()->json([
                'success' => true,
                'error' => false,
                'newAjaxToken' => csrf_token(),
                'wreckField' => $wreckField,
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
