<?php

namespace OGame\Services\Objects\Models;

use OGame\Services\Objects\Models\Fields\GameObjectProperties;
use OGame\Services\Objects\Models\Fields\GameObjectRapidfire;

abstract class UnitObject extends GameObject
{
    /**
     * Objects that this object requires on with required level.
     *
     * @var array<GameObjectRapidfire>
     */
    public array $rapidfire;

    /**
     * Properties of the object.
     *
     * @var GameObjectProperties
     */
    public GameObjectProperties $properties;
}