<?php

namespace OGame\GameObjects\Models;

use OGame\GameObjects\Models\Fields\GameObjectProduction;
use OGame\GameObjects\Models\Fields\GameObjectStorage;

class BuildingObject extends GameObject
{
    public string $type = 'building';

    /**
     * Production of the object (in case of buildings)
     */
    public GameObjectProduction $production;

    /**
     * Storage of the object (in case of storage buildings)
     */
    public GameObjectStorage $storage;
}