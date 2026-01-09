<?php

namespace OGame\GameMissions\BattleEngine\Models;

use OGame\Factories\PlayerServiceFactory;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\FleetMission;
use OGame\Services\FleetMissionService;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;

/**
 * Represents a single defending fleet in a battle.
 * Can be either the planet owner's stationary forces or an ACS defend fleet.
 */
class DefenderFleet
{
    /**
     * @var UnitCollection The units in this defending fleet.
     */
    public UnitCollection $units;

    /**
     * @var PlayerService The player who owns this defending fleet.
     */
    public PlayerService $player;

    /**
     * @var int The fleet mission ID (0 for planet owner's stationary forces).
     */
    public int $fleetMissionId;

    /**
     * @var int The ID of the player who owns this fleet.
     */
    public int $ownerId;

    /**
     * @var FleetMission|null The fleet mission (null for planet owner's stationary forces).
     */
    public ?FleetMission $fleetMission;

    /**
     * Create a DefenderFleet from a planet's stationary forces.
     *
     * @param PlanetService $planet
     * @return self
     */
    public static function fromPlanet(PlanetService $planet): self
    {
        $defender = new self();

        // Collect all units on the planet (ships + defenses)
        $defender->units = new UnitCollection();
        $defender->units->addCollection($planet->getShipUnits());
        $defender->units->addCollection($planet->getDefenseUnits());

        $defender->player = $planet->getPlayer();
        $defender->fleetMissionId = 0; // 0 indicates stationary planet forces
        $defender->ownerId = $planet->getPlayer()->getId();
        $defender->fleetMission = null;

        return $defender;
    }

    /**
     * Create a DefenderFleet from an ACS defend fleet mission.
     *
     * @param FleetMission $mission
     * @param FleetMissionService $fleetMissionService
     * @param PlayerServiceFactory $playerServiceFactory
     * @return self
     */
    public static function fromFleetMission(
        FleetMission $mission,
        FleetMissionService $fleetMissionService,
        PlayerServiceFactory $playerServiceFactory
    ): self {
        $defender = new self();

        $defender->units = $fleetMissionService->getFleetUnits($mission);
        $defender->player = $playerServiceFactory->make($mission->user_id, true);
        $defender->fleetMissionId = $mission->id;
        $defender->ownerId = $mission->user_id;
        $defender->fleetMission = $mission;

        return $defender;
    }
}
