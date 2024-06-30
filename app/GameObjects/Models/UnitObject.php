<?php

namespace OGame\GameObjects\Models;

use OGame\GameObjects\Models\Abstracts\GameObject;
use OGame\GameObjects\Models\Fields\GameObjectProperties;
use OGame\GameObjects\Models\Fields\GameObjectRapidfire;

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
