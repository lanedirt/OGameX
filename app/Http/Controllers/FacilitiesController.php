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

class FacilitiesController extends AbstractBuildingsController
{
    /**
     * ResourcesController constructor.
     */
    public function __construct(BuildingQueueService $queue)
    {
        $this->route_view_index = 'facilities.index';
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
            parent::indexPageParams($request, $player)
        );
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
}
