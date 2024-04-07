<?php

namespace OGame\Services\Objects\Properties\Models;

/**
 * Class ObjectProperties.
 *
 * @package OGame\Services\Objects\Properties\Models
 */
class ObjectProperties
{
    public ObjectPropertyDetails $structuralIntegrity;
    public ObjectPropertyDetails $shield;
    public ObjectPropertyDetails $attack;
    public ObjectPropertyDetails $speed;
    public ObjectPropertyDetails $capacity;
    public ObjectPropertyDetails $fuel;

    public function __construct($structural_integrity, $shield, $attack, $speed, $capacity, $fuel)
    {
        $this->structuralIntegrity = $structural_integrity;
        $this->shield = $shield;
        $this->attack = $attack;
        $this->speed = $speed;
        $this->capacity = $capacity;
        $this->fuel = $fuel;
    }
}