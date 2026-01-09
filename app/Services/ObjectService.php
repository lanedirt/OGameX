<?php

namespace OGame\Services;

use OGame\Models\UnitQueue;
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
     * Check if character class requirements are met for class-specific ships.
     *
     * @param string $machine_name
     * @param PlanetService $planet
     * @return bool
     */
    public static function objectCharacterClassMet(string $machine_name, PlanetService $planet): bool
    {
        $player = $planet->getPlayer();
        $user = $player->getUser();
        $characterClassService = app(CharacterClassService::class);

        if ($machine_name === 'reaper' && !$characterClassService->isGeneral($user)) {
            return false;
        }

        if ($machine_name === 'pathfinder' && !$characterClassService->isDiscoverer($user)) {
            return false;
        }

        if ($machine_name === 'crawler' && !$characterClassService->isCollector($user)) {
            return false;
        }

        return true;
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

        // Check missile silo capacity for IPM and ABM
        if ($machine_name === 'interplanetary_missile' || $machine_name === 'anti_ballistic_missile') {
            $silo_level = $planet->getObjectLevel('missile_silo');
            $total_capacity = $silo_level * 10; // Each silo level provides 10 slots

            // Calculate current silo usage from built missiles
            $current_ipm = $planet->getObjectAmount('interplanetary_missile');
            $current_abm = $planet->getObjectAmount('anti_ballistic_missile');

            // Get queued missiles from the unit queue
            $ipm_object_id = self::getObjectByMachineName('interplanetary_missile')->id;
            $abm_object_id = self::getObjectByMachineName('anti_ballistic_missile')->id;

            $queued_ipm = UnitQueue::where('planet_id', $planet->getPlanetId())
                ->where('object_id', $ipm_object_id)
                ->where('processed', 0)
                ->sum('object_amount');

            $queued_abm = UnitQueue::where('planet_id', $planet->getPlanetId())
                ->where('object_id', $abm_object_id)
                ->where('processed', 0)
                ->sum('object_amount');

            // Calculate total usage including queue
            $total_ipm = $current_ipm + $queued_ipm;
            $total_abm = $current_abm + $queued_abm;
            $used_capacity = ($total_ipm * 2) + $total_abm; // IPM = 2 slots, ABM = 1 slot

            $remaining_capacity = $total_capacity - $used_capacity;

            // Calculate max build amount based on silo capacity
            if ($machine_name === 'interplanetary_missile') {
                // IPM takes 2 slots, so divide remaining capacity by 2
                $max_from_silo = floor($remaining_capacity / 2);
            } else {
                // ABM takes 1 slot
                $max_from_silo = $remaining_capacity;
            }

            // Make sure we don't return negative numbers
            if ($max_from_silo <= 0) {
                return 0;
            }
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

        // Add silo capacity limit to the array for missiles
        if (($machine_name === 'interplanetary_missile' || $machine_name === 'anti_ballistic_missile') && isset($max_from_silo)) {
            $max_build_amount[] = $max_from_silo;
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
     * Gets the cost of downgrading a building on this planet by one level.
     *
     * @param string $machine_name
     * @param PlanetService $planet
     * @return Resources
     * @throws Exception
     */
    public static function getObjectDowngradePrice(string $machine_name, PlanetService $planet, int|null $target_level = null): Resources
    {
        $object = self::getObjectByMachineName($machine_name);

        // Only buildings and stations can be downgraded
        if ($object->type !== GameObjectType::Building && $object->type !== GameObjectType::Station) {
            return new Resources(0, 0, 0, 0);
        }

        $current_level = $planet->getObjectLevel($object->machine_name);

        // If target_level is provided, use it (for calculating downgrade price when upgrades are in queue)
        // Otherwise, use current_level
        $level_for_calculation = $target_level ?? $current_level;

        // Cannot downgrade if already at level 0
        if ($level_for_calculation <= 0) {
            return new Resources(0, 0, 0, 0);
        }

        // Get the construction cost for the level (cost to build from level-1 to level)
        // The downgrade cost equals the construction cost of the level
        $base_downgrade_cost = self::getObjectRawPrice($machine_name, $level_for_calculation);

        // Apply Ion technology bonus (each level reduces cost by 4%)
        $player = $planet->getPlayer();
        if ($player !== null) {
            $ion_technology_level = $player->getResearchLevel('ion_technology');
            $ion_bonus = $ion_technology_level * 0.04;

            // Apply bonus: reduce cost by ion_bonus percentage
            $final_cost = new Resources(
                max(0, floor($base_downgrade_cost->metal->get() * (1 - $ion_bonus))),
                max(0, floor($base_downgrade_cost->crystal->get() * (1 - $ion_bonus))),
                max(0, floor($base_downgrade_cost->deuterium->get() * (1 - $ion_bonus))),
                0 // Energy is not used for downgrade
            );

            return $final_cost;
        }

        // If no player, return base cost without bonus
        return new Resources(
            floor($base_downgrade_cost->metal->get()),
            floor($base_downgrade_cost->crystal->get()),
            floor($base_downgrade_cost->deuterium->get()),
            0 // Energy is not used for downgrade
        );
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
            $metal = floor($metal);
            $crystal = floor($crystal);
            $deuterium = floor($deuterium);
            $energy = floor($energy);

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
     * Gets the cumulative cost of all levels from 1 to the specified level for a building or research.
     * Uses geometric series formula for O(1) performance instead of iterative O(n) calculation.
     *
     * @param string $machine_name
     * @param int $level
     * @return Resources (with energy excluded)
     */
    public static function getObjectCumulativeCost(string $machine_name, int $level): Resources
    {
        try {
            $object = self::getObjectByMachineName($machine_name);
        } catch (Exception $e) {
            return new Resources(0, 0, 0, 0);
        }

        if ($object->type !== GameObjectType::Building &&
            $object->type !== GameObjectType::Station &&
            $object->type !== GameObjectType::Research) {
            return new Resources(0, 0, 0, 0);
        }

        if ($level === 0) {
            return new Resources(0, 0, 0, 0);
        }

        if ($level === 1) {
            $base_price = $object->price;
            $metal = floor($base_price->resources->metal->get());
            $crystal = floor($base_price->resources->crystal->get());
            $deuterium = floor($base_price->resources->deuterium->get());

            return new Resources($metal, $crystal, $deuterium, 0);
        }

        $base_price = $object->price;
        $factor = $base_price->factor;

        $metal = self::calculateCumulativeCostForResource(
            $base_price->resources->metal->get(),
            $factor,
            $level
        );

        $crystal = self::calculateCumulativeCostForResource(
            $base_price->resources->crystal->get(),
            $factor,
            $level
        );

        $deuterium = self::calculateCumulativeCostForResource(
            $base_price->resources->deuterium->get(),
            $factor,
            $level
        );

        return new Resources($metal, $crystal, $deuterium, 0);
    }

    /**
     * Calculate cumulative cost for a single resource type using geometric series formula.
     *
     * @param float $base_cost
     * @param float $factor
     * @param int $level
     * @return float
     */
    private static function calculateCumulativeCostForResource(float $base_cost, float $factor, int $level): float
    {
        if ($base_cost == 0) {
            return 0;
        }

        if ($factor == 1) {
            return floor($base_cost * $level);
        }

        $sum = $base_cost * (1 - pow($factor, $level)) / (1 - $factor);
        return floor($sum);
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

    /**
     * Get all requirements recursively for a game object.
     * Returns an array of [machine_name => minimum_level] for all prerequisites.
     *
     * @param string $machineName
     * @param array<string, int> $collected Already collected requirements (for recursion)
     * @return array<string, int>
     */
    public static function getRecursiveRequirements(string $machineName, array &$collected = []): array
    {
        try {
            $object = self::getObjectByMachineName($machineName);
        } catch (Exception $e) {
            return $collected;
        }

        foreach ($object->requirements as $requirement) {
            $reqName = $requirement->object_machine_name;
            $reqLevel = $requirement->level;

            if (!isset($collected[$reqName]) || $collected[$reqName] < $reqLevel) {
                $collected[$reqName] = $reqLevel;
            }

            self::getRecursiveRequirements($reqName, $collected);
        }

        return $collected;
    }

    /**
     * Check if a building can be downgraded (no other buildings/research require it at current level).
     *
     * @param string $machine_name
     * @param PlanetService $planet
     * @return bool
     */
    public static function canDowngradeBuilding(string $machine_name, PlanetService $planet): bool
    {
        try {
            $object = self::getObjectByMachineName($machine_name);

            // Only buildings and stations can be downgraded
            if ($object->type !== GameObjectType::Building && $object->type !== GameObjectType::Station) {
                return false;
            }

            $current_level = $planet->getObjectLevel($machine_name);

            // Cannot downgrade if already at level 0
            if ($current_level <= 0) {
                return false;
            }

            // Special case: Missile Silo cannot be downgraded if it contains missiles
            if ($machine_name === 'missile_silo') {
                $ipm_count = $planet->getObjectAmount('interplanetary_missile');
                $abm_count = $planet->getObjectAmount('anti_ballistic_missile');

                if ($ipm_count > 0 || $abm_count > 0) {
                    return false; // Cannot downgrade silo while it contains missiles
                }
            }

            // Check all buildings, stations, and research objects for requirements
            $allObjects = [...self::getBuildingObjects(), ...self::getStationObjects(), ...self::getResearchObjects()];

            foreach ($allObjects as $checkObject) {
                // Skip checking requirements for the same object
                if ($checkObject->machine_name === $machine_name) {
                    continue;
                }

                // Check if this object has requirements
                if (empty($checkObject->requirements)) {
                    continue;
                }

                // Check each requirement
                foreach ($checkObject->requirements as $requirement) {
                    // If this requirement matches the building we want to downgrade
                    if ($requirement->object_machine_name === $machine_name) {
                        // Check if the requirement level matches or exceeds current level
                        if ($requirement->level >= $current_level) {
                            // Get the current level of the requiring object
                            $requiring_object_level = 0;
                            if ($checkObject->type === GameObjectType::Research) {
                                $requiring_object_level = $planet->getPlayer()->getResearchLevel($checkObject->machine_name);
                            } else {
                                $requiring_object_level = $planet->getObjectLevel($checkObject->machine_name);
                            }

                            // If the requiring object exists at a level that needs this building at current level or higher
                            // Only block if the requiring object's level meets or exceeds the requirement level
                            if ($requiring_object_level >= $requirement->level) {
                                return false; // Cannot downgrade, dependency exists
                            }
                        }
                    }
                }
            }

            return true; // No dependencies found, can downgrade
        } catch (Exception $e) {
            return false; // On error, don't allow downgrade
        }
    }
}
