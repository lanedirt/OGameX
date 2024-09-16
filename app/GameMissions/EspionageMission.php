<?php

namespace OGame\GameMissions;

use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameMissions\Models\MissionPossibleStatus;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\EspionageReport;
use OGame\Models\FleetMission;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\Services\PlanetService;
use Throwable;

class EspionageMission extends GameMission
{
    protected static string $name = 'Espionage';
    protected static int $typeId = 6;
    protected static bool $hasReturnMission = true;

    /**
     * @inheritdoc
     */
    public function isMissionPossible(PlanetService $planet, Coordinate $targetCoordinate, int $targetType, UnitCollection $units): MissionPossibleStatus
    {
        // Espionage mission is only possible for planets and moons.
        if (!in_array($targetType, [1, 3])) {
            return new MissionPossibleStatus(false);
        }

        $targetPlanet = $this->planetServiceFactory->makeForCoordinate($targetCoordinate);

        // If planet does not exist, the mission is not possible.
        if ($targetPlanet === null) {
            return new MissionPossibleStatus(false);
        }

        // If planet belongs to current player, the mission is not possible.
        if ($planet->getPlayer()->equals($targetPlanet->getPlayer())) {
            return new MissionPossibleStatus(false);
        }

        // If no espionage probes are present in the fleet, the mission is not possible.
        if ($units->getAmountByMachineName('espionage_probe') === 0) {
            return new MissionPossibleStatus(false);
        }

        // If all checks pass, the mission is possible.
        return new MissionPossibleStatus(true);
    }

    /**
     * @inheritdoc
     * @throws Throwable
     */
    protected function processArrival(FleetMission $mission): void
    {
        $target_planet = $this->planetServiceFactory->make($mission->planet_id_to, true);
        $origin_planet = $this->planetServiceFactory->make($mission->planet_id_from, true);

        // Trigger target planet update to make sure the espionage report is accurate.
        $target_planet->update();

        $reportId = $this->createEspionageReport($mission, $origin_planet, $target_planet);

        // Send a message to the player with a reference to the espionage report.
        $this->messageService->sendEspionageReportMessageToPlayer(
            $origin_planet->getPlayer(),
            $reportId,
        );

        // Mark the arrival mission as processed
        $mission->processed = 1;
        $mission->save();

        // Assembly new unit collection.
        $units = $this->fleetMissionService->getFleetUnits($mission);
        // TODO: a battle can happen if counter-espionage has taken place. Add logic for this using the battle system.

        // Create and start the return mission.
        $this->startReturn($mission, $this->fleetMissionService->getResources($mission), $units);
    }

    /**
     * @inheritdoc
     */
    protected function processReturn(FleetMission $mission): void
    {
        // Load the target planet
        $target_planet = $this->planetServiceFactory->make($mission->planet_id_to, true);

        // Espionage return trip: add back the units to the source planet. Then we're done.
        $target_planet->addUnits($this->fleetMissionService->getFleetUnits($mission));

        // Add resources to the origin planet (if any).
        $return_resources = $this->fleetMissionService->getResources($mission);
        if ($return_resources->sum() > 0) {
            $target_planet->addResources($return_resources);
        }

        // Espionage return mission does not send a return confirmation message to the user.

        // Mark the return mission as processed
        $mission->processed = 1;
        $mission->save();
    }

    /**
     * Creates an espionage report for the target planet.
     *
     * @param FleetMission $mission
     * @param PlanetService $originPlanet
     * @param PlanetService $targetPlanet
     * @return int
     */
    private function createEspionageReport(FleetMission $mission, PlanetService $originPlanet, PlanetService $targetPlanet): int
    {
        // Create new espionage report record.
        $report = new EspionageReport();
        $report->planet_galaxy = $targetPlanet->getPlanetCoordinates()->galaxy;
        $report->planet_system = $targetPlanet->getPlanetCoordinates()->system;
        $report->planet_position = $targetPlanet->getPlanetCoordinates()->position;

        $report->planet_user_id = $targetPlanet->getPlayer()->getId();

        $report->player_info = [
            'player_id' => (string)$targetPlanet->getPlayer()->getId(),
            'player_name' => $targetPlanet->getPlayer()->getUsername(),
        ];

        // Resources
        $report->resources = [
            'metal' => (int)$targetPlanet->metal()->get(),
            'crystal' => (int)$targetPlanet->crystal()->get(),
            'deuterium' => (int)$targetPlanet->deuterium()->get(),
            'energy' => (int)$targetPlanet->energy()->get()
        ];

        //TODO: Validate this does not cause issues when probing slot 16
        $attackerEspionnageLevel = $originPlanet->getPlayer()->getResearchLevel('espionage_technology');
        $defenderEspionnageLevel = $targetPlanet->getPlayer()->getResearchLevel('espionage_technology');
        $techDifference = $defenderEspionnageLevel - $attackerEspionnageLevel;
        $levelDifference = max(0, $techDifference);
        $extraProbesRequired = pow($levelDifference, 2);
        $remainingProbes = max(0, $mission->espionage_probe - $extraProbesRequired);

        // Fleets
        if ($this->canRevealData($remainingProbes, $attackerEspionnageLevel, $defenderEspionnageLevel, 2, 1)) {
            $report->ships = $targetPlanet->getShipUnits()->toArray();
        }

        // Defense
        if ($this->canRevealData($remainingProbes, $attackerEspionnageLevel, $defenderEspionnageLevel, 3, 2)) {
            $report->defense = $targetPlanet->getDefenseUnits()->toArray();
        }

        // Buildings
        if ($this->canRevealData($remainingProbes, $attackerEspionnageLevel, $defenderEspionnageLevel, 5, 3)) {
            $report->buildings = $targetPlanet->getBuildingArray();
        }

        // Research
        if ($this->canRevealData($remainingProbes, $attackerEspionnageLevel, $defenderEspionnageLevel, 7, 4)) {
            $report->research = $targetPlanet->getPlayer()->getResearchArray();
        }

        $report->save();

        return $report->id;
    }

    /**
     * Determine if specific data can be revealed in the report based on remaining probes and espionage levels.
     *
     * @param int $remainingProbes
     * @param int $attackerLevel
     * @param int $defenderLevel
     * @param int $probeThreshold
     * @param int $levelThreshold
     * @return bool
     */
    private function canRevealData(int $remainingProbes, int $attackerLevel, int $defenderLevel, int $probeThreshold, int $levelThreshold): bool
    {
        return $remainingProbes >= $probeThreshold || $attackerLevel - $levelThreshold >= $defenderLevel;
    }
}
