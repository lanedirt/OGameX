<?php

namespace OGame\GameObjects\Models;

class UnitCollection
{
    /**
     * Objects that this object requires on with required level.
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
}
