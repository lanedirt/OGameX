<?php

namespace OGame\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Http\Controllers\Abstracts\AbstractUnitsController;
use OGame\Services\Objects\ObjectService;
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
     * @param ObjectService $objects
     * @return View
     */
    public function index(Request $request, PlayerService $player, ObjectService $objects) : View
    {
        $this->setBodyId('shipyard');

        // Prepare custom properties
        $this->objects = [
            0 => [204, 205, 206, 207, 215, 211, 213, 214],
            1 => [202, 203, 208, 209, 210, 212],
        ];
        $this->view_name = 'ingame.shipyard.index';

        return parent::index($request, $player, $objects);
    }

    /**
     * Handles the shipyard page AJAX requests.
     *
     * @param Request $request
     * @param PlayerService $player
     * @param ObjectService $objects
     * @return View
     * @throws Exception
     */
    public function ajax(Request $request, PlayerService $player, ObjectService $objects) : View
    {
        return $this->ajaxHandler($request, $player, $objects);
    }
}
