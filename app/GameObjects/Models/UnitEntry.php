<?php

namespace OGame\GameObjects\Models;

class UnitEntry
{
    /**
     * Objects that this object requires on with required level.
     *
     * @var UnitObject
     */
    public UnitObject $unitObject;

    /**
     * Amount of units in this collection.
     *
     * @var int
     */
    public int $amount;

    public function __construct(UnitObject $unitObject, int $amount)
    {
        $this->unitObject = $unitObject;
        $this->amount = $amount;
    }
}