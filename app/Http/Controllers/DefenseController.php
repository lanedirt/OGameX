<?php

namespace OGame\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Http\Controllers\Abstracts\AbstractUnitsController;
use OGame\Services\PlayerService;
use OGame\Services\UnitQueueService;

class DefenseController extends AbstractUnitsController
{
    /**
     * ShipyardController constructor.
     */
    public function __construct(UnitQueueService $queue)
    {
        $this->route_view_index = 'defense.index';
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
        $this->setBodyId('defense');

        // Prepare custom properties
        $this->objects = [
            [
                'rocket_launcher',
                'light_laser',
                'heavy_laser',
                'gauss_cannon',
                'ion_cannon',
                'plasma_turret',
                'small_shield_dome',
                'large_shield_dome',
                'anti_ballistic_missile',
                'interplanetary_missile'
            ],
        ];

        return view(view: 'ingame.defense.index')->with(
            array_merge(
                ['shipyard_upgrading' => $player->planets->current()->isBuildingObject('shipyard')],
                parent::indexPage($request, $player)
            )
        );
    }

    /**
     * Handles the research page AJAX requests.
     *
     * @param Request $request
     * @param PlayerService $player
     * @return JsonResponse
     * @throws Exception
     */
    public function addBuildRequest(Request $request, PlayerService $player): JsonResponse
    {
        // If the shipyard isn't upgrading, we can continue to process the request.
        if (!$player->planets->current()->isBuildingObject('shipyard')) {
            return parent::addBuildRequest($request, $player);
        } else {
            // Otherwise, it shouldn't be allowed.
            return response()->json([
                'success' => false,
                'errors' => [['message' => __('Shipyard is being upgraded.')]],
            ]);
        }
    }

    /**
     * Handles the research page AJAX requests.
     *
     * @throws Exception
     */
    public function ajax(Request $request, PlayerService $player): JsonResponse
    {
        return $this->ajaxHandler($request, $player);
    }
}
