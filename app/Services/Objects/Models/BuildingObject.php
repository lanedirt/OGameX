<?php

namespace OGame\Services\Objects\Models;

use OGame\Services\Objects\Models\Fields\GameObjectProduction;

class BuildingObject extends GameObject
{
    public string $type = 'building';

    /**
     * Production of the object (in case of buildings)
     */
    public GameObjectProduction $production;
}