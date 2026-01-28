<?php

namespace OGame\GameMissions\BattleEngine\Models;

use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Resources;

/**
 * Tracks battle results for a specific attacking fleet in an ACS battle.
 */
class AttackerFleetResult
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
     * @var Resources The resources in terms of ships that this fleet lost.
     */
    public Resources $resourceLoss;

    /**
     * @var Resources This fleet's share of the loot.
     */
    public Resources $lootShare;

    /**
     * @var Resources Cargo resources that survived (proportional to surviving cargo capacity).
     */
    public Resources $survivingCargo;

    /**
     * @var bool Whether this fleet was completely destroyed.
     */
    public bool $completelyDestroyed;

    /**
     * Create a new AttackerFleetResult.
     *
     * @param int $fleetMissionId
     * @param int $playerId
     * @param UnitCollection $unitsStart
     */
    public function __construct(public int $fleetMissionId, public int $playerId, UnitCollection $unitsStart)
    {
        $this->unitsStart = clone $unitsStart;
        $this->unitsResult = new UnitCollection();
        $this->unitsLost = new UnitCollection();
        $this->resourceLoss = new Resources(0, 0, 0, 0);
        $this->lootShare = new Resources(0, 0, 0, 0);
        $this->survivingCargo = new Resources(0, 0, 0, 0);
        $this->completelyDestroyed = false;
    }

    /**
     * Calculate resource loss based on units lost.
     *
     * @return void
     */
    public function calculateResourceLoss(): void
    {
        $this->resourceLoss = $this->unitsLost->toResources();
    }

    /**
     * Check if this fleet has any survivors.
     *
     * @return bool
     */
    public function hasSurvivors(): bool
    {
        return $this->unitsResult->getAmount() > 0;
    }

    /**
     * Get the total number of ships at start.
     *
     * @return int
     */
    public function getStartCount(): int
    {
        return $this->unitsStart->getAmount();
    }

    /**
     * Get the total number of surviving ships.
     *
     * @return int
     */
    public function getSurvivorCount(): int
    {
        return $this->unitsResult->getAmount();
    }
}
