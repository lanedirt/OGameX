<?php

namespace OGame\GameObjects\Models;

use OGame\GameObjects\Models\Fields\GameObjectAssets;
use OGame\GameObjects\Models\Fields\GameObjectPrice;
use OGame\GameObjects\Models\Fields\GameObjectRequirement;

abstract class GameObject
{
    public int $id;
    public string $title;
    public string $type;
    public string $machine_name;
    /**
     * Optional class name of the object used in frontend which differs from the machine name.
     *
     * @var string
     */
    public string $class_name = '';
    public string $description;
    public string $description_long;

    /**
     * Objects that this object requires on with required level.
     *
     * @var array<GameObjectRequirement>
     */
    public array $requirements = [];

    /**
     * Price of the object.
     *
     * @var GameObjectPrice
     */
    public GameObjectPrice $price;

    /**
     * Assets of the object.
     *
     * @var GameObjectAssets
     */
    public GameObjectAssets $assets;
}
