<?php

namespace OGame\GameMissions;

use OGame\Enums\FleetMissionStatus;
use OGame\Enums\FleetSpeedType;
use OGame\GameMessages\TransportArrived;
use OGame\GameMessages\TransportReceived;
use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameMissions\Models\MissionPossibleStatus;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\Services\PlanetService;
use RuntimeException;

class TransportMission extends GameMission
{
    protected static string $name = 'Transport';
    protected static int $typeId = 3;
    protected static bool $hasReturnMission = true;
    protected static FleetSpeedType $fleetSpeedType = FleetSpeedType::peaceful;
    protected static FleetMissionStatus $friendlyStatus = FleetMissionStatus::Neutral;

    /**
     * @inheritdoc
     */
    public function isMissionPossible(PlanetService $planet, Coordinate $targetCoordinate, PlanetType $targetType, UnitCollection $units): MissionPossibleStatus
    {
        $parentCheck = parent::isMissionPossible($planet, $targetCoordinate, $targetType, $units);
        if (!$parentCheck->possible) {
            return $parentCheck;
        }

        // Transport mission is only possible for planets and moons.
        if (!in_array($targetType, [PlanetType::Planet, PlanetType::Moon])) {
            return new MissionPossibleStatus(false);
        }

        // If target planet does not exist, the mission is not possible.
        $targetPlanet = $this->planetServiceFactory->makeForCoordinate($targetCoordinate, true, $targetType);
        if ($targetPlanet === null) {
            return new MissionPossibleStatus(false);
        }

        // If target player is in vacation mode, the mission is not possible.
        if ($vacationCheck = $this->checkTargetVacationMode($targetPlanet)) {
            return $vacationCheck;
        }

        // If all checks pass, the mission is possible.
        return new MissionPossibleStatus(true);
    }

    /**
     * @inheritdoc
     */
    protected function processArrival(FleetMission $mission): void
    {
        if ($mission->planet_id_from === null || $mission->planet_id_to === null) {
            throw new RuntimeException('Transport mission is missing origin or target planet.');
        }
        $origin_planet = $this->planetServiceFactory->make($mission->planet_id_from, true);
        $target_planet = $this->planetServiceFactory->make($mission->planet_id_to, true);
        if ($origin_planet === null || $target_planet === null) {
            throw new RuntimeException('Transport mission origin or target planet does not exist.');
        }
        $originPlayer = $origin_planet->getPlayer();
        $targetPlayer = $target_planet->getPlayer();
        if ($originPlayer === null || $targetPlayer === null) {
            throw new RuntimeException('Transport mission origin or target planet has no owner.');
        }

        // Add resources to the target planet
        $target_planet->addResources($this->fleetMissionService->getResources($mission));

        // Send a message to the origin player that the mission has arrived
        $this->messageService->sendSystemMessageToPlayer($originPlayer, TransportArrived::class, [
            'from' => '[planet]' . $mission->planet_id_from . '[/planet]',
            'to' => '[planet]' . $mission->planet_id_to . '[/planet]',
            'metal' => (string)$mission->metal,
            'crystal' => (string)$mission->crystal,
            'deuterium' => (string)$mission->deuterium,
        ]);

        if ($originPlayer->getId() !== $targetPlayer->getId()) {
            // Send a message to the target player that the mission has arrived
            $this->messageService->sendSystemMessageToPlayer($targetPlayer, TransportReceived::class, [
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
        // Transport delivers all resources, so return with empty cargo.
        $units = $this->fleetMissionService->getFleetUnits($mission);
        $this->startReturn($mission, new Resources(0, 0, 0, 0), $units);
    }

    /**
     * @inheritdoc
     */
    protected function processReturn(FleetMission $mission): void
    {
        // Load the target planet
        if ($mission->planet_id_to === null) {
            throw new RuntimeException('Transport return mission has no target planet.');
        }
        $target_planet = $this->planetServiceFactory->make($mission->planet_id_to, true);
        if ($target_planet === null) {
            throw new RuntimeException('Transport return mission target planet does not exist.');
        }
        $targetPlayer = $target_planet->getPlayer();
        if ($targetPlayer === null) {
            throw new RuntimeException('Transport return mission target planet has no owner.');
        }

        // Transport return trip: add back the units to the source planet.
        $target_planet->addUnits($this->fleetMissionService->getFleetUnits($mission));

        // Add resources to the origin planet (if any).
        $return_resources = $this->fleetMissionService->getResources($mission);
        if ($return_resources->any()) {
            $target_planet->addResources($return_resources);
        }

        // Send message to player that the return mission has arrived.
        $this->sendFleetReturnMessage($mission, $targetPlayer);

        // Mark the return mission as processed
        $mission->processed = 1;
        $mission->save();
    }
}
