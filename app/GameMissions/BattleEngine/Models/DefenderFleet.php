<?php

namespace OGame\GameMissions\BattleEngine\Models;

use OGame\Services\ObjectService;
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
     * Note: This excludes interplanetary missiles and anti-ballistic missiles
     * from combat, as they should not participate in fleet battles.
     * - ABMs only intercept IPMs during missile attacks
     * - IPMs only attack defenses via the MissileMission
     *
     * @param PlanetService $planet
     * @return self
     */
    public static function fromPlanet(PlanetService $planet): self
    {
        $defender = new self();

        // Collect all units on the planet (ships + defenses)
        // but exclude missiles which should not participate in combat
        $defender->units = new UnitCollection();
        $defender->units->addCollection($planet->getShipUnits());
        $defender->units->addCollection(self::getDefenseUnitsForCombat($planet));

        $defender->player = $planet->getPlayer();
        $defender->fleetMissionId = 0; // 0 indicates stationary planet forces
        $defender->ownerId = $planet->getPlayer()->getId();
        $defender->fleetMission = null;

        return $defender;
    }

    /**
     * Get defense units for combat, excluding missiles.
     *
     * Missiles (interplanetary and anti-ballistic) should not participate
     * in fleet combat. They are only used in missile attacks.
     *
     * @param PlanetService $planet
     * @return UnitCollection
     */
    private static function getDefenseUnitsForCombat(PlanetService $planet): UnitCollection
    {
        $units = new UnitCollection();
        $objects = ObjectService::getDefenseObjects();
        foreach ($objects as $object) {
            // Skip missiles - they should not participate in combat
            if (in_array($object->machine_name, [
                'interplanetary_missile',
                'anti_ballistic_missile',
            ])) {
                continue;
            }

            $amount = $planet->getObjectAmount($object->machine_name);
            if ($amount > 0) {
                $units->addUnit($object, $amount);
            }
        }

        return $units;
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
