<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use OGame\Http\Controllers\Abstracts\AbstractBuildingsController;
use OGame\Services\BuildingQueueService;
use OGame\Services\Objects\ObjectService;
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
     * @param ObjectService $objects
     * @return View
     */
    public function index(Request $request, PlayerService $player, ObjectService $objects): View
    {
        $this->setBodyId('station');

        // Prepare custom properties
        $this->header_filename_objects = [14, 21, 31, 34]; // Building ID's that make up the header filename.
        $this->objects = [
            0 => [14, 21, 31, 34, 44, 15, 33, 36],
        ];
        $this->view_name = 'ingame.facilities.index';

        return parent::index($request, $player, $objects);
    }

    /**
     * Handles the facilities page AJAX requests.
     *
     * @param int $id
     * @return Response
     */
    public function ajax(Request $request, PlayerService $player, ObjectService $objects)
    {
        $this->building_type = 'station';

        return $this->ajaxHandler($request, $player, $objects);
    }
}
