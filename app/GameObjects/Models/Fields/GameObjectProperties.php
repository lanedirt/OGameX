<?php

namespace OGame\GameObjects\Models\Fields;

use OGame\GameObjects\Models\Abstracts\GameObject;
use OGame\GameObjects\Services\Properties\AttackPropertyService;
use OGame\GameObjects\Services\Properties\CapacityPropertyService;
use OGame\GameObjects\Services\Properties\FuelPropertyService;
use OGame\GameObjects\Services\Properties\ShieldPropertyService;
use OGame\GameObjects\Services\Properties\SpeedPropertyService;
use OGame\GameObjects\Services\Properties\StructuralIntegrityPropertyService;

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
        $this->structural_integrity = new GameObjectProperty('Structural Integrity', $structural_integrity, $calculationService);

        $calculationService = new ShieldPropertyService($parentObject, $shield);
        $this->shield = new GameObjectProperty('Shield Strength', $shield, $calculationService);

        $calculationService = new AttackPropertyService($parentObject, $attack);
        $this->attack = new GameObjectProperty('Attack Strength', $attack, $calculationService);

        $calculationService = new SpeedPropertyService($parentObject, $speed);
        $this->speed = new GameObjectProperty('Speed', $speed, $calculationService);

        $calculationService = new CapacityPropertyService($parentObject, $capacity);
        $this->capacity = new GameObjectProperty('Cargo Capacity', $capacity, $calculationService);

        $calculationService = new FuelPropertyService($parentObject, $fuel);
        $this->fuel = new GameObjectProperty('Fuel usage (Deuterium)', $fuel, $calculationService);
    }
}
