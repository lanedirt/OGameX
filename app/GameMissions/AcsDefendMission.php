<?php

namespace OGame\GameMissions;

use OGame\Enums\FleetMissionStatus;
use OGame\Enums\FleetSpeedType;
use OGame\GameMessages\AcsDefendArrivalHost;
use OGame\GameMessages\AcsDefendArrivalSender;
use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameMissions\Models\MissionPossibleStatus;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\BuddyRequest;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\Services\PlanetService;

class AcsDefendMission extends GameMission
{
    protected static string $name = 'ACS Defend';
    protected static int $typeId = 5;
    protected static bool $hasReturnMission = true;
    protected static FleetSpeedType $fleetSpeedType = FleetSpeedType::war;
    protected static FleetMissionStatus $friendlyStatus = FleetMissionStatus::Friendly;

    /**
     * @inheritdoc
     */
    public function isMissionPossible(PlanetService $planet, Coordinate $targetCoordinate, PlanetType $targetType, UnitCollection $units): MissionPossibleStatus
    {
        // Cannot send missions while in vacation mode
        if ($planet->getPlayer()->isInVacationMode()) {
            return new MissionPossibleStatus(false, 'You cannot send missions while in vacation mode!');
        }

        // ACS Defend mission is only possible for planets and moons.
        if (!in_array($targetType, [PlanetType::Planet, PlanetType::Moon])) {
            return new MissionPossibleStatus(false);
        }

        // If target planet does not exist, the mission is not possible.
        $targetPlanet = $this->planetServiceFactory->makeForCoordinate($targetCoordinate, true, $targetType);
        if ($targetPlanet === null) {
            return new MissionPossibleStatus(false);
        }

        // Cannot send ACS Defend to own planet
        if ($planet->getPlayer()->equals($targetPlanet->getPlayer())) {
            return new MissionPossibleStatus(false);
        }

        // If mission from and to coordinates and types are the same, the mission is not possible.
        if ($planet->getPlanetCoordinates()->equals($targetCoordinate) && $planet->getPlanetType() === $targetType) {
            return new MissionPossibleStatus(false);
        }

        // If target player is in vacation mode, the mission is not possible.
        $targetPlayer = $targetPlanet->getPlayer();
        if ($targetPlayer->isInVacationMode()) {
            return new MissionPossibleStatus(false, 'This player is in vacation mode!');
        }

        // Check if players are buddies (accepted buddy request exists)
        $currentUserId = $planet->getPlayer()->getUser()->id;
        $targetUserId = $targetPlayer->getUser()->id;

        $isBuddy = BuddyRequest::where('status', BuddyRequest::STATUS_ACCEPTED)
            ->where(function ($query) use ($currentUserId, $targetUserId) {
                $query->where(function ($q) use ($currentUserId, $targetUserId) {
                    $q->where('sender_user_id', $currentUserId)
                        ->where('receiver_user_id', $targetUserId);
                })
                ->orWhere(function ($q) use ($currentUserId, $targetUserId) {
                    $q->where('sender_user_id', $targetUserId)
                        ->where('receiver_user_id', $currentUserId);
                });
            })
            ->exists();

        // TODO: Add alliance check when alliance system is implemented
        // For now, only allow ACS Defend to buddy planets
        if (!$isBuddy) {
            return new MissionPossibleStatus(false, 'You can only send ACS Defend missions to buddies!');
        }

        // If all checks pass, the mission is possible.
        return new MissionPossibleStatus(true);
    }

    /**
     * @inheritdoc
     */
    protected function processArrival(FleetMission $mission): void
    {
        $origin_planet = $this->planetServiceFactory->make($mission->planet_id_from, true);
        $target_planet = $this->planetServiceFactory->make($mission->planet_id_to, true);

        // Send message to sender (Fleet Command)
        $this->messageService->sendSystemMessageToPlayer($origin_planet->getPlayer(), AcsDefendArrivalSender::class, [
            'to' => '[planet]' . $mission->planet_id_to . '[/planet]',
        ]);

        // Send message to host/target (Space Monitoring)
        $this->messageService->sendSystemMessageToPlayer($target_planet->getPlayer(), AcsDefendArrivalHost::class, [
            'to' => '[planet]' . $mission->planet_id_to . '[/planet]',
        ]);

        // Mark the arrival mission as processed
        $mission->processed = 1;
        $mission->save();

        // Create and start the return mission after hold time expires
        // Preserve any resources sent with the fleet (like recycle mission behavior)
        $resources = $this->fleetMissionService->getResources($mission);
        $units = $this->fleetMissionService->getFleetUnits($mission);
        $this->startReturn($mission, $resources, $units);
    }

    /**
     * @inheritdoc
     */
    protected function processReturn(FleetMission $mission): void
    {
        // Load the origin planet
        $target_planet = $this->planetServiceFactory->make($mission->planet_id_to, true);

        // Return units to the source planet
        $target_planet->addUnits($this->fleetMissionService->getFleetUnits($mission));

        // Send message to player that the return mission has arrived.
        $this->sendFleetReturnMessage($mission, $target_planet->getPlayer());

        // Mark the return mission as processed
        $mission->processed = 1;
        $mission->save();
    }
}
