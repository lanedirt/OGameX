<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use OGame\Http\Controllers\Abstracts\AbstractUnitsController;
use OGame\Services\Objects\ObjectService;
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
     * @param int $id
     * @return Response
     */
    public function index(Request $request, PlayerService $player, ObjectService $objects)
    {
        // Prepare custom properties
        $this->objects = [
            0 => [401, 402, 403, 404, 405, 406, 407, 408, 502, 503],
        ];
        $this->body_id = 'defense';
        $this->view_name = 'ingame.defense.index';

        return parent::index($request, $player, $objects);
    }

    /**
     * Handles the research page AJAX requests.
     *
     * @param int $id
     * @return Response
     */
    public function ajax(Request $request, PlayerService $player, ObjectService $objects)
    {
        $this->building_type = 'defense';

        return $this->ajaxHandler($request, $player, $objects);
    }
}
