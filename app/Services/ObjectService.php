<?php

namespace OGame\Services;

use Exception;
use OGame\GameObjects\BuildingObjects;
use OGame\GameObjects\CivilShipObjects;
use OGame\GameObjects\DefenseObjects;
use OGame\GameObjects\MilitaryShipObjects;
use OGame\GameObjects\Models\BuildingObject;
use OGame\GameObjects\Models\DefenseObject;
use OGame\GameObjects\Models\GameObject;
use OGame\GameObjects\Models\ResearchObject;
use OGame\GameObjects\Models\ShipObject;
use OGame\GameObjects\Models\StationObject;
use OGame\GameObjects\Models\UnitObject;
use OGame\GameObjects\ResearchObjects;
use OGame\GameObjects\StationObjects;
use OGame\Models\Resources;

/**
 * Class ObjectService.
 *
 * Contains all information about game objects such as buildings, research,
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
    public function getObjects(): array
    {
        return array_merge(BuildingObjects::get(), StationObjects::get(), ResearchObjects::get(), MilitaryShipObjects::get(), CivilShipObjects::get(), DefenseObjects::get());
    }

    /**
     * Get all buildings.
     *
     * @return array<BuildingObject>
     */
    public function getBuildingObjects(): array
    {
        return BuildingObjects::get();
    }

    /**
     * Get all buildings.
     *
     * @return array<StationObject>
     */
    public function getStationObjects(): array
    {
        return StationObjects::get();
    }

    /**
     * Get all buildings.
     *
     * @return array<ResearchObject>
     */
    public function getResearchObjects(): array
    {
        return ResearchObjects::get();
    }

    /**
     * Get all unit objects.
     *
     * @return array<UnitObject>
     */
    public function getUnitObjects(): array
    {
        return array_merge(MilitaryShipObjects::get(), CivilShipObjects::get(), DefenseObjects::get());
    }


    /**
     * Get all ship objects.
     *
     * @return array<ShipObject>
     */
    public function getShipObjects(): array
    {
        return array_merge(MilitaryShipObjects::get(), CivilShipObjects::get());
    }

    /**
     * Get all defense objects.
     *
     * @return array<DefenseObject>
     */
    public function getDefenseObjects(): array
    {
        return DefenseObjects::get();
    }

    /**
     * Get all military ship objects.
     *
     * @return array<ShipObject>
     */
    public function getMilitaryShipObjects(): array
    {
        return MilitaryShipObjects::get();
    }

    /**
     * Get all civil ship objects.
     *
     * @return array<ShipObject>
     */
    public function getCivilShipObjects(): array
    {
        return CivilShipObjects::get();
    }

    /**
     * Get specific building.
     *
     * @param string $machine_name
     * @return BuildingObject
     */
    public function getBuildingObjectByMachineName(string $machine_name): BuildingObject
    {
        // Loop through all buildings and return the one with the matching UID
        foreach (BuildingObjects::get() as $building) {
            if ($building->machine_name === $machine_name) {
                return $building;
            }
        }

        throw new \RuntimeException('Building not found');
    }

    /**
     * Get specific ship.
     *
     * @param string $machine_name
     * @return ShipObject
     */
    public function getShipObjectByMachineName(string $machine_name): ShipObject
    {
        // Loop through all buildings and return the one with the matching UID
        $shipObjects = array_merge(MilitaryShipObjects::get(), CivilShipObjects::get());
        foreach ($shipObjects as $ship) {
            if ($ship->machine_name === $machine_name) {
                return $ship;
            }
        }

        throw new \RuntimeException('Ship not found');
    }

    /**
     * Get specific game object.
     *
     * @param int $object_id
     * @return GameObject
     */
    public function getObjectById(int $object_id): GameObject
    {
        // Loop through all buildings and return the one with the matching UID
        $allObjects = array_merge(BuildingObjects::get(), StationObjects::get(), ResearchObjects::get(), MilitaryShipObjects::get(), CivilShipObjects::get(), DefenseObjects::get());
        foreach ($allObjects as $object) {
            if ($object->id == $object_id) {
                return $object;
            }
        }

        throw new \RuntimeException('Game object not found with ID: ' . $object_id);
    }

    /**
     * Get specific game object.
     *
     * @param string $machine_name
     * @return GameObject
     */
    public function getObjectByMachineName(string $machine_name): GameObject
    {
        // Loop through all buildings and return the one with the matching UID
        $allObjects = array_merge(BuildingObjects::get(), StationObjects::get(), ResearchObjects::get(), MilitaryShipObjects::get(), CivilShipObjects::get(), DefenseObjects::get());
        foreach ($allObjects as $object) {
            if ($object->machine_name == $machine_name) {
                return $object;
            }
        }

        throw new \RuntimeException('Game object not found with machine name: ' . $machine_name);
    }

    /**
     * Get specific research object.
     *
     * @param string $machine_name
     * @return ResearchObject
     */
    public function getResearchObjectByMachineName(string $machine_name): ResearchObject
    {
        // Loop through all buildings and return the one with the matching UID
        $allObjects = ResearchObjects::get();
        foreach ($allObjects as $object) {
            if ($object->machine_name === $machine_name) {
                return $object;
            }
        }

        throw new \RuntimeException('Research object not found with machine name: ' . $machine_name);
    }

    /**
     * Get specific research object.
     *
     * @param int $object_id
     * @return ResearchObject
     */
    public function getResearchObjectById(int $object_id): ResearchObject
    {
        // Loop through all buildings and return the one with the matching UID
        $allObjects = ResearchObjects::get();
        foreach ($allObjects as $object) {
            if ($object->id === $object_id) {
                return $object;
            }
        }

        throw new \RuntimeException('Unit object not found with object ID: ' . $object_id);
    }

    /**
     * Get specific unit object.
     *
     * @param int $object_id
     * @return UnitObject
     */
    public function getUnitObjectById(int $object_id): UnitObject
    {
        $allObjects = array_merge(MilitaryShipObjects::get(), CivilShipObjects::get(), DefenseObjects::get());
        foreach ($allObjects as $object) {
            if ($object->id === $object_id) {
                return $object;
            }
        }

        throw new \RuntimeException('Unit object not found with object ID: ' . $object_id);
    }

    /**
     * Get specific unit object.
     *
     * @param string $machine_name
     * @return UnitObject
     */
    public function getUnitObjectByMachineName(string $machine_name): UnitObject
    {
        // Loop through all buildings and return the one with the matching UID
        $allObjects = array_merge(MilitaryShipObjects::get(), CivilShipObjects::get(), DefenseObjects::get());
        foreach ($allObjects as $object) {
            if ($object->machine_name === $machine_name) {
                return $object;
            }
        }

        throw new \RuntimeException('Unit object not found with machine name: ' . $machine_name);
    }

    /**
     * Get all buildings that have production values.
     *
     * @return array<BuildingObject>
     */
    public function getBuildingObjectsWithProduction(): array
    {
        $return = array();

        foreach (BuildingObjects::get() as $value) {
            if (!empty(($value->production))) {
                $return[] = $value;
            }
        }

        return $return;
    }

    /**
     * Get all buildings that have production values.
     *
     * @param string $machine_name
     * @return BuildingObject
     */
    public function getBuildingObjectsWithProductionByMachineName(string $machine_name): BuildingObject
    {
        foreach (BuildingObjects::get() as $object) {
            if ($object->machine_name === $machine_name && !empty(($object->production))) {
                return $object;
            }
        }

        throw new \RuntimeException('Building not found with production value for machine name: ' . $machine_name);
    }

    /**
     * Get all buildings that have storage values.
     *
     * @return array<BuildingObject>
     */
    public function getBuildingObjectsWithStorage(): array
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
     * @param PlayerService $player
     * @return bool
     */
    public function objectRequirementsMet(string $machine_name, PlanetService $planet, PlayerService $player): bool
    {
        try {
            $object = $this->getObjectByMachineName($machine_name);
            foreach ($object->requirements as $requirement) {
                // Load required object and check if requirements are met.
                $object_required = $this->getObjectByMachineName($requirement->object_machine_name);
                if ($object_required->type === 'research') {
                    if ($player->getResearchLevel($object_required->machine_name) < $requirement->level) {
                        return false;
                    }
                } else {
                    if ($planet->getObjectLevel($object_required->machine_name) < $requirement->level) {
                        return false;
                    }
                }
            }
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Calculates the max build amount of an object (unit) based on available
     * planet resources.
     *
     * @param string $machine_name
     * @param PlanetService $planet
     * @return int
     * @throws Exception
     */
    public function getObjectMaxBuildAmount(string $machine_name, PlanetService $planet): int
    {
        $price = $this->getObjectPrice($machine_name, $planet);

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
        return min($max_build_amount);
    }

    /**
     * Gets the cost of upgrading a building on this planet to the next level.
     *
     * @param string $machine_name
     * @param PlanetService $planet
     * @return Resources
     * @throws Exception
     */
    public function getObjectPrice(string $machine_name, PlanetService $planet): Resources
    {
        $object = $this->getObjectByMachineName($machine_name);
        $player = $planet->getPlayer();

        // Price calculation for buildings or research (price depends on level)
        if ($object->type === 'building' || $object->type === 'station' || $object->type === 'research') {
            if ($object->type === 'building' || $object->type === 'station') {
                $current_level = $planet->getObjectLevel($object->machine_name);
            } else {
                $current_level = $player?->getResearchLevel($object->machine_name);
            }

            $price = $this->getObjectRawPrice($machine_name, $current_level + 1);
        }
        // Price calculation for fleet or defense (regular price per unit)
        else {
            $price = $this->getObjectRawPrice($machine_name);
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
    public function getObjectRawPrice(string $machine_name, int $level = 0): Resources
    {
        try {
            $object = $this->getObjectByMachineName($machine_name);
        } catch (Exception $e) {
            return new Resources(0, 0, 0, 0);
        }

        // Price calculation for buildings or research (price depends on level)
        if ($object->type === 'building' || $object->type === 'station' || $object->type === 'research') {
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
}
