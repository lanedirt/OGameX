<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Services\Objects\ObjectService;
use OGame\Services\Objects\Properties\Models\ObjectProperties;
use OGame\Services\PlayerService;

class TechtreeController extends OGameController
{
    /**
     * Returns techtree ajax content.
     *
     * @param Request $request
     * @param ObjectService $objects
     * @param PlayerService $player
     * @return View
     */
    public function ajax(Request $request, ObjectService $objects, PlayerService $player): View
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
                'storage_table' => $this->getStorageTable($object, $player),
                'rapidfire_table' => $this->getRapidfireTable($object, $objects),
                'properties_table' => $this->getPropertiesTable($object, $player),
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

        return view('empty');
    }

    /**
     * Returns techtree production table.
     *
     * @param array $object
     * @param PlayerService $player
     * @return View
     */
    public function getProductionTable(array $object, PlayerService $player): View
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
     * Returns techtree storage table.
     *
     * @param $object
     * @param PlayerService $player
     * @return View
     */
    public function getStorageTable($object, PlayerService $player): View
    {
        $object_id = $object['id'];
        $planet = $player->planets->current();
        $current_level = $player->planets->current()->getObjectLevel($object['id']);

        $storage_table = [];
        if (!empty($object['storage'])) {
            $storage_amount_current_level = 0;
            foreach ($planet->getBuildingMaxStorage($object['id'], $current_level) as $type => $amount) {
                if ($amount > 0) {
                    $storage_amount_current_level = $amount;
                }
            }

            // Create storage table array value
            // TODO: add unittest to verify that storage calculation is correctly for various buildings.
            $min_level = (($current_level - 2) > 1) ? $current_level - 2 : 1;
            for ($i = $min_level; $i < $min_level + 15; $i++) {
                $storage_amount_previous_level = 0;
                foreach ($planet->getBuildingMaxStorage($object['id'], $i - 1) as $type => $amount) {
                    if ($amount > 0) {
                        $storage_amount_previous_level = $amount;
                    }
                }

                $storage_array = $planet->getBuildingMaxStorage($object['id'], $i);
                $storage_amount = 0;
                foreach ($storage_array as $type => $amount) {
                    if ($amount > 0) {
                        $storage_amount = $amount;
                    }
                }

                $storage_table[] = [
                    'level' => $i,
                    'storage' => $storage_amount,
                    'storage_difference' => $storage_amount - $storage_amount_current_level,
                    'storage_difference_per_level' => ($i == $current_level) ? 0 : (($i - 1 < $current_level) ? ($storage_amount - $storage_amount_previous_level) * -1 : $storage_amount - $storage_amount_previous_level),
                    'protected' => 0,
                ];
            }
        }

        return view('ingame.techtree.info.storage')->with([
            'object' => $object,
            'planet' => $planet,
            'storage_table' => $storage_table,
            'current_level' => $player->planets->current()->getObjectLevel($object_id),
        ]);
    }

    /**
     * Returns techtree rapidfire table.
     *
     * @param array $object
     * @param ObjectService $objects
     * @return View
     */
    public function getRapidfireTable(array $object, ObjectService $objects): View
    {
        // Loop through all other objects and see if they have rapidfire against this object
        // if so, create a new array with the rapidfire data same as above.
        $rapidfire_from = [];
        foreach ($objects->getObjects() as $from_object) {
            if (empty($from_object['rapidfire'])) {
                continue;
            }

            foreach ($from_object['rapidfire'] as $target_objectid => $data) {
                if ($target_objectid == $object['id']) {
                    $rapidfire_from[$from_object['id']] = $data;
                    $rapidfire_from[$from_object['id']]['object'] = $from_object;
                }
            }
        }

        // Get rapidfire against other objects.
        $rapidfire_against = [];
        if (!empty($object['rapidfire'])) {
            foreach ($object['rapidfire'] as $target_objectid => $data) {
                // Add objefct name to rapidfire array
                $target_object = $objects->getObjects($target_objectid);
                $rapidfire_against[$target_objectid] = $data;
                $rapidfire_against[$target_objectid]['object'] = $target_object;
            }
        }

        return view('ingame.techtree.info.rapidfire')->with([
            'object' => $object,
            'rapidfire_from' => $rapidfire_from,
            'rapidfire_against' => $rapidfire_against,
        ]);
    }

    /**
     * Returns techtree properties table.
     *
     * @param array $object
     * @param PlayerService $player
     * @return View
     */
    public function getPropertiesTable(array $object, PlayerService $player): View
    {
        // Add tooltips for object properties
        if (empty($object['properties'])) {
            return view('empty');
        }

        // Get actual property values
        $properties = $player->planets->current()->getObjectProperties($object['id']);

        foreach ($object['properties'] as $property_key => $property_value) {
            $object['tooltips'][$property_key] = $this->getPropertyTooltip($properties, $property_key);
        }

        return view('ingame.techtree.info.properties')->with([
            'object' => $object,
            'properties' => $properties,
        ]);
    }

    /**
     * Returns techtree property tooltip.
     *
     * @param ObjectProperties $properties
     * @param string $property_key
     * @return View
     */
    public function getPropertyTooltip(ObjectProperties $properties, string $property_key): View
    {
        $property_name = 'N/a';
        $property_breakdown = [];
        $property_value = 0;
        switch ($property_key) {
            case 'structural_integrity':
                $property_name = 'Structural Integrity';
                $property_breakdown = $properties->structuralIntegrity->breakdown;
                $property_value = $properties->structuralIntegrity->totalValue;
                break;
            case 'shield':
                $property_name = 'Shield Strength';
                $property_breakdown = $properties->shield->breakdown;
                $property_value = $properties->shield->totalValue;
                break;
            case 'attack':
                $property_name = 'Attack Strength';
                $property_breakdown = $properties->attack->breakdown;
                $property_value = $properties->attack->totalValue;
                break;
            case 'speed':
                $property_name = 'Speed';
                $property_breakdown = $properties->speed->breakdown;
                $property_value = $properties->speed->totalValue;
                break;
            case 'capacity':
                $property_name = 'Capacity';
                $property_breakdown = $properties->capacity->breakdown;
                $property_value = $properties->capacity->totalValue;
                break;
            case 'fuel':
                $property_name = 'Fuel Consumption';
                $property_breakdown = $properties->fuel->breakdown;
                $property_value = $properties->fuel->totalValue;
                break;
        }

        return view('ingame.techtree.info.property_tooltip')->with([
            'property_name' => $property_name,
            'property_breakdown' => $property_breakdown,
            'property_value' => $property_value,
        ]);
    }
}
