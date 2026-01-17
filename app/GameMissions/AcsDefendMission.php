<?php

namespace OGame\GameMissions;

use OGame\Enums\FleetMissionStatus;
use OGame\Enums\FleetSpeedType;
use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameMissions\Models\MissionPossibleStatus;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\Services\AllianceService;
use OGame\Services\BuddyService;
use OGame\Services\PlanetService;

class AcsDefendMission extends GameMission
{
    protected static string $name = 'ACS Defend';
    protected static int $typeId = 5;
    protected static bool $hasReturnMission = true;
    protected static FleetSpeedType $fleetSpeedType = FleetSpeedType::holding;
    protected static FleetMissionStatus $friendlyStatus = FleetMissionStatus::Friendly;

    /**
     * @inheritdoc
     */
    public function isMissionPossible(PlanetService $planet, Coordinate $targetCoordinate, PlanetType $targetType, UnitCollection $units): MissionPossibleStatus
    {
        // Check parent conditions (vacation mode, same coordinates)
        $parentCheck = parent::isMissionPossible($planet, $targetCoordinate, $targetType, $units);
        if (!$parentCheck->possible) {
            return $parentCheck;
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
        if ($ownPlanetCheck = $this->checkOwnPlanet($planet, $targetPlanet)) {
            return $ownPlanetCheck;
        }

        // If target player is in vacation mode, the mission is not possible.
        if ($vacationCheck = $this->checkTargetVacationMode($targetPlanet)) {
            return $vacationCheck;
        }

        // Check if players are buddies (accepted buddy request exists) or in the same alliance
        $currentUserId = $planet->getPlayer()->getUser()->id;
        $targetUserId = $targetPlanet->getPlayer()->getUser()->id;

        $buddyService = app(BuddyService::class);
        $isBuddy = $buddyService->areBuddies($currentUserId, $targetUserId);

        $allianceService = app(AllianceService::class);
        $isAllianceMember = $allianceService->arePlayersInSameAlliance($currentUserId, $targetUserId);

        // Only allow ACS Defend to buddies or alliance members
        if (!$isBuddy && !$isAllianceMember) {
            return new MissionPossibleStatus(false, __('You can only send ACS Defend missions to buddies or alliance members!'));
        }

        // If all checks pass, the mission is possible.
        return new MissionPossibleStatus(true);
    }

    /**
     * @inheritdoc
     */
    protected function processArrival(FleetMission $mission): void
    {
        // Note: Arrival messages are sent earlier when the fleet physically arrives (start of hold time)
        // via FleetMissionService::sendAcsDefendArrivalMessages()
        // This method is called after the hold time expires to create the return mission

        // Create and start the return mission
        $this->startReturn($mission, $this->fleetMissionService->getResources($mission), $this->fleetMissionService->getFleetUnits($mission));

        // Mark the arrival mission as processed
        $mission->processed = 1;
        $mission->save();
    }

    /**
     * @inheritdoc
     */
    protected function processReturn(FleetMission $mission): void
    {
        // Load the destination planet (where ships are returning to)
        $destination_planet = $this->planetServiceFactory->make($mission->planet_id_to, true);

        // Return units to the destination planet
        $destination_planet->addUnits($this->fleetMissionService->getFleetUnits($mission));

        // Add resources to the destination planet (if any).
        $return_resources = $this->fleetMissionService->getResources($mission);
        if ($return_resources->any()) {
            $destination_planet->addResources($return_resources);
        }

        // Send message to player that the return mission has arrived.
        $this->sendFleetReturnMessage($mission, $destination_planet->getPlayer());

        // Mark the return mission as processed
        $mission->processed = 1;
        $mission->save();
    }
}
