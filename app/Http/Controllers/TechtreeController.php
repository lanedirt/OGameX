<?php

namespace OGame\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\GameObjects\Models\Abstracts\GameObject;
use OGame\GameObjects\Models\Calculations\CalculationType;
use OGame\GameObjects\Models\Enums\GameObjectType;
use OGame\GameObjects\Models\Techtree\TechtreeRequiredBy;
use OGame\GameObjects\Models\Techtree\TechtreeRequirement;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;

class TechtreeController extends OGameController
{
    /**
     * Returns tech tree ajax content.
     *
     * @param Request $request
     * @param PlayerService $player
     * @return View
     * @throws Exception
     */
    public function ajax(Request $request, PlayerService $player): View
    {
        $object_id = (int)$request->input('object_id');
        $tab = (int)$request->input('tab');

        // Load object
        $object = ObjectService::getObjectById($object_id);

        if ($tab === 1) {
            $techtree = $this->getTechtreeArray($object, $player->planets->current());

            // Get the amount of columns in the techtree by getting the highest column number.
            $columns = array_column($techtree, 'column');
            $amount_of_columns = !empty($columns) ? max($columns) + 1 : 1;

            // Create techtree array with all unique items for a specific depth as a sub-array.
            // This makes it easier to render the tech tree in the template.
            $techtree_by_depth = [];
            $items_added = [];
            foreach ($techtree as $requirement) {
                $unique_key = $requirement->gameObject->id . '|' . $requirement->levelRequired;
                if (!isset($items_added[$unique_key])) {
                    $items_added[$unique_key] = true;
                    $techtree_by_depth[$requirement->depth][$requirement->column] = $requirement;
                }
            }

            // Create unique requirements array where every requirement with a specific level
            // only appears once. This is used to create the endpoints list.
            $techtree_unique = [];
            foreach ($techtree as $requirement) {
                if (!isset($techtree_unique[$requirement->gameObject->id . $requirement->levelRequired])) {
                    $techtree_unique[$requirement->gameObject->id . $requirement->levelRequired] = $requirement;
                }
            }

            return view('ingame.techtree.techtree')->with([
                'object' => $object,
                'amount_of_columns' => $amount_of_columns,
                'techtree' => $techtree,
                'techtree_unique' => $techtree_unique,
                'techtree_by_depth' => $techtree_by_depth,
            ]);
        } elseif ($tab === 2) {
            return view('ingame.techtree.techinfo')->with([
                'object' => $object,
                'production_table' => $this->getProductionTable($object, $player),
                'storage_table' => $this->getStorageTable($object, $player),
                'rapidfire_table' => $this->getRapidfireTable($object),
                'properties_table' => $this->getPropertiesTable($object, $player),
                'plasma_table' => $this->getPlasmaTable($object, $player),
                'astrophysics_table' => $this->getAstrophysicsTable($object, $player),
            ]);
        } elseif ($tab === 3) {
            return view('ingame.techtree.technology')->with([
                'object' => $object,
            ]);
        } elseif ($tab === 4) {
            return view('ingame.techtree.applications')->with([
                'object' => $object,
                'required_by' => $this->getRequiredBy($object, $player->planets->current())
            ]);
        }

        return view('empty');
    }

    /**
     * Returns tech tree production table.
     *
     * @param GameObject $object
     * @param PlayerService $player
     * @return View
     * @throws Exception
     */
    public function getProductionTable(GameObject $object, PlayerService $player): View
    {
        if ($object->type !== GameObjectType::Building) {
            return view('empty');
        }

        // Reload object to get the BuildingObject
        $object = ObjectService::getBuildingObjectByMachineName($object->machine_name);

        $planet = $player->planets->current();
        $current_level = $player->planets->current()->getObjectLevel($object->machine_name);

        $production_table = [];
        if (!empty($object->production)) {
            $production_amount_current_level = $planet->getObjectProduction($object->machine_name, $current_level, true)->sum();

            // Create production table array value
            // TODO: add unittest to verify that production calculation is correctly for various buildings.
            $min_level = (($current_level - 2) > 1) ? $current_level - 2 : 1;
            for ($i = $min_level; $i < $min_level + 15; $i++) {
                $production_amount_previous_level = $planet->getObjectProduction($object->machine_name, $i - 1, true)->sum();
                $production_amount = $planet->getObjectProduction($object->machine_name, $i, true)->sum();

                $production_table[] = [
                    'level' => $i,
                    'production' => $production_amount,
                    'production_difference' => $production_amount - $production_amount_current_level,
                    'production_difference_per_level' => ($i === $current_level) ? 0 : (($i - 1 < $current_level) ? ($production_amount - $production_amount_previous_level) * -1 : $production_amount - $production_amount_previous_level),
                    'energy_balance' => $planet->getObjectProduction($object->machine_name, $i, true)->energy->get(),
                    'energy_difference' => ($i === $current_level) ? 0 : ($planet->getObjectProduction($object->machine_name, $i, true)->energy->get() - $planet->getObjectProduction($object->machine_name, $current_level, true)->energy->get()),
                    'deuterium_consumption' => $planet->getObjectProduction($object->machine_name, $i, true)->deuterium->get(),
                    'deuterium_consumption_per_level' => ($i === $current_level) ? 0 : ($planet->getObjectProduction($object->machine_name, $i, true)->deuterium->get() - $planet->getObjectProduction($object->machine_name, $current_level, true)->deuterium->get()),
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
     * Returns tech tree storage table.
     *
     * @param GameObject $object
     * @param PlayerService $player
     * @return View
     * @throws Exception
     */
    public function getStorageTable(GameObject $object, PlayerService $player): View
    {
        if ($object->type !== GameObjectType::Building) {
            return view('empty');
        }

        // Reload object to get the BuildingObject
        $object = ObjectService::getBuildingObjectByMachineName($object->machine_name);

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
                    'storage_difference_per_level' => ($i === $current_level) ? 0 : (($i - 1 < $current_level) ? ($storage_amount - $storage_amount_previous_level) * -1 : $storage_amount - $storage_amount_previous_level),
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
     * Returns tech tree rapidfire table.
     *
     * @param GameObject $object
     * @return View
     */
    public function getRapidfireTable(GameObject $object): View
    {
        if ($object->type !== GameObjectType::Ship && $object->type !== GameObjectType::Defense) {
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
        foreach (ObjectService::getObjects() as $from_object) {
            if (empty($from_object->rapidfire)) {
                continue;
            }

            foreach ($from_object->rapidfire as $rapidfire) {
                if ($rapidfire->object_machine_name === $object->machine_name) {
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
                $object = ObjectService::getObjectByMachineName($rapidfire->object_machine_name);
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
     * Returns tech tree properties table.
     *
     * @param GameObject $object
     * @param PlayerService $player
     * @return View
     * @throws Exception
     */
    public function getPropertiesTable(GameObject $object, PlayerService $player): View
    {
        if ($object->type !== GameObjectType::Ship && $object->type !== GameObjectType::Defense) {
            return view('empty');
        }

        // Add tooltips for object properties
        if (empty($object->properties)) {
            return view('empty');
        }

        // Load object again to get the UnitObject
        $object = ObjectService::getUnitObjectByMachineName($object->machine_name);
        // Get UnitObject properties...
        $properties = $object->properties;

        $structural_integrity = $properties->structural_integrity->calculate($player);
        $shield = $properties->shield->calculate($player);
        $attack = $properties->attack->calculate($player);
        $speed = $properties->speed->calculate($player);
        $capacity = $properties->capacity->calculate($player);
        $fuel = $properties->fuel->calculate($player);

        $properties_array = [];
        $properties_array['structural_integrity'] = $structural_integrity->totalValue;
        $properties_array['shield'] = $shield->totalValue;
        $properties_array['attack'] = $attack->totalValue;

        if ($object->type !== GameObjectType::Defense) {
            $properties_array['speed'] = $speed->totalValue;
            $properties_array['capacity'] = $capacity->totalValue;
            $properties_array['fuel'] = $fuel->totalValue;
        }

        $tooltips_array = [];
        $tooltips_array['structural_integrity'] = $this->getPropertyTooltip($properties->structural_integrity->name, $structural_integrity->breakdown, $structural_integrity->totalValue);
        $tooltips_array['shield'] = $this->getPropertyTooltip($properties->shield->name, $shield->breakdown, $structural_integrity->totalValue);
        $tooltips_array['attack'] = $this->getPropertyTooltip($properties->attack->name, $attack->breakdown, $structural_integrity->totalValue);

        if ($object->type !== GameObjectType::Defense) {
            $tooltips_array['speed'] = $this->getPropertyTooltip($properties->speed->name, $speed->breakdown, $structural_integrity->totalValue);
            $tooltips_array['capacity'] = $this->getPropertyTooltip($properties->capacity->name, $capacity->breakdown, $structural_integrity->totalValue);
            $tooltips_array['fuel'] = $this->getPropertyTooltip($properties->fuel->name, $fuel->breakdown, $structural_integrity->totalValue);
        }

        return view('ingame.techtree.info.properties')->with([
            'object' => $object,
            'tooltips' => $tooltips_array,
            'properties' => $properties_array,
        ]);
    }

    /**
     * Returns techtree property tooltip.
     *
     * @param string $name
     * @param array<string,array<int, array<string, float|int|string>>|float|int> $breakdown
     * @param int $value
     * @return View
     */
    public function getPropertyTooltip(string $name, array $breakdown, int $value): View
    {
        return view('ingame.techtree.info.property_tooltip')->with([
            'property_name' => $name,
            'property_breakdown' => $breakdown,
            'property_value' => $value,
        ]);
    }

    /**
     * Returns techtree plasma table.
     *
     * @param GameObject $object
     * @param PlayerService $player
     * @return View
     * @throws Exception
     */
    public function getPlasmaTable(GameObject $object, PlayerService $player): View
    {
        if ($object->type !== GameObjectType::Research || $object->machine_name !== 'plasma_technology') {
            return view('empty');
        }

        $current_level = $player->getResearchLevel($object->machine_name);
        $plasma_table = [];
        $min_level = (($current_level - 2) > 1) ? $current_level - 2 : 1;

        for ($i = $min_level; $i < $min_level + 15; $i++) {
            $plasma_table[] = [
                'level' => $i,
                'metal_bonus' => $i,
                'crystal_bonus' => $i * 0.66,
                'deuterium_bonus' => $i * 0.33
            ];
        }

        return view('ingame.techtree.info.plasma')->with([
            'object' => $object,
            'plasma_table' => $plasma_table,
            'current_level' => $current_level,
        ]);
    }

    /**
     * Returns techtree astrophysics.
     *
     * @param GameObject $object
     * @param PlayerService $player
     * @return View
     * @throws Exception
     */
    public function getAstrophysicsTable(GameObject $object, PlayerService $player): View
    {
        if ($object->type !== GameObjectType::Research || $object->machine_name !== 'astrophysics') {
            return view('empty');
        }

        $current_level = $player->getResearchLevel($object->machine_name);
        $astrophysics_table = [];
        $min_level = (($current_level - 2) > 1) ? $current_level - 2 : 1;

        for ($i = $min_level; $i < $min_level + 15; $i++) {
            $astrophysics_table[] = [
                'level' => $i,
                'max_colonies' => $object->performCalculation(CalculationType::MAX_COLONIES, $i),
                'max_expedition_slots' => $object->performCalculation(CalculationType::MAX_EXPEDITION_SLOTS, $i),
            ];
        }

        return view('ingame.techtree.info.astrophysics')->with([
            'object' => $object,
            'astrophysics_table' => $astrophysics_table,
            'current_level' => $current_level,
        ]);
    }

    /**
     * Returns the objects that require the given object including status if requirements have been met for current
     * player and planet.
     *
     * @param GameObject $object
     * @param PlanetService $planet
     * @return array<TechtreeRequiredBy>
     */
    private function getRequiredBy(GameObject $object, PlanetService $planet): array
    {
        $all_objects = ObjectService::getObjects();
        $required_by = [];

        $require_objects = array_filter($all_objects, function ($a_object) use ($object) {
            $has_object_required = false;
            foreach ($a_object->requirements as $requirement) {
                if ($requirement->object_machine_name === $object->machine_name) {
                    $has_object_required = true;
                }
            }

            return $has_object_required;
        });

        foreach ($require_objects as $r_object) {
            $required_by[] = new TechtreeRequiredBy($r_object, ObjectService::objectRequirementsMet($r_object->machine_name, $planet));
        }

        return $required_by;
    }

    /**
     * Returns techtree array which includes all (recursive) objects that the given object depends on.
     * This resulting array is used to render the full tech tree.
     *
     * @param GameObject $object
     * @param PlanetService $planet
     * @param int $depth
     * @param int $column
     * @param TechtreeRequirement|null $parent
     * @return array<TechtreeRequirement>
     */
    private function getTechtreeArray(GameObject $object, PlanetService $planet, int $depth = 1, int &$column = 0, TechtreeRequirement|null $parent = null): array
    {
        $techtree_array = [];

        // If we're at the beginning, add current object to requirement array as depth 0 (root)
        if ($depth === 1) {
            if ($object->type === GameObjectType::Research) {
                $object_level = $planet->getPlayer()->getResearchLevel($object->machine_name);
            } else {
                $object_level = $planet->getObjectLevel($object->machine_name);
            }
            $requirement = new TechtreeRequirement(0, 0, null, $object, 0, $object_level);
            $techtree_array[] = $requirement;

            // Set current object as parent for the loop below.
            $parent = $requirement;
        }

        foreach ($object->requirements as $requirement) {
            $current_column = $column++;  // Increment column for each requirement
            $object = ObjectService::getObjectByMachineName($requirement->object_machine_name);

            if ($object->type === GameObjectType::Research) {
                $object_level = $planet->getPlayer()->getResearchLevel($object->machine_name);
            } else {
                $object_level = $planet->getObjectLevel($object->machine_name);
            }

            $requirement = new TechtreeRequirement($depth, $current_column, $parent, $object, $requirement->level, $object_level);
            $techtree_array[] = $requirement;

            // Get requirements for this object recursively
            $child_requirements = $this->getTechtreeArray($object, $planet, $depth + 1, $column, $requirement);
            $techtree_array = array_merge($techtree_array, $child_requirements);
        }

        return $techtree_array;
    }
}
