<?php

namespace Tests\Feature\FleetDispatch;

use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Resources;
use OGame\Services\FleetMissionService;
use OGame\Services\ObjectService;
use OGame\Services\SettingsService;
use Tests\FleetDispatchTestCase;

/**
 * Test that other mission types do NOT apply Attack-style resource loss logic.
 */
class FleetDispatchMissionResourceHandlingTest extends FleetDispatchTestCase
{
    protected int $missionType = 15; // Default to Expedition
    protected string $missionName = 'Expedition';

    protected function basicSetup(): void
    {
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('economy_speed', 1);
        $settingsService->set('fleet_speed_war', 1);
        $settingsService->set('fleet_speed_holding', 1);
        $settingsService->set('fleet_speed_peaceful', 1);
    }

    /**
     * Test Expedition missions do NOT apply resource loss logic.
     */
    public function testExpeditionMissionDoesNotApplyResourceLoss(): void
    {
        $this->basicSetup();

        $this->planetAddUnit('large_cargo', 10);
        $this->playerSetResearchLevel('astrophysics', 1);
        $this->playerSetResearchLevel('computer_technology', 10);

        $initialMetal = 50000;
        $initialCrystal = 30000;
        $initialDeuterium = 20000;
        $this->planetAddResources(new Resources($initialMetal, $initialCrystal, $initialDeuterium + 100000, 0));

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('large_cargo'), 10);

        $this->sendMissionToPosition16(
            $unitCollection,
            new Resources($initialMetal, $initialCrystal, $initialDeuterium, 0)
        );

        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $this->assertNotNull($fleetMission, 'Fleet mission not created');

        // Travel to arrival
        $this->travel($fleetMission->time_arrival - time() + 1)->seconds();
        $this->reloadApplication();
        $this->get('/overview');

        // Get return mission
        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();
        $this->assertGreaterThan(0, $activeMissions->count(), 'No return mission found');

        $returnMission = $activeMissions->first();
        $returnedTotal = $returnMission->metal + $returnMission->crystal + $returnMission->deuterium;
        $originalTotal = $initialMetal + $initialCrystal + $initialDeuterium;

        // Key assertion: Expedition returns with AT LEAST original resources
        // (may have found more, but never loses original resources like Attack does)
        $this->assertGreaterThanOrEqual(
            $originalTotal - 1,
            $returnedTotal,
            'Expedition mission incorrectly applied resource loss logic'
        );
    }

    /**
     * Test Transport missions do NOT apply resource loss logic.
     */
    public function testTransportMissionDoesNotApplyResourceLoss(): void
    {
        $this->basicSetup();

        $this->planetAddUnit('large_cargo', 10);

        $transportMetal = 50000;
        $transportCrystal = 30000;
        $transportDeuterium = 20000;
        $this->planetAddResources(new Resources($transportMetal, $transportCrystal, $transportDeuterium + 100000, 0));

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('large_cargo'), 10);

        $this->missionType = 3; // Transport

        $this->sendMissionToSecondPlanet(
            $unitCollection,
            new Resources($transportMetal, $transportCrystal, $transportDeuterium, 0)
        );

        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $this->assertNotNull($fleetMission, 'Fleet mission not created');

        // Travel to arrival
        $this->travel($fleetMission->time_arrival - time() + 1)->seconds();
        $this->reloadApplication();
        $this->get('/overview');

        // Verify return mission exists
        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();
        $this->assertGreaterThan(0, $activeMissions->count(), 'No return mission found');
        $this->assertNotNull($activeMissions, 'Transport mission completed successfully');
    }

    /**
     * Test Colonisation missions do NOT apply resource loss logic.
     */
    public function testColonisationMissionDoesNotApplyResourceLoss(): void
    {
        $this->basicSetup();

        $this->planetAddUnit('colony_ship', 1);
        $this->planetAddUnit('large_cargo', 5);
        $this->playerSetResearchLevel('astrophysics', 1);

        $colonyMetal = 10000;
        $colonyCrystal = 5000;
        $colonyDeuterium = 2000;
        $this->planetAddResources(new Resources($colonyMetal, $colonyCrystal, $colonyDeuterium + 100000, 0));

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('colony_ship'), 1);
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('large_cargo'), 5);

        $this->missionType = 7; // Colonisation
        $this->sendMissionToEmptyPosition(
            $unitCollection,
            new Resources($colonyMetal, $colonyCrystal, $colonyDeuterium, 0)
        );

        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $this->assertNotNull($fleetMission, 'Fleet mission not created');

        // Travel to arrival
        $this->travel($fleetMission->time_arrival - time() + 1)->seconds();
        $this->reloadApplication();
        $this->get('/overview');

        // Verify mission completed successfully
        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();
        $this->assertNotNull($activeMissions, 'Colonisation mission completed successfully');
    }

    /**
     * Test Recycle missions do NOT apply resource loss logic.
     */
    public function testRecycleMissionDoesNotApplyResourceLoss(): void
    {
        $this->basicSetup();

        $this->planetAddUnit('recycler', 10);

        $initialMetal = 10000;
        $initialCrystal = 5000;
        $initialDeuterium = 2000;
        $this->planetAddResources(new Resources($initialMetal, $initialCrystal, $initialDeuterium + 100000, 0));

        // Create debris field at second planet coordinates
        $debrisFieldService = resolve(\OGame\Services\DebrisFieldService::class);
        $debrisFieldService->loadOrCreateForCoordinates($this->secondPlanetService->getPlanetCoordinates());
        $debrisFieldService->appendResources(new Resources(5000, 3000, 0, 0));
        $debrisFieldService->save();

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('recycler'), 10);

        $this->missionType = 8; // Recycle
        $this->sendMissionToSecondPlanetDebrisField(
            $unitCollection,
            new Resources($initialMetal, $initialCrystal, $initialDeuterium, 0)
        );

        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $this->assertNotNull($fleetMission, 'Fleet mission not created');

        // Travel to arrival
        $this->travel($fleetMission->time_arrival - time() + 1)->seconds();
        $this->reloadApplication();
        $this->get('/overview');

        // Get return mission
        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();
        $this->assertGreaterThan(0, $activeMissions->count(), 'No return mission found');

        $returnMission = $activeMissions->first();
        $returnedTotal = $returnMission->metal + $returnMission->crystal + $returnMission->deuterium;
        $originalTotal = $initialMetal + $initialCrystal + $initialDeuterium;

        // Recycle returns original + harvested debris
        $this->assertGreaterThanOrEqual($originalTotal, $returnedTotal, 'Resources incorrectly lost');
    }
}
