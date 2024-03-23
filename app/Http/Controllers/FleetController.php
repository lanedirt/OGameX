<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use OGame\Http\Traits\IngameTrait;
use OGame\Services\ObjectService;
use OGame\Services\PlayerService;


class FleetController extends Controller
{
    use IngameTrait;

    /**
     * Shows the fleet index page
     *
     * @param int $id
     * @return Response
     */
    public function index(Request $request, PlayerService $player, ObjectService $objects)
    {
        // Get amount of fleet units on current planet
        $planet = $player->planets->current();

        return view('ingame.fleet.index')->with([
            'planet' => $planet,
            'objects' => $objects->getShipObjects(),
            'shipAmount' => $planet->getFlightShipAmount()
        ]);
    }

    /**
     * Shows the fleet movement page
     *
     * @param int $id
     * @return Response
     */
    public function movement(Request $request)
    {
        return view('ingame.fleet.movement');
    }
}
