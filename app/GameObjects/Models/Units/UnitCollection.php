<?php

namespace OGame\GameObjects\Models\Units;

use Exception;
use InvalidArgumentException;
use OGame\GameObjects\Models\UnitObject;
use OGame\Models\Resources;
use OGame\Services\PlayerService;

class UnitCollection
{
    /**
     * Units in the collection.
     *
     * @var array<UnitEntry>
     */
    public array $units = [];

    /**
     * Implement the clone magic method so that during clone of this object
     * the inner objects are also properly cloned.
     */
    public function __clone()
    {
        // Clone all units in the collection.
        $this->units = array_map(function ($entry) {
            return clone $entry;
        }, $this->units);
    }

    /**
     * Add a unit to the collection.
     */
    public function addUnit(UnitObject $unitObject, int $amount): void
    {
        // Check if the unit is already in the collection.
        foreach ($this->units as $entry) {
            if ($entry->unitObject->machine_name === $unitObject->machine_name) {
                $entry->amount += $amount;
                return;
            }
        }

        // If the unit is not in the collection, add it.
        $entry = new UnitEntry($unitObject, $amount);
        $this->units[] = $entry;
    }

    /**
     * Remove a unit from the collection.
     *
     * @param UnitObject $unitObject
     * @param int $amount
     * @param bool $remove_empty_units If true, the unit will be removed from the collection if the
     * amount reaches 0. Defaults to TRUE.
     */
    public function removeUnit(UnitObject $unitObject, int $amount, bool $remove_empty_units = false): void
    {
        $found = false;
        foreach ($this->units as $key => $entry) {
            if ($entry->unitObject->machine_name === $unitObject->machine_name) {
                $this->units[$key]->amount -= $amount;

                if ($remove_empty_units && $this->units[$key]->amount <= 0) {
                    unset($this->units[$key]);
                }

                $found = true;
            }
        }

        // Throw an exception if the to be removed unit was not found in the collection.
        if (!$found) {
            throw new InvalidArgumentException('Unit ' . $unitObject->machine_name . ' not found in collection');
        }
    }

    /**
     * Check if the collection has a unit.
     *
     * @param UnitObject $unitObject
     * @return bool
     */
    public function hasUnit(UnitObject $unitObject): bool
    {
        foreach ($this->units as $entry) {
            if ($entry->unitObject->machine_name === $unitObject->machine_name) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the amount of a unit in the collection.
     *
     * @param string $machine_name
     * @return int
     */
    public function getAmountByMachineName(string $machine_name): int
    {
        foreach ($this->units as $entry) {
            if ($entry->unitObject->machine_name === $machine_name) {
                return $entry->amount;
            }
        }

        return 0;
    }

    /**
     * Get the slowest unit in the collection.
     *
     * @param PlayerService $player
     * @return int
     */
    public function getSlowestUnitSpeed(PlayerService $player): int
    {
        $slowest = 0;
        foreach ($this->units as $entry) {
            $speed = $entry->unitObject->properties->speed->calculate($player)->totalValue;
            if ($slowest === 0 || $speed < $slowest) {
                $slowest = $speed;
            }
        }

        return $slowest;
    }

    /**
     * Get the cheapest ship from a given list of ship objects.
     *
     * @param UnitObject[] $candidateShipObjects
     * @return UnitObject|null
     */
    public function findCheapestShip(array $candidateShipObjects): UnitObject|null
    {
        $cheapest = null;
        foreach ($candidateShipObjects as $ship) {
            if ($cheapest === null || $ship->price->resources->sum() < $cheapest->price->resources->sum()) {
                $cheapest = $ship;
            }
        }

        return $cheapest;
    }

    /**
     * Get the total amount of units in the collection.
     *
     * @return int
     */
    public function getAmount(): int
    {
        $amount = 0;
        foreach ($this->units as $entry) {
            $amount += $entry->amount;
        }

        return $amount;
    }

    /**
     * Check if the collection has a non-espionage unit. This is used to check if e.g. an expedition mission is possible.
     *
     * @return bool
     */
    public function hasNonEspionageUnit(): bool
    {
        foreach ($this->units as $entry) {
            if ($entry->unitObject->machine_name !== 'espionage_probe') {
                return true;
            }
        }
        return false;
    }

    /**
     * Converts the unit collection to an associative array where key is machine name and value is amount.
     *
     * @return array<string, int>
     */
    public function toArray(): array
    {
        $units = [];
        foreach ($this->units as $entry) {
            $units[$entry->unitObject->machine_name] = $entry->amount;
        }

        return $units;
    }

    /**
     * Adds all units from another collection to this collection.
     *
     * @param UnitCollection $collection
     * @return void
     */
    public function addCollection(UnitCollection $collection): void
    {
        foreach ($collection->units as $entry) {
            // Check if the unit is already in the collection. If so, add the amount instead of adding a new entry.
            $found = false;
            foreach ($this->units as $key => $unit) {
                if ($unit->unitObject->machine_name === $entry->unitObject->machine_name) {
                    $this->units[$key]->amount += $entry->amount;
                    $found = true;
                }
            }

            // If the unit is not in the collection, add it.
            if (!$found) {
                $this->units[] = $entry;
            }
        }
    }

    /**
     * Subtracts all units from another collection from this collection.
     *
     * @param UnitCollection $collection
     * @return void
     * @throws Exception
     */
    public function subtractCollection(UnitCollection $collection): void
    {
        foreach ($collection->units as $entry) {
            $this->removeUnit($entry->unitObject, $entry->amount);
        }
    }

    /**
     * Get the total resource cost (build cost) of the units in the collection.
     *
     * @return Resources
     */
    public function toResources(): Resources
    {
        $resources = new Resources(0, 0, 0, 0);
        foreach ($this->units as $entry) {
            $metal = $entry->unitObject->price->resources->metal->get() * $entry->amount;
            $crystal = $entry->unitObject->price->resources->crystal->get() * $entry->amount;
            $deuterium = $entry->unitObject->price->resources->deuterium->get() * $entry->amount;

            $resources->add(new Resources($metal, $crystal, $deuterium, 0));
        }

        return $resources;
    }

    /**
     * Get the total cargo capacity of the units in the collection.
     *
     * @param PlayerService $player
     * @return int
     */
    public function getTotalCargoCapacity(PlayerService $player): int
    {
        $total = 0;
        foreach ($this->units as $entry) {
            $total += $entry->unitObject->properties->capacity->calculate($player)->totalValue * $entry->amount;
        }
        return $total;
    }
}
