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

    /**
     * ObjectProperties constructor.
     *
     * @param ObjectPropertyDetails $structural_integrity
     * @param ObjectPropertyDetails $shield
     * @param ObjectPropertyDetails $attack
     * @param ObjectPropertyDetails $speed
     * @param ObjectPropertyDetails $capacity
     * @param ObjectPropertyDetails $fuel
     */
    public function __construct(ObjectPropertyDetails $structural_integrity, ObjectPropertyDetails $shield, ObjectPropertyDetails $attack, ObjectPropertyDetails $speed, ObjectPropertyDetails $capacity, ObjectPropertyDetails $fuel)
    {
        $this->structuralIntegrity = $structural_integrity;
        $this->shield = $shield;
        $this->attack = $attack;
        $this->speed = $speed;
        $this->capacity = $capacity;
        $this->fuel = $fuel;
    }
}