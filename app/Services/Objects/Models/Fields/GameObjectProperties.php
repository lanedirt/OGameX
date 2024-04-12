<?php

namespace OGame\Services\Objects\Models\Fields;

use OGame\Services\Objects\Models\GameObject;
use OGame\Services\Objects\Properties\AttackPropertyService;
use OGame\Services\Objects\Properties\CapacityPropertyService;
use OGame\Services\Objects\Properties\FuelPropertyService;
use OGame\Services\Objects\Properties\ShieldPropertyService;
use OGame\Services\Objects\Properties\SpeedPropertyService;
use OGame\Services\Objects\Properties\StructuralIntegrityPropertyService;

class GameObjectProperties
{
    public GameObjectProperty $structural_integrity;
    public GameObjectProperty $shield;
    public GameObjectProperty $attack;
    public GameObjectProperty $speed;
    public GameObjectProperty $capacity;
    public GameObjectProperty $fuel;

    /**
     * Upgrades to speed for this object depending on alternative drive technology level. Items with higher
     * index take precedence over items with lower index.
     *
     * @var array<GameObjectSpeedUpgrade>
     */
    public array $speed_upgrade;

    /**
     * GameObjectProperties constructor.
     *
     * @param GameObject $parentObject
     *  Reference to the object this properties belong to (the parent).
     * @param int $structural_integrity
     * @param int $shield
     * @param int $attack
     * @param int $speed
     * @param int $capacity
     * @param int $fuel
     */
    public function __construct(GameObject $parentObject, int $structural_integrity, int $shield, int $attack, int $speed, int $capacity, int $fuel)
    {
        $calculationService = new StructuralIntegrityPropertyService($parentObject, $structural_integrity);
        $this->structural_integrity = new GameObjectProperty($structural_integrity, $calculationService);

        $calculationService = new ShieldPropertyService($parentObject, $shield);
        $this->shield = new GameObjectProperty($shield, $calculationService);

        $calculationService = new AttackPropertyService($parentObject, $attack);
        $this->attack = new GameObjectProperty($attack, $calculationService);

        $calculationService = new SpeedPropertyService($parentObject, $speed);
        $this->speed = new GameObjectProperty($speed, $calculationService);

        $calculationService = new CapacityPropertyService($parentObject, $capacity);
        $this->capacity = new GameObjectProperty($capacity, $calculationService);

        $calculationService = new FuelPropertyService($parentObject, $fuel);
        $this->fuel = new GameObjectProperty($fuel, $calculationService);
    }
}