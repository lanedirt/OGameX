<?php

namespace OGame\GameMissions\BattleEngine\Models;

use OGame\GameObjects\Models\UnitObject;

/**
 * Model class that represents a unit in a battle keeping track of its health and other properties.
 */
class BattleUnit
{
    /**
     * @var int The original hull plating of the unit. This is the structural integrity of the unit divided by 10.
     */
    public int $originalHullPlating;

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
     * Create a new BattleUnit object.
     *
     * @param UnitObject $unitObject
     * @param int $structuralIntegrity
     * @param int $originalShieldPoints
     * @param int $attackPower
     * @param int $fleetMissionId
     * @param int $ownerId
     */
    public function __construct(public UnitObject $unitObject, int $structuralIntegrity, public int $originalShieldPoints, public int $attackPower, public int $fleetMissionId, public int $ownerId)
    {
        // Hull plating is the structural integrity divided by 10.
        $hullPlating = $structuralIntegrity / 10;
        $this->originalHullPlating = $hullPlating;
        $this->currentHullPlating = $hullPlating;
        $this->currentShieldPoints = $this->originalShieldPoints;
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
