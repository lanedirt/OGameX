<?php

namespace OGame\GameMissions;

use OGame\GameMessages\FleetDeployment;
use OGame\GameMessages\FleetDeploymentWithResources;
use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameMissions\Models\MissionPossibleStatus;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\FleetMission;
use OGame\Models\Planet\Coordinate;
use OGame\Services\DebrisFieldService;
use OGame\Services\PlanetService;

class RecycleMission extends GameMission
{
    protected static string $name = 'Harvest';
    protected static int $typeId = 8;
    protected static bool $hasReturnMission = true;

    /**
     * @inheritdoc
     */
    public function isMissionPossible(PlanetService $planet, Coordinate $targetCoordinate, int $targetType, UnitCollection $units): MissionPossibleStatus
    {
        // Recycle mission is only possible for debris fields.
        if ($targetType !== 2) {
            return new MissionPossibleStatus(false);
        }

        // Check if debris field exists on the target coordinate.
        $debrisField = app(DebrisFieldService::class);
        $debrisField->loadForCoordinates($targetCoordinate);
        if (!$debrisField->getResources()->any()) {
            return new MissionPossibleStatus(false);
        }

        // If all checks pass, the mission is possible.
        return new MissionPossibleStatus(true);
    }

    /**
     * @inheritdoc
     */
    protected function processArrival(FleetMission $mission): void
    {
        $target_planet = $this->planetServiceFactory->make($mission->planet_id_to, true);

        // Add resources to the target planet
        $resources = $this->fleetMissionService->getResources($mission);
        $target_planet->addResources($resources);

        // Add units to the target planet
        $target_planet->addUnits($this->fleetMissionService->getFleetUnits($mission));

        // Send a message to the player that the mission has arrived
        if ($resources->sum() > 0) {
            $this->messageService->sendSystemMessageToPlayer($target_planet->getPlayer(), FleetDeploymentWithResources::class, [
                'from' => '[planet]' . $mission->planet_id_from . '[/planet]',
                'to' => '[planet]' . $mission->planet_id_to . '[/planet]',
                'metal' => (string)$mission->metal,
                'crystal' => (string)$mission->crystal,
                'deuterium' => (string)$mission->deuterium
            ]);
        } else {
            $this->messageService->sendSystemMessageToPlayer($target_planet->getPlayer(), FleetDeployment::class, [
                'from' => '[planet]' . $mission->planet_id_from . '[/planet]',
                'to' => '[planet]' . $mission->planet_id_to . '[/planet]',
            ]);
        }

        // Mark the arrival mission as processed
        $mission->processed = 1;
        $mission->save();
    }

    /**
     * @inheritdoc
     */
    protected function processReturn(FleetMission $mission): void
    {
        $target_planet = $this->planetServiceFactory->make($mission->planet_id_to, true);

        // Transport return trip: add back the units to the source planet. Then we're done.
        $target_planet->addUnits($this->fleetMissionService->getFleetUnits($mission));

        // Add resources to the origin planet (if any).
        $return_resources = $this->fleetMissionService->getResources($mission);
        if ($return_resources->sum() > 0) {
            $target_planet->addResources($return_resources);
        }

        // Send message to player that the return mission has arrived.
        $this->sendFleetReturnMessage($mission, $target_planet->getPlayer());

        // Mark the return mission as processed
        $mission->processed = 1;
        $mission->save();
    }
}
