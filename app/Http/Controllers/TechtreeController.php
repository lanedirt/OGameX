<?php

namespace OGame\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Services\Objects\Models\BuildingObject;
use OGame\Services\Objects\Models\GameObject;
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
     * @throws Exception
     */
    public function ajax(Request $request, ObjectService $objects, PlayerService $player): View
    {
        $object_id = $request->input('object_id');
        $tab = $request->input('tab');
        // TODO: is this planet still needed?
        $planet = $player->planets->current();

        // Load object
        $object = $objects->getObjectById($object_id);

        if ($tab == 1) {
            return view('ingame.techtree.techtree')->with([
                'object' => $object,
                'object_id' => $object_id,
                'planet' => $planet,
            ]);
        } elseif ($tab == 2) {
            return view('ingame.techtree.techinfo')->with([
                'object' => $object,
                'object_id' => $object_id,
                'planet' => $planet,
                'production_table' => $this->getProductionTable($object, $player, $objects),
                'storage_table' => $this->getStorageTable($object, $player, $objects),
                'rapidfire_table' => $this->getRapidfireTable($object, $objects, $objects),
                'properties_table' => $this->getPropertiesTable($object, $player, $objects),
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
            ]);
        }

        return view('empty');
    }

    /**
     * Returns techtree production table.
     *
     * @param GameObject $object
     * @param PlayerService $player
     * @param ObjectService $objects
     * @return View
     * @throws Exception
     */
    public function getProductionTable(GameObject $object, PlayerService $player, ObjectService $objects): View
    {
        if ($object->type != 'building') {
            return view('empty');
        }

        // Reload object to get the BuildingObject
        $object = $objects->getBuildingObjectByMachineName($object->machine_name);

        $planet = $player->planets->current();
        $current_level = $player->planets->current()->getObjectLevel($object->machine_name);

        $production_table = [];
        if (!empty($object->production)) {
            $production_amount_current_level = $planet->getBuildingProduction($object->machine_name, $current_level)->sum();

            // Create production table array value
            // TODO: add unittest to verify that production calculation is correctly for various buildings.
            $min_level = (($current_level - 2) > 1) ? $current_level - 2 : 1;
            for ($i = $min_level; $i < $min_level + 15; $i++) {
                $production_amount_previous_level = $planet->getBuildingProduction($object->machine_name, $i - 1)->sum();
                $production_amount = $planet->getBuildingProduction($object->machine_name, $i)->sum();

                $production_table[] = [
                    'level' => $i,
                    'production' => $production_amount,
                    'production_difference' => $production_amount - $production_amount_current_level,
                    'production_difference_per_level' => ($i == $current_level) ? 0 : (($i - 1 < $current_level) ? ($production_amount - $production_amount_previous_level) * -1 : $production_amount - $production_amount_previous_level),
                    'energy_balance' => $planet->getBuildingProduction($object->machine_name, $i)->energy->get(),
                    'energy_difference' => ($i == $current_level) ? 0 : ($planet->getBuildingProduction($object->machine_name, $i)->energy->get() - $planet->getBuildingProduction($object->machine_name, $current_level)->energy->get()),
                    'deuterium_consumption' => $planet->getBuildingProduction($object->machine_name, $i)->deuterium->get(),
                    'deuterium_consumption_per_level' => ($i == $current_level) ? 0 : ($planet->getBuildingProduction($object->machine_name, $i)->deuterium->get() - $planet->getBuildingProduction($object->machine_name, $current_level)->deuterium->get()),
                    'protected' => 0,
                ];
            }
        }

        return view('ingame.techtree.info.production')->with([
            'object' => $object,
            'planet' => $planet,
            'production_table' => $production_table,
            'current_level' => $player->planets->current()->getObjectLevel($object->machine_name),
        ]);
    }

    /**
     * Returns techtree storage table.
     *
     * @param GameObject $object
     * @param PlayerService $player
     * @param ObjectService $objects
     * @return View
     * @throws Exception
     */
    public function getStorageTable(GameObject $object, PlayerService $player, ObjectService $objects): View
    {
        if ($object->type != 'building') {
            return view('empty');
        }

        // Reload object to get the BuildingObject
        $object = $objects->getBuildingObjectByMachineName($object->machine_name);

        $planet = $player->planets->current();
        $current_level = $player->planets->current()->getObjectLevel($object->machine_name);

        $storage_table = [];
        if (!empty($object->storage)) {
            $storage_amount_current_level = $planet->getBuildingMaxStorage($object->machine_name, $current_level)->sum();

            // Create storage table array value
            // TODO: add unittest to verify that storage calculation is correctly for various buildings.
            $min_level = (($current_level - 2) > 1) ? $current_level - 2 : 1;
            for ($i = $min_level; $i < $min_level + 15; $i++) {
                $storage_amount_previous_level = $planet->getBuildingMaxStorage($object->machine_name, $i - 1)->sum();
                $storage_amount = $planet->getBuildingMaxStorage($object->machine_name, $i)->sum();

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
            'current_level' => $player->planets->current()->getObjectLevel($object->machine_name),
        ]);
    }

    /**
     * Returns techtree rapidfire table.
     *
     * @param GameObject $object
     * @param ObjectService $objects
     * @return View
     */
    public function getRapidfireTable(GameObject $object, ObjectService $objects): View
    {
        if ($object->type != 'ship' && $object->type != 'defense') {
            return view('empty');
        }

        // Loop through all other objects and see if they have rapidfire against this object
        // if so, create a new array with the rapidfire data same as above.

        // Rapidfire array structure:
        // [
        //     'rapidfire' => GameObjectRapidfire,
        //      'object' => GameObject
        // ]

        $rapidfire_from = [];
        foreach ($objects->getObjects() as $from_object) {
            if (empty($from_object->rapidfire)) {
                continue;
            }

            foreach ($from_object->rapidfire as $rapidfire) {
                if ($rapidfire->object_machine_name == $object->machine_name) {
                    $rapidfire_from[$from_object->id] = [
                        'rapidfire' => $rapidfire,
                        'object' => $from_object,
                    ];
                }
            }
        }

        // Get rapidfire against other objects.
        $rapidfire_against = [];
        if (!empty($object->rapidfire)) {
            foreach ($object->rapidfire as $rapidfire) {
                // Add object name to rapidfire array
                $object = $objects->getObjectByMachineName($rapidfire->object_machine_name);
                $rapidfire_against[$object->id] = [
                    'rapidfire' => $rapidfire,
                    'object' => $object,
                ];
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
     * @param GameObject $object
     * @param PlayerService $player
     * @param ObjectService $objects
     * @return View
     * @throws Exception
     */
    public function getPropertiesTable(GameObject $object, PlayerService $player, ObjectService $objects): View
    {
        if ($object->type != 'ship' && $object->type != 'defense') {
            return view('empty');
        }

        // Add tooltips for object properties
        if (empty($object->properties)) {
            return view('empty');
        }

        // Load object again to get the UnitObject
        $object = $objects->getUnitObjectByMachineName($object->machine_name);

        // Get UnitObject properties...
        $properties = $object->properties;

        $structural_integrity = $properties->structural_integrity->calculate($player->planets->current());
        $shield = $properties->shield->calculate($player->planets->current());
        $attack = $properties->attack->calculate($player->planets->current());
        $speed = $properties->speed->calculate($player->planets->current());
        $capacity = $properties->capacity->calculate($player->planets->current());
        $fuel = $properties->fuel->calculate($player->planets->current());

        // Get actual property values
        $calculatedProperties = new ObjectProperties(
            $structural_integrity,
            $shield,
            $attack,
            $speed,
            $capacity,
            $fuel
        );

        $tooltips = [];
        $tooltips['structural_integrity'] = $this->getPropertyTooltip($calculatedProperties, 'structural_integrity');
        $tooltips['shield'] = $this->getPropertyTooltip($calculatedProperties, 'shield');
        $tooltips['attack'] = $this->getPropertyTooltip($calculatedProperties, 'attack');
        $tooltips['speed'] = $this->getPropertyTooltip($calculatedProperties, 'speed');
        $tooltips['capacity'] = $this->getPropertyTooltip($calculatedProperties, 'capacity');
        $tooltips['fuel'] = $this->getPropertyTooltip($calculatedProperties, 'fuel');

        return view('ingame.techtree.info.properties')->with([
            'object' => $object,
            'tooltips' => $tooltips,
            'properties' => $calculatedProperties,
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
        // TODO: refactor below breakDown and totalValue into statically types models.
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
