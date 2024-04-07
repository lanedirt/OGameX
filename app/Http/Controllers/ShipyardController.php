<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use OGame\Http\Controllers\Abstracts\AbstractUnitsController;
use OGame\Services\Objects\ObjectService;
use OGame\Services\PlayerService;
use OGame\Services\UnitQueueService;

class ShipyardController extends AbstractUnitsController
{
    /**
     * ShipyardController constructor.
     */
    public function __construct(UnitQueueService $queue)
    {
        $this->route_view_index = 'shipyard.index';
        parent::__construct($queue);
    }

    /**
     * Shows the shipyard index page
     *
     * @param int $id
     * @return Response
     */
    public function index(Request $request, PlayerService $player, ObjectService $objects)
    {
        // Prepare custom properties
        $this->objects = [
            0 => [204, 205, 206, 207, 215, 211, 213, 214],
            1 => [202, 203, 208, 209, 210, 212],
        ];
        $this->body_id = 'shipyard';
        $this->view_name = 'ingame.shipyard.index';

        return parent::index($request, $player, $objects);
    }

    /**
     * Handles the shipyard page AJAX requests.
     *
     * @param int $id
     * @return Response
     */
    public function ajax(Request $request, PlayerService $player, ObjectService $objects)
    {
        $this->building_type = 'shipyard';

        return $this->ajaxHandler($request, $player, $objects);
    }
}
