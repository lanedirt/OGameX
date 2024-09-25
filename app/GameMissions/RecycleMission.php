<?php

namespace OGame\GameMissions;

use OGame\GameMessages\FleetDeployment;
use OGame\GameMessages\FleetDeploymentWithResources;
use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameMissions\Models\MissionPossibleStatus;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
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
    public function isMissionPossible(PlanetService $planet, Coordinate $targetCoordinate, PlanetType $targetType, UnitCollection $units): MissionPossibleStatus
    {
        // Recycle mission is only possible for debris fields.
        if ($targetType !== PlanetType::DebrisField) {
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
        // Load the debris field for the target coordinate.
        $debrisField = app(DebrisFieldService::class);
        $debrisField->loadForCoordinates(new Coordinate($mission->galaxy_to, $mission->system_to, $mission->position_to));

        // Recycle the debris field resources.
        $resources = $debrisField->getResources();

        // Take the maximum amount of resources that the fleet can carry.


        // Send a message to the player that the mission has arrived and the resources (if any) have been collected.
        if ($resources->any()) {
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

        // Create and start the return mission.
        $units = $this->fleetMissionService->getFleetUnits($mission);
        $this->startReturn($mission, new Resources(0, 0, 0, 0), $units);
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
