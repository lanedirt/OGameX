<?php

namespace OGame\GameMissions;

use OGame\GameMessages\TransportArrived;
use OGame\GameMessages\TransportReceived;
use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameMissions\Models\MissionPossibleStatus;
use OGame\GameObjects\Models\UnitCollection;
use OGame\Models\FleetMission;
use OGame\Services\PlanetService;

class TransportMission extends GameMission
{
    protected static string $name = 'Transport';
    protected static int $typeId = 3;
    protected static bool $hasReturnMission = true;

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

    protected function processArrival(FleetMission $mission): void
    {
        // Load origin planet
        $origin_planet = $this->planetServiceFactory->make($mission->planet_id_from);
        // Load the target planet
        $target_planet = $this->planetServiceFactory->make($mission->planet_id_to);

        // Add resources to the target planet
        $target_planet->addResources($this->fleetMissionService->getResources($mission));

        // Send a message to the origin player that the mission has arrived
        // TODO: make message content translatable by using tokens instead of directly inserting dynamic content.
        $this->messageService->sendSystemMessageToPlayer($origin_planet->getPlayer(), TransportArrived::class, [
            'from' => '[planet]' . $mission->planet_id_from . '[/planet]',
            'to' => '[planet]' . $mission->planet_id_to . '[/planet]',
            'metal' => (string)$mission->metal,
            'crystal' => (string)$mission->crystal,
            'deuterium' => (string)$mission->deuterium,
        ]);

        if ($origin_planet->getPlayer()->getId() !== $target_planet->getPlayer()->getId()) {
            // Send a message to the target player that the mission has arrived
            $this->messageService->sendSystemMessageToPlayer($target_planet->getPlayer(), TransportReceived::class, [
                'from' => '[planet]' . $mission->planet_id_from . '[/planet]',
                'to' => '[planet]' . $mission->planet_id_to . '[/planet]',
                'metal' => (string)$mission->metal,
                'crystal' => (string)$mission->crystal,
                'deuterium' => (string)$mission->deuterium,
            ]);
        }

        // Mark the arrival mission as processed
        $mission->processed = 1;
        $mission->save();

        // Create and start the return mission.
        $this->startReturn($mission);
    }

    /**
     * @inheritdoc
     */
    protected function processReturn(FleetMission $mission): void
    {
        // Load the target planet
        $target_planet = $this->planetServiceFactory->make($mission->planet_id_to);

        // Transport return trip: add back the units to the source planet.
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
