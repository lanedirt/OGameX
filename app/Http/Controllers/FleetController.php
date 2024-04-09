<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Http\Traits\IngameTrait;
use OGame\Services\Objects\ObjectService;
use OGame\Services\PlayerService;

class FleetController extends Controller
{
    use IngameTrait;

    /**
     * Shows the fleet index page
     *
     * @param Request $request
     * @param PlayerService $player
     * @param ObjectService $objects
     * @return View
     */
    public function index(Request $request, PlayerService $player, ObjectService $objects) : View
    {
        // Define ship ids to include in the fleet screen.
        // 0 = military ships
        // 1 = civil ships
        $screen_objects = [
            0 => [204, 205, 206, 207, 215, 211, 213, 214],
            1 => [202, 203, 208, 209, 210],
        ];

        $planet = $player->planets->current();

        $objects_array = $objects->getShipObjects();
        $units = [];
        $count = 0;

        foreach ($screen_objects as $key_row => $objects_row) {
            foreach ($objects_row as $object_id) {
                $count++;

                // Get current level of building
                $amount = $planet->getObjectAmount($object_id);

                $units[$key_row][$object_id] = array_merge($objects_array[$object_id], [
                    'amount' => $amount,
                    'count' => $count,
                ]);
            }
        }

        return view('ingame.fleet.index')->with([
            'planet' => $planet,
            'units' => $units,
            'objects' => $objects->getShipObjects(),
            'shipAmount' => $planet->getFlightShipAmount()
        ]);
    }

    /**
     * Shows the fleet movement page
     *
     * @return View
     */
    public function movement() : View
    {
        return view('ingame.fleet.movement');
    }
}
