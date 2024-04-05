<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use OGame\Http\Traits\IngameTrait;
use OGame\Services\ObjectService;
use OGame\Services\PlayerService;

class TechtreeController extends Controller
{
    use IngameTrait;

    /**
     * Returns techtree ajax content.
     *
     * @param int $id
     * @return Response
     */
    public function ajax(Request $request, ObjectService $objects, PlayerService $player)
    {
        $object_id = $request->input('object_id');
        $tab = $request->input('tab');
        // TODO: is this planet still needed?
        $planet = $player->planets->current();

        // Load object
        $object = $objects->getObjects($object_id);

        if ($tab == 1) {
            return view('ingame.techtree.techtree')->with([
                'object' => $object,
                'object_id' => $object_id,
                'planet' => $planet,
                'current_level' => $player->planets->current()->getObjectLevel($object_id),
            ]);
        } elseif ($tab == 2) {
            return view('ingame.techtree.techinfo')->with([
                'object' => $object,
                'object_id' => $object_id,
                'planet' => $planet,
                'current_level' => $player->planets->current()->getObjectLevel($object_id),
                'production_table' => $this->getProductionTable($object, $player),
                'properties_table' => $this->getPropertiesTable($object),
            ]);
        } elseif ($tab == 3) {
            return view('ingame.techtree.technology')->with([
                'object' => $object,
                'object_id' => $object_id,
                'planet' => $planet,
            ]);
        } elseif ($tab == 4) {
            return view('ingame.techtree.applications')->with([
                'object' => $object,
                'object_id' => $object_id,
                'planet' => $planet,
                'current_level' => $player->planets->current()->getObjectLevel($object_id),
            ]);
        }

        return false;
    }

    /**
     * Returns techtree production table.
     *
     * @param int $id
     * @return Response
     */
    public function getProductionTable($object, PlayerService $player)
    {
        $object_id = $object['id'];
        $planet = $player->planets->current();
        $current_level = $player->planets->current()->getObjectLevel($object['id']);

        $production_table = [];
        if (!empty($object['production'])) {
            $production_amount_current_level = 0;
            foreach ($planet->getBuildingProduction($object['id'], $current_level) as $type => $amount) {
                if ($amount > 0) {
                    $production_amount_current_level = $amount;
                }
            }

            // Create production table array value
            // TODO: add unittest to verify that production calculation is correctly for various buildings.
            $min_level = (($current_level - 2) > 1) ? $current_level - 2 : 1;
            for ($i = $min_level; $i < $min_level + 15; $i++) {
                $production_amount_previous_level = 0;
                foreach ($planet->getBuildingProduction($object['id'], $i - 1) as $type => $amount) {
                    if ($amount > 0) {
                        $production_amount_previous_level = $amount;
                    }
                }

                $production_array = $planet->getBuildingProduction($object['id'], $i);
                $production_amount = 0;
                foreach ($production_array as $type => $amount) {
                    if ($amount > 0) {
                        $production_amount = $amount;
                    }
                }

                $production_table[] = [
                    'level' => $i,
                    'production' => $production_amount,
                    'production_difference' => $production_amount - $production_amount_current_level,
                    'production_difference_per_level' => ($i == $current_level) ? 0 : (($i - 1 < $current_level) ? ($production_amount - $production_amount_previous_level) * -1 : $production_amount - $production_amount_previous_level),
                    'energy_balance' => $planet->getBuildingProduction($object['id'], $i)['energy'],
                    'energy_difference' => ($i == $current_level) ? 0 : ($planet->getBuildingProduction($object['id'], $i)['energy'] - $planet->getBuildingProduction($object['id'], $current_level)['energy']),
                    'deuterium_consumption' => $planet->getBuildingProduction($object['id'], $i)['deuterium'],
                    'deuterium_consumption_per_level' => ($i == $current_level) ? 0 : ($planet->getBuildingProduction($object['id'], $i)['deuterium'] - $planet->getBuildingProduction($object['id'], $current_level)['deuterium']),
                    'protected' => 0,
                ];
            }
        }

        return view('ingame.techtree.info.production')->with([
            'object' => $object,
            'planet' => $planet,
            'production_table' => $production_table,
            'current_level' => $player->planets->current()->getObjectLevel($object_id),
        ]);
    }

    /**
     * Returns techtree properties table.
     *
     * @param int $id
     * @return Response
     */
    public function getPropertiesTable($object)
    {
        // Add tooltips for object properties
        if (empty($object['properties'])) {
            return;
        }

        foreach ($object['properties'] as $property_key => $property_value) {
            $object['tooltips'][$property_key] = $this->getPropertyTooltip($object, $property_key);
        }

        return view('ingame.techtree.info.properties')->with([
            'object' => $object,
        ]);
    }

    /**
     * Returns techtree property tooltip.
     *
     * @param int $id
     * @return Response
     */
    public function getPropertyTooltip($object, $property_key)
    {
        switch ($property_key) {
            case 'structural_integrity':
                $property_name = 'Structural Integrity';
                break;
            case 'shield':
                $property_name = 'Shield Strength';
                break;
            case 'attack':
                $property_name = 'Attack Strength';
                break;
            case 'speed':
                $property_name = 'Speed';
                break;
            case 'capacity':
                $property_name = 'Capacity';
                break;
            case 'fuel':
                $property_name = 'Fuel Consumption';
                break;
        }

        // TODO: add calculation for property values taking into account research.
        $calculated_value = $object['properties'][$property_key];

        return view('ingame.techtree.property_tooltip')->with([
            'property_name' => $property_name,
            'property_value' => $object['properties'][$property_key],
            'calculated_value' => $calculated_value,
        ]);
    }
}
