<?php

namespace OGame\GameMissions\BattleEngine\Models;

use OGame\GameObjects\Models\Units\UnitCollection;

/**
 * Tracks battle results for a specific defending fleet.
 */
class DefenderFleetResult
{
    /**
     * @var int The fleet mission ID (0 for planet owner's stationary forces).
     */
    public int $fleetMissionId;

    /**
     * @var int The ID of the player who owns this fleet.
     */
    public int $ownerId;

    /**
     * @var UnitCollection Units at the start of battle.
     */
    public UnitCollection $unitsStart;

    /**
     * @var UnitCollection Units remaining after battle.
     */
    public UnitCollection $unitsResult;

    /**
     * @var UnitCollection Units lost during battle.
     */
    public UnitCollection $unitsLost;

    /**
     * @var bool Whether this fleet was completely destroyed.
     */
    public bool $completelyDestroyed;

    /**
     * Create a new DefenderFleetResult.
     *
     * @param int $fleetMissionId
     * @param int $ownerId
     * @param UnitCollection $unitsStart
     */
    public function __construct(int $fleetMissionId, int $ownerId, UnitCollection $unitsStart)
    {
        $this->fleetMissionId = $fleetMissionId;
        $this->ownerId = $ownerId;
        $this->unitsStart = clone $unitsStart;
        $this->unitsResult = new UnitCollection();
        $this->unitsLost = new UnitCollection();
        $this->completelyDestroyed = false;
    }
}
