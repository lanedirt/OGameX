<?php

namespace OGame\GameObjects\Models;

use OGame\Services\PlanetService;

class UnitCollection
{
    /**
     * Units in the collection.
     *
     * @var array<UnitEntry>
     */
    public array $units;

    /**
     * Add a unit to the collection.
     */
    public function addUnit(UnitObject $unitObject, int $amount): void
    {
        $entry = new UnitEntry($unitObject, $amount);

        $this->units[] = $entry;
    }

    /**
     * Remove a unit from the collection.
     */
    public function removeUnit(UnitObject $unitObject, int $amount): void
    {
        $found = false;
        foreach ($this->units as $key => $entry) {
            if ($entry->unitObject->machine_name === $unitObject->machine_name) {
                $this->units[$key]->amount -= $amount;

                if ($this->units[$key]->amount <= 0) {
                    unset($this->units[$key]);
                }

                $found = true;
            }
        }

        // Throw an exception if the to be removed unit was not found in the collection.
        if (!$found) {
            throw new \Exception('Unit not found in collection');
        }
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
     * @param PlanetService $planet
     * @return int
     */
    public function getSlowestUnitSpeed(PlanetService $planet): int
    {
        $slowest = 0;
        foreach ($this->units as $entry) {
            $speed = $entry->unitObject->properties->speed->calculate($planet)->totalValue;
            if ($slowest === 0 || $speed < $slowest) {
                $slowest = $speed;
            }
        }

        return $slowest;
    }

}
