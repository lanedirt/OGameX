<?php

namespace OGame\GameMissions;

use OGame\GameMessages\FleetDeployment;
use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameMissions\Models\MissionPossibleStatus;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Planet\Coordinate;
use OGame\Services\ACSService;
use OGame\Services\PlanetService;

class ACSDefendMission extends GameMission
{
    protected static string $name = 'ACS Defend';
    protected static int $typeId = 5;
    protected static bool $hasReturnMission = true;

    /**
     * Maximum hold time in hours
     */
    private const MAX_HOLD_HOURS = 32;

    /**
     * @inheritdoc
     */
    public function isMissionPossible(PlanetService $planet, Coordinate $targetCoordinate, PlanetType $targetType, UnitCollection $units): MissionPossibleStatus
    {
        // ACS Defend mission is only possible for planets and moons.
        if (!in_array($targetType, [PlanetType::Planet, PlanetType::Moon])) {
            return new MissionPossibleStatus(false);
        }

        // If target planet does not exist, the mission is not possible.
        $targetPlanet = $this->planetServiceFactory->makeForCoordinate($targetCoordinate, true, $targetType);
        if ($targetPlanet === null) {
            return new MissionPossibleStatus(false);
        }

        // Cannot defend your own planet (use deployment for that)
        if ($planet->getPlayer()->equals($targetPlanet->getPlayer())) {
            return new MissionPossibleStatus(false);
        }

        // Check if target player is buddy or alliance member
        if (!ACSService::isBuddyOrAllianceMember($planet->getPlayer()->getId(), $targetPlanet->getPlayer()->getId())) {
            return new MissionPossibleStatus(false);
        }

        // If mission from and to coordinates and types are the same, the mission is not possible.
        if ($planet->getPlanetCoordinates()->equals($targetCoordinate) && $planet->getPlanetType() === $targetType) {
            return new MissionPossibleStatus(false);
        }

        // If all checks pass, the mission is possible.
        return new MissionPossibleStatus(true);
    }

    /**
     * Process arrival of defending fleet
     * Fleet arrives and hold duration completes - calculate consumption and return
     */
    protected function processArrival(FleetMission $mission): void
    {
        // Fleet has completed its hold duration
        // Calculate deuterium consumed during the hold period

        $originPlanet = $this->planetServiceFactory->make($mission->planet_id_from, true);
        $targetPlanet = $this->planetServiceFactory->make($mission->planet_id_to, true);

        // Get hold duration in hours
        $holdDurationSeconds = $mission->time_holding ?? 0;
        $holdDurationHours = $holdDurationSeconds / 3600;

        // Calculate total deuterium consumed during hold
        $holdConsumptionService = new \OGame\Services\FleetHoldConsumptionService();
        $units = $this->fleetMissionService->getFleetUnits($mission);
        $totalConsumptionNeeded = $holdConsumptionService->calculateTotalConsumption($units, (int)$holdDurationHours);

        // Check if target planet has Alliance Depot
        $allianceDepot = \OGame\Services\ObjectService::getObjectByMachineName('alliance_depot');
        $depotLevel = $targetPlanet->getObjectLevel($allianceDepot->id);

        // Calculate Alliance Depot supply (20,000 deut/hour per level)
        $depotSupplyRate = 20000; // per hour per level
        $depotSupplyAvailable = $depotLevel * $depotSupplyRate * $holdDurationHours;

        // Check how much deuterium the planet actually has
        $planetDeuterium = $targetPlanet->deuterium();
        $depotSupplyUsed = min($depotSupplyAvailable, $planetDeuterium, $totalConsumptionNeeded);

        // Deduct depot supply from planet storage
        if ($depotSupplyUsed > 0) {
            $targetPlanet->deductResources(new \OGame\Models\Resources(0, 0, $depotSupplyUsed, 0));
        }

        // Calculate how much fleet cargo needs to cover
        $fleetCargoConsumption = max(0, $totalConsumptionNeeded - $depotSupplyUsed);

        // Deduct consumed deuterium from mission cargo
        $originalDeuterium = $mission->deuterium;
        $mission->deuterium = max(0, $originalDeuterium - $fleetCargoConsumption);

        \Log::debug('ACS Defend hold completed - deuterium consumed', [
            'mission_id' => $mission->id,
            'hold_hours' => $holdDurationHours,
            'total_consumption_needed' => $totalConsumptionNeeded,
            'depot_level' => $depotLevel,
            'depot_supply_available' => $depotSupplyAvailable,
            'depot_supply_used' => $depotSupplyUsed,
            'planet_deuterium_before' => $planetDeuterium,
            'fleet_cargo_original' => $originalDeuterium,
            'fleet_cargo_consumed' => $fleetCargoConsumption,
            'fleet_cargo_remaining' => $mission->deuterium,
        ]);

        // Send a message to the fleet owner
        $this->messageService->sendSystemMessageToPlayer($originPlanet->getPlayer(), FleetDeployment::class, [
            'from' => '[planet]' . $mission->planet_id_from . '[/planet]',
            'to' => '[planet]' . $mission->planet_id_to . '[/planet]',
        ]);

        // Send message to planet owner that defense has ended
        $this->messageService->sendSystemMessageToPlayer($targetPlanet->getPlayer(), FleetDeployment::class, [
            'from' => '[planet]' . $mission->planet_id_from . '[/planet]',
            'to' => '[planet]' . $mission->planet_id_to . '[/planet]',
        ]);

        // Mark the arrival mission as processed
        $mission->processed = 1;
        $mission->save();

        // Create and start the return mission with remaining resources
        $this->startReturn($mission, new \OGame\Models\Resources(0, 0, 0, 0), $units);
    }

    /**
     * Process return of defending fleet
     * Fleet returns home after hold duration expires
     */
    protected function processReturn(FleetMission $mission): void
    {
        $originPlanet = $this->planetServiceFactory->make($mission->planet_id_from, true);

        // Add units back to origin planet
        $originPlanet->addUnits($this->fleetMissionService->getFleetUnits($mission));

        // Add any remaining resources back
        $return_resources = $this->fleetMissionService->getResources($mission);
        if ($return_resources->any()) {
            $originPlanet->addResources($return_resources);
        }

        // Send message to player that the fleet has returned
        $this->sendFleetReturnMessage($mission, $originPlanet->getPlayer());

        // Mark the return mission as processed
        $mission->processed = 1;
        $mission->save();
    }

    /**
     * Get maximum hold time in hours
     */
    public static function getMaxHoldHours(): int
    {
        return self::MAX_HOLD_HOURS;
    }
}
