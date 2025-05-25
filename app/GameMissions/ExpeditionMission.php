<?php

namespace OGame\GameMissions;

use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameMissions\Models\MissionPossibleStatus;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\Services\PlanetService;

class ExpeditionMission extends GameMission
{
    protected static string $name = 'Expedition';
    protected static int $typeId = 15;
    protected static bool $hasReturnMission = false;

    protected static array $outcomes = [
        [
            'success' => true,
            'message' => 'On an isolated planetoid we found some easily accessible resources fields and harvested some successfully.',
            'resources' => new Resources(1, 1, 0, 0),
        ],
        [
            'success' => true,
            'message' => 'Our expedition found a planet which was almost destroyed during a certain chain of wars. There are different ships floating around in the orbit. The technicians are trying to repair some of them. Maybe we will also get information about what happened here.',
            'units' => new UnitCollection(),
        ],
        [
            'success' => false,
            'message' => 'Due to a failure in the central computers of the flagship, the expedition mission had to be aborted. Unfortunately as a result of the computer malfunction, the fleet returns home empty handed.',
        ],
        [
            'success' => false,
            'message' => 'Your expedition nearly ran into a neutron stars gravitation field and needed some time to free itself. Because of that a lot of Deuterium was consumed and the expedition fleet had to come back without any results.',
        ],
        [
            'success' => false,
            'message' => 'Your expedition went into a sector full of particle storms. This set the energy stores to overload and most of the ships` main systems crashed. Your mechanics were able to avoid the worst, but the expedition is going to return with a big delay.',
        ],
    ];

    /**
     * @inheritdoc
     */
    public function isMissionPossible(PlanetService $planet, Coordinate $targetCoordinate, PlanetType $targetType, UnitCollection $units): MissionPossibleStatus
    {
        // Expedition mission is only possible for position 16.
        if ($targetCoordinate->position !== 16) {
            return new MissionPossibleStatus(false);
        }

        // Mission is only possible towards a planet.
        if ($targetType === PlanetType::Moon) {
            return new MissionPossibleStatus(false, __('Error, there is no moon'));
        }

        // Only possible if player has astrophysics research level 1 or higher.
        if ($planet->getPlayer()->getResearchLevel('astrophysics') <= 0) {
            return new MissionPossibleStatus(false, __('Fleets cannot be sent to this target. You have to research Astrophysics first.'));
        }

        if ($targetType === PlanetType::DebrisField) {
            // TODO: this logic should check if there are actually pathfinders in the units collection
            // once the pathfinder unit has been added to the game.
            return new MissionPossibleStatus(false, __('Pathfinders must be sent for recycling!'));
        }

        // If all checks pass, the mission is possible.
        return new MissionPossibleStatus(true);
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    protected function processArrival(FleetMission $mission): void
    {
        // TODO: Implement processArrival() method with expedition random events, loot gained etc.
        // TODO: add logic to send confirmation message to player with the results of the expedition.

        // Mark the arrival mission as processed
        $mission->processed = 1;
        $mission->save();

        $units = $this->fleetMissionService->getFleetUnits(mission: $mission);

        // Create and start the return mission.
        $this->startReturn($mission, new Resources(0, 0, 0, 0), $units);
    }

    /**
     * @inheritdoc
     */
    protected function processReturn(FleetMission $mission): void
    {
        $target_planet = $this->planetServiceFactory->make($mission->planet_id_to, true);

        // Expedition mission: add back the units to the source planet.
        $target_planet->addUnits($this->fleetMissionService->getFleetUnits($mission));

        // Add resources to the origin planet (if any).
        $return_resources = $this->fleetMissionService->getResources($mission);
        if ($return_resources->any()) {
            $target_planet->addResources($return_resources);
        }

        // Send message to player that the return mission has arrived.
        $this->sendFleetReturnMessage($mission, $target_planet->getPlayer());

        // Mark the return mission as processed
        $mission->processed = 1;
        $mission->save();
    }
}
