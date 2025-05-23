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
