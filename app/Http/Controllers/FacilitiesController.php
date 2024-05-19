<?php

namespace OGame\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Http\Controllers\Abstracts\AbstractBuildingsController;
use OGame\Services\BuildingQueueService;
use OGame\Services\ObjectService;
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
     * @throws Exception
     */
    public function index(Request $request, PlayerService $player, ObjectService $objects): View
    {
        $this->setBodyId('station');

        // Prepare custom properties
        $this->header_filename_objects = [14, 21, 31, 34]; // Building ID's that make up the header filename.
        $this->objects = [
            ['robot_factory', 'shipyard', 'research_lab', 'alliance_depot', 'missile_silo', 'nano_factory', 'terraformer', 'space_dock'],
        ];
        $this->view_name = 'ingame.facilities.index';

        return parent::index($request, $player, $objects);
    }

    /**
     * Handles the facilities page AJAX requests.
     *
     * @param Request $request
     * @param PlayerService $player
     * @param ObjectService $objects
     * @return JsonResponse
     * @throws Exception
     */
    public function ajax(Request $request, PlayerService $player, ObjectService $objects): JsonResponse
    {
        return $this->ajaxHandler($request, $player, $objects);
    }
}
