<?php

namespace OGame\GameObjects\Models;

use OGame\GameObjects\Models\Abstracts\GameObject;
use OGame\GameObjects\Models\Enums\GameObjectType;
use OGame\GameObjects\Models\Fields\GameObjectStorage;

class BuildingObject extends GameObject
{
    public GameObjectType $type = GameObjectType::Building;

    /**
     * Storage of the object (in case of storage buildings)
     */
    public GameObjectStorage $storage;
}
