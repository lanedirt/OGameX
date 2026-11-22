<?php

namespace OGame\GameMissions\BattleEngine\Models;

use OGame\Factories\PlayerServiceFactory;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\FleetMission;
use OGame\Models\Resources;
use OGame\Services\FleetMissionService;
use OGame\Services\ObjectService;
use OGame\Services\PlayerService;

/**
 * Represents a single attacking fleet in an ACS battle.
 * Used to group ships by owner for survivor tracking and loot distribution.
 */
class AttackerFleet
{
    /**
     * @var UnitCollection The units in this attacking fleet.
     */
    public UnitCollection $units;

    /**
     * @var PlayerService The player who owns this attacking fleet.
     */
    public PlayerService $player;

    /**
     * @var int The fleet mission ID.
     */
    public int $fleetMissionId;

    /**
     * @var int The ID of the player who owns this fleet.
     */
    public int $ownerId;

    /**
     * @var Resources The cargo resources this fleet is carrying.
     */
    public Resources $cargoResources;

    /**
     * @var bool Whether this is the initiator fleet (created the union).
     */
    public bool $isInitiator;

    /**
     * @var FleetMission|null The fleet mission.
     */
    public ?FleetMission $fleetMission;

    /**
     * Create an AttackerFleet from a fleet mission.
     *
     * @param FleetMission $mission
     * @param FleetMissionService $fleetMissionService
     * @param PlayerServiceFactory $playerServiceFactory
     * @param bool $isInitiator
     * @return self
     */
    public static function fromFleetMission(
        FleetMission $mission,
        FleetMissionService $fleetMissionService,
        PlayerServiceFactory $playerServiceFactory,
        bool $isInitiator = false
    ): self {
        $attacker = new self();

        $attacker->units = $fleetMissionService->getFleetUnits($mission);
        $attacker->player = $playerServiceFactory->make($mission->user_id, true);
        $attacker->fleetMissionId = $mission->id;
        $attacker->ownerId = $mission->user_id;
        $attacker->cargoResources = $fleetMissionService->getResources($mission);
        $attacker->isInitiator = $isInitiator;
        $attacker->fleetMission = $mission;

        return $attacker;
    }

    /**
     * Convert fleet units to individual BattleUnit objects with owner tracking.
     * Each fleet uses its OWN player's tech levels for calculations.
     *
     * @param ObjectService $objectService
     * @return array<BattleUnit>
     */
    public function toBattleUnits(ObjectService $objectService): array
    {
        $battleUnits = [];

        foreach ($this->units->units as $unit) {
            // Always use OWN player's tech levels
            $structuralIntegrity = $unit->unitObject->properties->structural_integrity
                ->calculate($this->player)->totalValue;
            $shieldPoints = $unit->unitObject->properties->shield
                ->calculate($this->player)->totalValue;
            $attackPower = $unit->unitObject->properties->attack
                ->calculate($this->player)->totalValue;

            // Create template unit with this fleet's tech levels
            $unitTemplate = new BattleUnit(
                $unit->unitObject,
                $structuralIntegrity,
                $shieldPoints,
                $attackPower,
                $this->fleetMissionId,
                $this->ownerId
            );

            // Create individual ship instances
            for ($i = 0; $i < $unit->amount; $i++) {
                $battleUnits[] = clone $unitTemplate;
            }
        }

        return $battleUnits;
    }

    /**
     * Get the total cargo capacity of surviving units for this fleet.
     *
     * @param UnitCollection $survivingUnits
     * @return int
     */
    public function getSurvivingCargoCapacity(UnitCollection $survivingUnits): int
    {
        return $survivingUnits->getTotalCargoCapacity($this->player);
    }
}
