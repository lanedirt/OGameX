<?php

namespace OGame\GameObjects\Models;

use OGame\GameObjects\Models\Enums\GameObjectType;

class DefenseObject extends UnitObject
{
    public GameObjectType $type = GameObjectType::Defense;
}
