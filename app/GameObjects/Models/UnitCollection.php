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
        $entry = new UnitEntry();
        $entry->unitObject = $unitObject;
        $entry->amount = $amount;

        $this->units[] = $entry;
    }
}