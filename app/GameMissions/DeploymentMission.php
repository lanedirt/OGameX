<?php

namespace OGame\GameMissions;

use OGame\GameMessages\FleetDeployment;
use OGame\GameMessages\FleetDeploymentWithResources;
use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameMissions\Models\MissionPossibleStatus;
use OGame\GameObjects\Models\UnitCollection;
use OGame\Models\FleetMission;
use OGame\Services\PlanetService;

class DeploymentMission extends GameMission
{
    protected static string $name = 'Deployment';
    protected static int $typeId = 4;
    protected static bool $hasReturnMission = false;

    /**
     * @inheritdoc
     */
    public function isMissionPossible(PlanetService $planet, ?PlanetService $targetPlanet, UnitCollection $units): MissionPossibleStatus
    {
        if ($targetPlanet != null) {
            if ($planet->getPlayer()->equals($targetPlanet->getPlayer())) {
                // If target player is the same as the current player, this mission is possible.
                return new MissionPossibleStatus(true);
            }
        }

        return new MissionPossibleStatus(false);
    }

    /**
     * @inheritdoc
     */
    protected function processArrival(FleetMission $mission): void
    {
        // Load the target planet
        $target_planet = $this->planetServiceFactory->make($mission->planet_id_to);

        // Add resources to the target planet
        $resources = $this->fleetMissionService->getResources($mission);
        $target_planet->addResources($resources);

        // Send a message to the player that the mission has arrived
        // TODO: make message content translatable by using tokens instead of directly inserting dynamic content.
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
        // Load the target planet
        $target_planet = $this->planetServiceFactory->make($mission->planet_id_to);

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
