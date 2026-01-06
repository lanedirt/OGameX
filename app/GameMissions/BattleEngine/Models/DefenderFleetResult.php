<?php

namespace OGame\GameMissions\BattleEngine\Models;

use OGame\GameObjects\Models\Units\UnitCollection;

/**
 * Tracks battle results for a specific defending fleet.
 */
class DefenderFleetResult
{
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
    public function __construct(public int $fleetMissionId, public int $ownerId, UnitCollection $unitsStart)
    {
        $this->unitsStart = clone $unitsStart;
        $this->unitsResult = new UnitCollection();
        $this->unitsLost = new UnitCollection();
        $this->completelyDestroyed = false;
    }
}
