<?php

namespace OGame\GameObjects\Models;

use OGame\GameObjects\Models\Enums\GameObjectType;

class ShipObject extends UnitObject
{
    public GameObjectType $type = GameObjectType::Ship;
}
