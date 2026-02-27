<?php

namespace OGame\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Http\Controllers\Abstracts\AbstractUnitsController;
use OGame\Services\HalvingService;
use OGame\Services\PlayerService;
use OGame\Services\UnitQueueService;

class ShipyardController extends AbstractUnitsController
{
    /**
     * ShipyardController constructor.
     *
     * @param UnitQueueService $queue
     */
    public function __construct(UnitQueueService $queue)
    {
        $this->route_view_index = 'shipyard.index';
        parent::__construct($queue);
    }

    /**
     * Shows the shipyard index page
     *
     * @param Request $request
     * @param PlayerService $player
     * @return View
     * @throws Exception
     */
    public function index(Request $request, PlayerService $player): View
    {
        $this->setBodyId('shipyard');

        // Prepare custom properties
        $this->objects = [
            0 => ['light_fighter', 'heavy_fighter', 'cruiser', 'battle_ship', 'battlecruiser', 'bomber', 'destroyer', 'deathstar', 'reaper', 'pathfinder'],
            1 => ['small_cargo', 'large_cargo', 'colony_ship', 'recycler', 'espionage_probe', 'solar_satellite', 'crawler'],
        ];

        return view(view: 'ingame.shipyard.index')->with(
            array_merge(
                [
                    'shipyard_upgrading' => $player->planets->current()->isBuildingObject('shipyard'),
                    'nanite_upgrading' => $player->planets->current()->isBuildingObject('nano_factory')
                ],
                parent::indexPage($request, $player)
            )
        );
    }

    /**
     * Handles the shipyard page AJAX requests.
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
     * @param Request $request
     * @param PlayerService $player
     * @return JsonResponse
     * @throws Exception
     */
    public function addBuildRequest(Request $request, PlayerService $player): JsonResponse
    {
        $planet = $player->planets->current();

        // If the shipyard is upgrading, block the request.
        if ($planet->isBuildingObject('shipyard')) {
            return response()->json([
                'success' => false,
                'errors' => [['message' => __('Shipyard is being upgraded.')]],
            ]);
        }

        // If the nanite factory is upgrading, block the request.
        if ($planet->isBuildingObject('nano_factory')) {
            return response()->json([
                'success' => false,
                'errors' => [['message' => __('Nanite Factory is being upgraded.')]],
            ]);
        }

        return parent::addBuildRequest($request, $player);
    }

    /**
     * Halve a unit queue item using Dark Matter.
     *
     * @param Request $request
     * @param PlayerService $player
     * @param HalvingService $halvingService
     * @return JsonResponse
     */
    public function halveUnit(Request $request, PlayerService $player, HalvingService $halvingService): JsonResponse
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

            $result = $halvingService->halveUnit(
                $player->getUser(),
                $queueItemId,
                $player->planets->current()
            );

            session()->flash('success', __('You have successfully accelerated the order.'));

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
     * Complete a unit queue item instantly using Dark Matter.
     *
     * @param Request $request
     * @param PlayerService $player
     * @param HalvingService $halvingService
     * @return JsonResponse
     */
    public function completeUnit(Request $request, PlayerService $player, HalvingService $halvingService): JsonResponse
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

            $result = $halvingService->completeUnit(
                $player->getUser(),
                $queueItemId,
                $player->planets->current()
            );

            session()->flash('success', __('You have successfully accelerated the order.'));

            return response()->json([
                'success' => true,
                'error' => false,
                'newAjaxToken' => csrf_token(),
                'cost' => $result['cost'],
                'new_balance' => $result['new_balance'],
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
