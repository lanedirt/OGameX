<?php

namespace OGame\GameObjects\Models;

use OGame\GameObjects\Models\Enums\GameObjectType;

class ShipObject extends UnitObject
{
    public GameObjectType $type = GameObjectType::Ship;

    /**
     * Whether this ship counts as a military ship for military-specific lookups
     * and score calculations. Core military ships are handled by MilitaryShipObjects;
     * module-registered ships set this flag to participate in the military split.
     */
    public bool $isMilitary = false;
}
