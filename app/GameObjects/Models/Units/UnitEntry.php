<?php

namespace OGame\GameObjects\Models\Units;

use OGame\GameObjects\Models\UnitObject;

class UnitEntry
{
    public function __construct(
        /**
         * The unit object.
         */
        public UnitObject $unitObject,
        /**
         * Amount of units in this collection.
         */
        public int $amount
    ) {
    }
}
