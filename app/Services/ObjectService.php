<?php

namespace OGame\Services;

use Exception;
use OGame\GameObjects\BuildingObjects;
use OGame\GameObjects\CivilShipObjects;
use OGame\GameObjects\DefenseObjects;
use OGame\GameObjects\MilitaryShipObjects;
use OGame\GameObjects\Models\Abstracts\GameObject;
use OGame\GameObjects\Models\BuildingObject;
use OGame\GameObjects\Models\DefenseObject;
use OGame\GameObjects\Models\Enums\GameObjectType;
use OGame\GameObjects\Models\Fields\GameObjectRequirement;
use OGame\GameObjects\Models\ResearchObject;
use OGame\GameObjects\Models\ShipObject;
use OGame\GameObjects\Models\StationObject;
use OGame\GameObjects\Models\UnitObject;
use OGame\GameObjects\ResearchObjects;
use OGame\GameObjects\StationObjects;
use OGame\Models\Resources;
use RuntimeException;

/**
 * Static class ObjectService.
 *
 * Contains static helper methods to retrieve information about game objects such as buildings, research,
 * ships, defense etc.
 *
 * @package OGame\Services
 */
class ObjectService
{
    /**
     * Get all objects.
     *
     * @return array<GameObject>
     */
    public static function getObjects(): array
    {
        return [...BuildingObjects::get(), ...StationObjects::get(), ...ResearchObjects::get(),
                ...MilitaryShipObjects::get(), ...CivilShipObjects::get(), ...DefenseObjects::get()];
    }

    /**
     * Get all buildings.
     *
     * @return array<BuildingObject>
     */
    public static function getBuildingObjects(): array
    {
        return BuildingObjects::get();
    }

    /**
     * Get all buildings.
     *
     * @return array<StationObject>
     */
    public static function getStationObjects(): array
    {
        return StationObjects::get();
    }

    /**
     * Get all buildings.
     *
     * @return array<ResearchObject>
     */
    public static function getResearchObjects(): array
    {
        return ResearchObjects::get();
    }

    /**
     * Get all unit objects.
     *
     * @return array<UnitObject>
     */
    public static function getUnitObjects(): array
    {
        return [...MilitaryShipObjects::get(), ...CivilShipObjects::get(), ...DefenseObjects::get()];
    }

    /**
     * Get all ship objects.
     *
     * @return array<ShipObject>
     */
    public static function getShipObjects(): array
    {
        return [...MilitaryShipObjects::get(), ...CivilShipObjects::get()];
    }

    /**
     * Get all defense objects.
     *
     * @return array<DefenseObject>
     */
    public static function getDefenseObjects(): array
    {
        return DefenseObjects::get();
    }

    /**
     * Get all military ship objects.
     *
     * @return array<ShipObject>
     */
    public static function getMilitaryShipObjects(): array
    {
        return MilitaryShipObjects::get();
    }

    /**
     * Get all civil ship objects.
     *
     * @return array<ShipObject>
     */
    public static function getCivilShipObjects(): array
    {
        return CivilShipObjects::get();
    }

    /**
     * Get specific building.
     *
     * @param string $machine_name
     * @return BuildingObject
     */
    public static function getBuildingObjectByMachineName(string $machine_name): BuildingObject
    {
        // Loop through all buildings and return the one with the matching UID
        foreach (BuildingObjects::get() as $building) {
            if ($building->machine_name === $machine_name) {
                return $building;
            }
        }

        throw new RuntimeException('Building not found');
    }

    /**
     * Get specific ship.
     *
     * @param string $machine_name
     * @return ShipObject
     */
    public static function getShipObjectByMachineName(string $machine_name): ShipObject
    {
        $shipObjects = [...MilitaryShipObjects::get(), ...CivilShipObjects::get()];
        // Loop through all buildings and return the one with the matching UID
        foreach ($shipObjects as $ship) {
            if ($ship->machine_name === $machine_name) {
                return $ship;
            }
        }

        throw new RuntimeException('Ship object not found with machine name: ' . $machine_name);
    }

    /**
     * Get specific game object.
     *
     * @param int $object_id
     * @return GameObject
     */
    public static function getObjectById(int $object_id): GameObject
    {
        $allObjects = [...BuildingObjects::get(), ...StationObjects::get(), ...ResearchObjects::get(),
                       ...MilitaryShipObjects::get(), ...CivilShipObjects::get(), ...DefenseObjects::get()];

        // Loop through all buildings and return the one with the matching UID
        foreach ($allObjects as $object) {
            if ($object->id == $object_id) {
                return $object;
            }
        }

        throw new RuntimeException('Game object not found with ID: ' . $object_id);
    }

    /**
     * Get specific game object.
     *
     * @param string $machine_name
     * @return GameObject
     */
    public static function getObjectByMachineName(string $machine_name): GameObject
    {
        $allObjects = [...BuildingObjects::get(), ...StationObjects::get(), ...ResearchObjects::get(),
                       ...MilitaryShipObjects::get(), ...CivilShipObjects::get(), ...DefenseObjects::get()];

        // Loop through all buildings and return the one with the matching UID
        foreach ($allObjects as $object) {
            if ($object->machine_name == $machine_name) {
                return $object;
            }
        }

        throw new RuntimeException('Game object not found with machine name: ' . $machine_name);
    }

    /**
     * Get specific research object.
     *
     * @param string $machine_name
     * @return ResearchObject
     */
    public static function getResearchObjectByMachineName(string $machine_name): ResearchObject
    {
        // Loop through all buildings and return the one with the matching UID
        $allObjects = ResearchObjects::get();
        foreach ($allObjects as $object) {
            if ($object->machine_name === $machine_name) {
                return $object;
            }
        }

        throw new RuntimeException('Research object not found with machine name: ' . $machine_name);
    }

    /**
     * Get specific research object.
     *
     * @param int $object_id
     * @return ResearchObject
     */
    public static function getResearchObjectById(int $object_id): ResearchObject
    {
        // Loop through all buildings and return the one with the matching UID
        $allObjects = ResearchObjects::get();
        foreach ($allObjects as $object) {
            if ($object->id === $object_id) {
                return $object;
            }
        }

        throw new RuntimeException('Unit object not found with object ID: ' . $object_id);
    }

    /**
     * Get specific unit object.
     *
     * @param int $object_id
     * @return UnitObject
     */
    public static function getUnitObjectById(int $object_id): UnitObject
    {
        $allObjects = [...MilitaryShipObjects::get(), ...CivilShipObjects::get(), ...DefenseObjects::get()];
        foreach ($allObjects as $object) {
            if ($object->id === $object_id) {
                return $object;
            }
        }

        throw new RuntimeException('Unit object not found with object ID: ' . $object_id);
    }

    /**
     * Get specific unit object.
     *
     * @param string $machine_name
     * @return UnitObject
     */
    public static function getUnitObjectByMachineName(string $machine_name): UnitObject
    {
        $allObjects = [...MilitaryShipObjects::get(), ...CivilShipObjects::get(), ...DefenseObjects::get()];

        // Loop through all buildings and return the one with the matching UID
        foreach ($allObjects as $object) {
            if ($object->machine_name === $machine_name) {
                return $object;
            }
        }

        throw new RuntimeException('Unit object not found with machine name: ' . $machine_name);
    }

    /**
     * Get all game objects that have production values.
     *
     * @return array<GameObject>
     */
    public static function getGameObjectsWithProduction(): array
    {
        $return = array();

        foreach (self::getObjects() as $value) {
            if (!empty(($value->production))) {
                $return[] = $value;
            }
        }

        return $return;
    }

    /**
     * Get all game objects that have production values.
     *
     * @param string $machine_name
     * @return GameObject
     */
    public static function getGameObjectsWithProductionByMachineName(string $machine_name): GameObject
    {
        foreach (self::getObjects() as $object) {
            if ($object->machine_name === $machine_name && !empty(($object->production))) {
                return $object;
            }
        }

        throw new RuntimeException('Game object not found with production value for machine name: ' . $machine_name);
    }

    /**
     * Get all buildings that have storage values.
     *
     * @return array<BuildingObject>
     */
    public static function getBuildingObjectsWithStorage(): array
    {
        $return = array();

        foreach (BuildingObjects::get() as $value) {
            if (!empty(($value->storage))) {
                $return[] = $value;
            }
        }

        return $return;
    }

    /**
     * Check if object requirements are met (for building it).
     *
     * @param string $machine_name
     * @param PlanetService $planet
     * @return bool
     */
    public static function objectRequirementsMet(string $machine_name, PlanetService $planet): bool
    {
        $object = self::getObjectByMachineName($machine_name);
        return count(self::filterCompletedRequirements($object->requirements, $planet)) === 0;
    }

    /**
     * Check if object requirements are met (for building it) with previous levels completed.
     *
     * @param string $machine_name
     * @param PlanetService $planet
     * @param int $target_level
     * @return bool
     */
    public static function objectRequirementsWithLevelsMet(string $machine_name, int $target_level, PlanetService $planet): bool
    {
        try {
            $object = self::getObjectByMachineName($machine_name);

            // Check that the object's previous level exists.
            if ($target_level) {
                if (!self::hasPreviousLevels($target_level, $object, $planet)) {
                    return false;
                }
            }
        } catch (Exception $e) {
            return false;
        }

        return count(self::filterCompletedRequirements($object->requirements, $planet)) === 0;
    }

    /**
     * Check if object requirements are met (for building it) with existing and queued items.
     *
     * @param string $machine_name
     * @param PlanetService $planet
     * @param int $target_level
     * @return bool
     */
    public static function objectRequirementsMetWithQueue(string $machine_name, int $target_level, PlanetService $planet): bool
    {
        $object = self::getObjectByMachineName($machine_name);

        // Check object's previous levels against queued objects
        if (!self::hasPreviousLevelsInQueue($target_level, $object, $planet)) {
            return false;
        }

        // Check object's requirements against built objects
        $missingRequirements = self::filterCompletedRequirements($object->requirements, $planet);

        if (count($missingRequirements) === 0) {
            return true;
        }

        // Check object's requirements against queued objects
        return count(self::filterQueuedRequirements($missingRequirements, $planet)) === 0;
    }

    /**
     * Check if building can be built on this planet type (planet or moon).
     *
     * @param string $machine_name
     * @param PlanetService $planet
     * @return bool
     */
    public static function objectValidPlanetType(string $machine_name, PlanetService $planet): bool
    {
        $object = self::getObjectByMachineName($machine_name);

        if (empty($object->valid_planet_types)) {
            return true;
        }

        return in_array($planet->getPlanetType(), $object->valid_planet_types);
    }

    /**
     * Calculates the max build amount of an object (unit) based on available
     * planet resources.
     *
     * @param string $machine_name
     * @param PlanetService $planet
     * @param bool $requirements_met
     * @return int
     * @throws Exception
     */
    public static function getObjectMaxBuildAmount(string $machine_name, PlanetService $planet, bool $requirements_met): int
    {
        // If requirements are false, the max build amount is 0
        if (!$requirements_met) {
            return 0;
        }

        // Objects only be able to be built once
        if ($machine_name === 'small_shield_dome' || $machine_name === 'large_shield_dome') {
            return $planet->getObjectAmount($machine_name) ? 0 : 1;
        }

        $price = self::getObjectPrice($machine_name, $planet);

        // Calculate max build amount based on price
        $max_build_amount = [];
        if ($price->metal->get() > 0) {
            $max_build_amount[] = floor($planet->metal()->get() / $price->metal->get());
        }

        if ($price->crystal->get() > 0) {
            $max_build_amount[] = floor($planet->crystal()->get() / $price->crystal->get());
        }

        if ($price->deuterium->get() > 0) {
            $max_build_amount[] = floor($planet->deuterium()->get() / $price->deuterium->get());
        }

        if ($price->energy->get() > 0) {
            $max_build_amount[] = floor($planet->energy()->get() / $price->energy->get());
        }

        // Get the lowest divided value which is the maximum amount of times this ship
        // can be built right now.
        if (count($max_build_amount) === 0) {
            return 0;
        }

        return (int)min($max_build_amount);
    }

    /**
     * Gets the cost of upgrading a building on this planet to the next level.
     *
     * @param string $machine_name
     * @param PlanetService $planet
     * @return Resources
     * @throws Exception
     */
    public static function getObjectPrice(string $machine_name, PlanetService $planet): Resources
    {
        $object = self::getObjectByMachineName($machine_name);

        // Price calculation for buildings or research (price depends on level)
        if ($object->type === GameObjectType::Building || $object->type === GameObjectType::Station || $object->type === GameObjectType::Research) {
            if ($object->type === GameObjectType::Building || $object->type === GameObjectType::Station) {
                $current_level = $planet->getObjectLevel($object->machine_name);
            } else {
                $current_level = $planet->getPlayer()->getResearchLevel($object->machine_name);
            }

            $price = self::getObjectRawPrice($machine_name, $current_level + 1);
        }
        // Price calculation for fleet or defense (regular price per unit)
        else {
            $price = self::getObjectRawPrice($machine_name);
        }

        return $price;
    }

    /**
     * Gets the cost of building a building of a certain level or a unit.
     *
     * @param string $machine_name
     * @param int $level
     * @return Resources
     */
    public static function getObjectRawPrice(string $machine_name, int $level = 0): Resources
    {
        try {
            $object = self::getObjectByMachineName($machine_name);
        } catch (Exception $e) {
            return new Resources(0, 0, 0, 0);
        }

        // Price calculation for buildings or research (price depends on level)
        if ($object->type === GameObjectType::Building || $object->type === GameObjectType::Station || $object->type === GameObjectType::Research) {
            // Level 0 is free.
            if ($level === 0) {
                return new Resources(0, 0, 0, 0);
            }

            $base_price = $object->price;

            // Calculate price.
            $metal = $base_price->resources->metal->get() * ($base_price->factor ** ($level - 1));
            $crystal = $base_price->resources->crystal->get() * ($base_price->factor ** ($level - 1));
            $deuterium = $base_price->resources->deuterium->get() * ($base_price->factor ** ($level - 1));
            $energy = $base_price->resources->energy->get() * ($base_price->factor ** ($level - 1));

            // Round price
            $metal = round($metal);
            $crystal = round($crystal);
            $deuterium = round($deuterium);
            $energy = round($energy);

            if (!empty($base_price->roundNearest100)) {
                // Round resource cost to nearest 100.
                $metal = round($metal / 100) * 100;
                $crystal = round($crystal / 100) * 100;
                $deuterium = round($deuterium / 100) * 100;
            }
        }
        // Price calculation for fleet or defense (regular price per unit)
        else {
            $metal = $object->price->resources->metal->get();
            $crystal = $object->price->resources->crystal->get();
            $deuterium = $object->price->resources->deuterium->get();
            $energy = $object->price->resources->energy->get();
        }

        return new Resources($metal, $crystal, $deuterium, $energy);
    }

    /**
     * Filter out completed requirements.
     *
     * @param array<GameObjectRequirement> $requirements
     * @param PlanetService $planet
     * @return array<GameObjectRequirement>
     */
    private static function filterCompletedRequirements(array $requirements, PlanetService $planet): array
    {
        return array_filter($requirements, function ($requirement) use ($planet) {
            try {
                $object = self::getObjectByMachineName($requirement->object_machine_name);

                if ($object->type === GameObjectType::Research) {
                    // Check if requirements are met with existing technology.
                    if ($planet->getPlayer()->getResearchLevel($requirement->object_machine_name) < $requirement->level) {
                        return true;
                    }
                } else {
                    // Check if requirements are met with existing buildings.
                    if ($planet->getObjectLevel($requirement->object_machine_name) < $requirement->level) {
                        return true;
                    }
                }

                return false;
            } catch (Exception $e) {
                return true;
            }
        });
    }

    /**
     * Filter out queued requirements.
     *
     * @param array<GameObjectRequirement> $requirements
     * @param PlanetService $planet
     * @return array<GameObjectRequirement>
     */
    private static function filterQueuedRequirements(array $requirements, PlanetService $planet): array
    {
        return array_filter($requirements, function ($requirement) use ($planet) {
            try {
                $object = self::getObjectByMachineName($requirement->object_machine_name);

                // Skip the queue check for the research lab, as it must be present for research objects.
                if ($requirement->object_machine_name === 'research_lab') {
                    return ($planet->getObjectLevel('research_lab') !== $requirement->level);
                }

                if ($object->type === GameObjectType::Research) {
                    // Check if the requirements are met by the items in the research queue.
                    if (!$planet->getPlayer()->isResearchingTech($requirement->object_machine_name, $requirement->level)) {
                        return true;
                    }
                } else {
                    // Check if the requirements are met by the items in the building queue.
                    // The building queue is checked only for building queue objects, not for unit queue objects.
                    if (!$planet->isBuildingObject($requirement->object_machine_name, $requirement->level)) {
                        return true;
                    }
                }

                return false;
            } catch (Exception $e) {
                return true;
            }
        });
    }

    /**
     * Check if object previous level requirements are met (for building it).
     *
     * @param int $target_level
     * @param GameObject $object
     * @param PlanetService $planet
     * @return bool
     */
    private static function hasPreviousLevels(int $target_level, GameObject $object, PlanetService $planet): bool
    {
        $current_level = 0;

        if ($object->type === GameObjectType::Research) {
            $current_level = $planet->getPlayer()->getResearchLevel($object->machine_name);
        } else {
            $current_level = $planet->getObjectLevel($object->machine_name);
        }

        // Check if target level is current or next level
        if ($current_level === $target_level || $current_level + 1 === $target_level) {
            return true;
        }

        return false;
    }

    /**
     * Check if object previous level requirements are met (for building it).
     *
     * @param int $target_level
     * @param GameObject $object
     * @param PlanetService $planet
     * @return bool
     */
    private static function hasPreviousLevelsInQueue(int $target_level, GameObject $object, PlanetService $planet): bool
    {
        // Check prior levels from queues.
        if ($object->type === GameObjectType::Research) {
            $current_level = $planet->getPlayer()->getResearchLevel($object->machine_name);
        } else {
            $current_level = $planet->getObjectLevel($object->machine_name);
        }

        for ($i = $current_level + 1; $i < $target_level; $i++) {
            if (!$planet->isBuildingObject($object->machine_name, $i) && !$planet->getPlayer()->isResearchingTech($object->machine_name, $i)) {
                return false;
            }
        }

        return true;
    }
}
