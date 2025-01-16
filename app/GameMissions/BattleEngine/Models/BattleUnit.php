<?php

namespace OGame\GameMissions\BattleEngine\Models;

use OGame\GameObjects\Models\UnitObject;

/**
 * Model class that represents a unit in a battle keeping track of its health and other properties.
 */
class BattleUnit
{
    /**
     * @var UnitObject The unit object that this battle unit represents.
     */
    public UnitObject $unitObject;

    /**
     * @var int The original hull plating of the unit. This is the structural integrity of the unit divided by 10.
     */
    public int $originalHullPlating;

    /**
     * @var int The original shield points of the unit.
     */
    public int $originalShieldPoints;

    /**
     * @var int The current health points of the unit. Hull plating = structural integrity / 10.
     */
    public int $currentHullPlating;

    /**
     * @var int The shield points of the unit.
     *
     * Damage is first applied to the shield, then to the hull plating. After every round of combat, the shield regenerates.
     */
    public int $currentShieldPoints;

    /**
     * @var int The attack power of the unit. This is the amount of damage the unit deals to another unit in a single round of combat.
     */
    public int $attackPower;

    /**
     * Create a new BattleUnit object.
     *
     * @param UnitObject $unitObject
     * @param int $structuralIntegrity
     * @param int $shieldPoints
     * @param int $attackPower
     */
    public function __construct(UnitObject $unitObject, int $structuralIntegrity, int $shieldPoints, int $attackPower)
    {
        $this->unitObject = $unitObject;

        // Hull plating is the structural integrity divided by 10.
        $hullPlating = $structuralIntegrity / 10;
        $this->originalHullPlating = $hullPlating;
        $this->currentHullPlating = $hullPlating;

        $this->originalShieldPoints = $shieldPoints;
        $this->currentShieldPoints = $shieldPoints;

        $this->attackPower = $attackPower;
    }

    /**
     * When the hull plating of the unit is < 70% of original, the unit has 1 - currentHullPlating/originalHullPlating chance of exploding.
     *
     * This method rolls a dice and returns TRUE if the unit explodes, FALSE otherwise.
     *
     * @return bool
     */
    public function damagedHullExplosion(): bool
    {
        $hullPercentage = $this->currentHullPlating / $this->originalHullPlating;
        if ($hullPercentage >= 0.7) {
            return false;
        }

        $explosionChance = (1 - $hullPercentage) * 100;
        return rand(0, 100) < $explosionChance;
    }
}
