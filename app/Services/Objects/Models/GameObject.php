<?php

namespace OGame\Services\Objects\Models;

use OGame\Services\Objects\Models\Fields\GameObjectPrice;
use OGame\Services\Objects\Models\Fields\GameObjectRequirement;

abstract class GameObject
{
    public int $id;
    public string $type;
    public string $title;
    public string $machine_name;
    public string $description;
    public string $description_long;

    /**
     * Objects that this object requires on with required level.
     *
     * @var array<GameObjectRequirement>
     */
    public array $requirements;

    /**
     * Price of the object.
     *
     * @var GameObjectPrice
     */
    public GameObjectPrice $price;
}