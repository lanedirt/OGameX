<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
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
     * @param Request $request
     * @param PlayerService $player
     * @param ObjectService $objects
     * @return View
     */
    public function index(Request $request, PlayerService $player, ObjectService $objects) : View
    {
        $this->setBodyId('defense');

        // Prepare custom properties
        $this->objects = [
            0 => [401, 402, 403, 404, 405, 406, 407, 408, 502, 503],
        ];
        $this->view_name = 'ingame.defense.index';

        return parent::index($request, $player, $objects);
    }

    /**
     * Handles the research page AJAX requests.
     *
     * @param Request $request
     * @param PlayerService $player
     * @param ObjectService $objects
     * @return View
     * @throws \Exception
     */
    public function ajax(Request $request, PlayerService $player, ObjectService $objects) : View
    {
        return $this->ajaxHandler($request, $player, $objects);
    }
}
