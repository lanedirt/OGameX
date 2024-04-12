<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Http\Traits\IngameTrait;
use OGame\Services\Objects\ObjectService;
use OGame\Services\PlayerService;
use OGame\ViewModels\UnitViewModel;

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
     * @throws \Exception
     */
    public function index(Request $request, PlayerService $player, ObjectService $objects) : View
    {
        // Define ship ids to include in the fleet screen.
        // 0 = military ships
        // 1 = civil ships
        $screen_objects = [
            0 => ['light_fighter', 'heavy_fighter', 'cruiser', 'battle_ship', 'battlecruiser', 'bomber', 'destroyer', 'deathstar'],
            1 => ['small_cargo', 'large_cargo', 'colony_ship', 'recycler', 'espionage_probe'],
        ];

        $planet = $player->planets->current();

        $units = [];
        $count = 0;

        foreach ($screen_objects as $key_row => $objects_row) {
            foreach ($objects_row as $object_machine_name) {
                $count++;

                $object = $objects->getUnitObjectByMachineName($object_machine_name);

                // Get current level of building
                $amount = $planet->getObjectAmount($object_machine_name);

                $view_model = new UnitViewModel();
                $view_model->object = $object;
                $view_model->count = $count;
                $view_model->amount = $amount;

                $units[$key_row][$object->id] = $view_model;
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
