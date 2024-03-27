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
        // Define ship ids to include in the fleet screen.
        // 0 = military ships
        // 1 = civil ships
        $this->objects = [
            0 => [204, 205, 206, 207, 215, 211, 213, 214],
            1 => [202, 203, 208, 209, 210],
        ];

        $planet = $player->planets->current();

        $objects_array = $objects->getShipObjects();
        $units = [];
        $count = 0;
        foreach ($this->objects as $key_row => $objects_row) {
            $buildings[$key_row] = [];

            foreach ($objects_row as $object_id) {
                $count++;

                // Get current level of building
                $amount = $planet->getObjectAmount($object_id);

                // Check requirements of this building
                $requirements_met = $objects->objectRequirementsMet($object_id, $planet, $player);

                // Check if the current planet has enough resources to build this building.
                $enough_resources = $planet->hasResources($objects->getObjectPrice($object_id, $planet));

                $units[$key_row][$object_id] = array_merge($objects_array[$object_id], [
                    'amount' => $amount,
                    'requirements_met' => $requirements_met,
                    'count' => $count,
                    'enough_resources' => $enough_resources,
                    'currently_building' => (!empty($build_active['id']) && $build_active['object']['id'] == $object_id),
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
     * @param int $id
     * @return Response
     */
    public function movement(Request $request)
    {
        return view('ingame.fleet.movement');
    }
}
