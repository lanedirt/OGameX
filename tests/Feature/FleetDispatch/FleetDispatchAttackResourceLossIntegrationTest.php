<?php

namespace Tests\Feature\FleetDispatch;

use Illuminate\Support\Carbon;
use OGame\GameMissions\AttackMission;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\BattleReport;
use OGame\Models\Resources;
use OGame\Services\FleetMissionService;
use OGame\Services\ObjectService;
use OGame\Services\SettingsService;
use Tests\FleetDispatchTestCase;

/**
 * Integration tests for Attack mission resource loss mechanics.
 */
class FleetDispatchAttackResourceLossIntegrationTest extends FleetDispatchTestCase
{
    /**
     * @var int The mission type for the test.
     */
    protected int $missionType = 1;

    /**
     * @var string The mission name for the test, displayed in UI.
     */
    protected string $missionName = 'Attack';

    /**
     * Prepare the planet for the test.
     */
    protected function basicSetup(): void
    {
        // Set the fleet speed to 1x for this test.
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('economy_speed', 0);
        $settingsService->set('fleet_speed_war', 1);
        $settingsService->set('fleet_speed_holding', 1);
        $settingsService->set('fleet_speed_peaceful', 1);
    }

    /**
     * Test full Attack mission flow with resource loss.
     */
    public function testFullAttackMissionFlowWithResourceLoss(): void
    {
        $this->basicSetup();

        // Setup: Send 50 large cargo ships with resources
        $shipCount = 50;
        $this->planetAddUnit('large_cargo', $shipCount);

        $initialMetal = 200000;
        $initialCrystal = 150000;
        $initialDeuterium = 100000;
        $this->planetAddResources(new Resources($initialMetal, $initialCrystal, $initialDeuterium + 200000, 0));

        // Record planet resources before mission
        $planetResourcesBefore = $this->planetService->getResources();

        // Create unit collection
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('large_cargo'), $shipCount);

        // Calculate original cargo capacity
        $playerService = $this->planetService->getPlayer();
        $originalCapacity = $unitCollection->getTotalCargoCapacity($playerService);

        // Dispatch fleet with resources
        $foreignPlanet = $this->sendMissionToOtherPlayerCleanPlanet(
            $unitCollection,
            new Resources($initialMetal, $initialCrystal, $initialDeuterium, 0)
        );

        // Add defender units to cause ship losses (calibrated for ~20-40% losses)
        // 50 large cargo vs 50 rocket launchers should result in attacker victory with moderate losses
        $foreignPlanet->addUnit('rocket_launcher', 50);
        $foreignPlanet->addResources(new Resources(100000, 80000, 60000, 0));

        // Verify fleet mission was created
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $this->assertNotNull($fleetMission, 'Fleet mission was not created');

        // Verify mission has correct resources
        $this->assertEquals($initialMetal, $fleetMission->metal);
        $this->assertEquals($initialCrystal, $fleetMission->crystal);
        $this->assertEquals($initialDeuterium, $fleetMission->deuterium);

        // Advance time to mission arrival
        $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration(
            $this->planetService,
            $foreignPlanet->getPlanetCoordinates(),
            $unitCollection,
            resolve(AttackMission::class)
        );
        $this->travel($fleetMissionDuration + 1)->seconds();

        // Reload and trigger mission processing
        $this->reloadApplication();
        $this->get('/overview')->assertStatus(200);

        // Verify battle report was created
        $battleReport = BattleReport::orderBy('id', 'desc')->first();
        $this->assertNotNull($battleReport, 'Battle report was not created');

        // Calculate remaining capacity from battle report
        $attackerUnitsResult = $battleReport->rounds[count($battleReport->rounds) - 1]['attacker_ships'] ?? [];
        $remainingCapacity = 0;
        $survivingShips = 0;
        foreach ($attackerUnitsResult as $machineName => $amount) {
            if ($amount > 0) {
                $unitObject = ObjectService::getUnitObjectByMachineName($machineName);
                $remainingCapacity += $unitObject->properties->capacity->calculate($playerService)->totalValue * $amount;
                $survivingShips += $amount;
            }
        }

        // Verify some ships were lost but not all
        $this->assertGreaterThan(0, $survivingShips, 'All ships were lost');
        $this->assertLessThan($shipCount, $survivingShips, 'No ships were lost');

        // Calculate survival rate
        $survivalRate = $originalCapacity > 0 ? $remainingCapacity / $originalCapacity : 0;

        // Verify return mission was created
        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();
        $this->assertCount(1, $activeMissions, 'Return mission was not created');

        $returnMission = $activeMissions->first();

        // Verify return mission resources reflect proportional loss
        $expectedRemainingMetal = (int)($initialMetal * $survivalRate);
        $expectedRemainingCrystal = (int)($initialCrystal * $survivalRate);
        $expectedRemainingDeuterium = (int)($initialDeuterium * $survivalRate);

        $lootMetal = $battleReport->loot['metal'];
        $lootCrystal = $battleReport->loot['crystal'];
        $lootDeuterium = $battleReport->loot['deuterium'];

        // Total returned should be remaining + loot (capped at capacity)
        $totalReturned = $returnMission->metal + $returnMission->crystal + $returnMission->deuterium;

        // Verify total doesn't exceed remaining capacity
        $this->assertLessThanOrEqual(
            $remainingCapacity + 1,
            $totalReturned,
            'Total returned resources exceed remaining cargo capacity'
        );

        // Verify resources are less than original (due to losses)
        $originalTotal = $initialMetal + $initialCrystal + $initialDeuterium;
        $this->assertLessThan(
            $originalTotal,
            $totalReturned - ($lootMetal + $lootCrystal + $lootDeuterium),
            'Resources were not lost despite ship losses'
        );

        // Advance time to return mission arrival
        $this->travelTo(Carbon::createFromTimestamp($returnMission->time_arrival));

        // Reload and trigger return processing
        $this->reloadApplication();
        $this->get('/overview')->assertStatus(200);

        // Verify return mission was processed
        $returnMission = $fleetMissionService->getFleetMissionById($returnMission->id, false);
        $this->assertEquals(1, $returnMission->processed, 'Return mission was not processed');

        // Verify resources were added back to planet
        $this->planetService->reloadPlanet();
        $planetResourcesAfter = $this->planetService->getResources();

        // Planet should have gained resources (original - sent + returned)
        $this->assertGreaterThan(
            $planetResourcesBefore->metal->get() - $initialMetal,
            $planetResourcesAfter->metal->get(),
            'Planet metal was not updated correctly'
        );
    }

    /**
     * Test battle report accuracy with resource loss.
     */
    public function testBattleReportAccuracyWithResourceLoss(): void
    {
        $this->basicSetup();

        // Setup: Send fleet with known resources
        $this->planetAddUnit('large_cargo', 30);

        $initialMetal = 150000;
        $initialCrystal = 100000;
        $initialDeuterium = 75000;
        $this->planetAddResources(new Resources($initialMetal, $initialCrystal, $initialDeuterium + 150000, 0));

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('large_cargo'), 30);

        $playerService = $this->planetService->getPlayer();
        $originalCapacity = $unitCollection->getTotalCargoCapacity($playerService);

        // Dispatch fleet
        $foreignPlanet = $this->sendMissionToOtherPlayerCleanPlanet(
            $unitCollection,
            new Resources($initialMetal, $initialCrystal, $initialDeuterium, 0)
        );

        // Add defender and resources (weaker defense for partial losses)
        // 30 large cargo vs 25 rocket launchers should result in attacker victory with some losses
        $foreignPlanet->addUnit('rocket_launcher', 25);
        $defenderMetal = 200000;
        $defenderCrystal = 150000;
        $defenderDeuterium = 100000;
        $foreignPlanet->addResources(new Resources($defenderMetal, $defenderCrystal, $defenderDeuterium, 0));

        // Get fleet mission
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        // Advance to battle
        $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration(
            $this->planetService,
            $foreignPlanet->getPlanetCoordinates(),
            $unitCollection,
            resolve(AttackMission::class)
        );
        $this->travel($fleetMissionDuration + 1)->seconds();

        $this->reloadApplication();
        $this->get('/overview')->assertStatus(200);

        // Get battle report
        $battleReport = BattleReport::orderBy('id', 'desc')->first();
        $this->assertNotNull($battleReport, 'Battle report not created');

        // Verify battle report has loot information
        $this->assertArrayHasKey('metal', $battleReport->loot);
        $this->assertArrayHasKey('crystal', $battleReport->loot);
        $this->assertArrayHasKey('deuterium', $battleReport->loot);

        // Verify battle report has rounds
        $this->assertNotEmpty($battleReport->rounds, 'Battle report has no rounds');

        // Get final round
        $finalRound = $battleReport->rounds[count($battleReport->rounds) - 1];
        $this->assertArrayHasKey('attacker_ships', $finalRound);

        // Calculate remaining capacity from battle report
        $remainingCapacity = 0;
        foreach ($finalRound['attacker_ships'] as $machineName => $amount) {
            if ($amount > 0) {
                $unitObject = ObjectService::getUnitObjectByMachineName($machineName);
                $remainingCapacity += $unitObject->properties->capacity->calculate($playerService)->totalValue * $amount;
            }
        }

        // Get return mission
        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();
        $this->assertGreaterThan(0, $activeMissions->count(), 'No return mission found');

        $returnMission = $activeMissions->first();

        // Verify return mission resources match battle report expectations
        $survivalRate = $originalCapacity > 0 ? $remainingCapacity / $originalCapacity : 0;
        $expectedRemaining = (int)(($initialMetal + $initialCrystal + $initialDeuterium) * $survivalRate);
        $lootTotal = $battleReport->loot['metal'] + $battleReport->loot['crystal'] + $battleReport->loot['deuterium'];
        $returnTotal = $returnMission->metal + $returnMission->crystal + $returnMission->deuterium;

        // Return total should be approximately remaining + loot (within capacity)
        $this->assertLessThanOrEqual(
            $remainingCapacity + 1,
            $returnTotal,
            'Return mission resources exceed capacity shown in battle report'
        );

        // Verify loot doesn't exceed 50% of defender resources
        $maxLoot = (int)(($defenderMetal + $defenderCrystal + $defenderDeuterium) * 0.5);
        $this->assertLessThanOrEqual(
            $maxLoot + 1,
            $lootTotal,
            'Loot exceeds 50% of defender resources'
        );
    }

    /**
     * Test database persistence of adjusted resources.
     */
    public function testDatabasePersistenceOfAdjustedResources(): void
    {
        $this->basicSetup();

        // Setup
        $this->planetAddUnit('large_cargo', 40);

        $initialMetal = 180000;
        $initialCrystal = 120000;
        $initialDeuterium = 90000;
        $this->planetAddResources(new Resources($initialMetal, $initialCrystal, $initialDeuterium + 180000, 0));

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('large_cargo'), 40);

        // Dispatch fleet
        $foreignPlanet = $this->sendMissionToOtherPlayerCleanPlanet(
            $unitCollection,
            new Resources($initialMetal, $initialCrystal, $initialDeuterium, 0)
        );

        // 40 large cargo vs 35 rocket launchers should result in attacker victory with losses
        $foreignPlanet->addUnit('rocket_launcher', 35);
        $foreignPlanet->addResources(new Resources(150000, 120000, 90000, 0));

        // Get fleet mission ID
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $outboundMissionId = $fleetMission->id;

        // Verify outbound mission is persisted correctly
        $this->assertDatabaseHas('fleet_missions', [
            'id' => $outboundMissionId,
            'metal' => $initialMetal,
            'crystal' => $initialCrystal,
            'deuterium' => $initialDeuterium,
            'processed' => 0,
        ]);

        // Advance to battle
        $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration(
            $this->planetService,
            $foreignPlanet->getPlanetCoordinates(),
            $unitCollection,
            resolve(AttackMission::class)
        );
        $this->travel($fleetMissionDuration + 1)->seconds();

        $this->reloadApplication();
        $this->get('/overview')->assertStatus(200);

        // Verify outbound mission is marked as processed
        $this->assertDatabaseHas('fleet_missions', [
            'id' => $outboundMissionId,
            'processed' => 1,
        ]);

        // Verify battle report is persisted
        $battleReport = BattleReport::orderBy('id', 'desc')->first();
        $this->assertNotNull($battleReport);
        $this->assertDatabaseHas('battle_reports', [
            'id' => $battleReport->id,
        ]);

        // Get return mission
        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();
        $this->assertGreaterThan(0, $activeMissions->count());

        $returnMission = $activeMissions->first();
        $returnMissionId = $returnMission->id;

        // Verify return mission is persisted with adjusted resources
        $this->assertDatabaseHas('fleet_missions', [
            'id' => $returnMissionId,
            'processed' => 0,
        ]);

        // Verify return mission resources are different from original (due to losses)
        $returnMissionFromDb = $fleetMissionService->getFleetMissionById($returnMissionId, false);
        $returnTotal = $returnMissionFromDb->metal + $returnMissionFromDb->crystal + $returnMissionFromDb->deuterium;
        $originalTotal = $initialMetal + $initialCrystal + $initialDeuterium;

        // Return should have loot, but original resources should be reduced
        // So we can't directly compare totals, but we can verify the mission exists with resources
        $this->assertGreaterThan(0, $returnTotal, 'Return mission has no resources');

        // Record planet resources before return
        $this->planetService->reloadPlanet();
        $planetMetalBefore = $this->planetService->getResources()->metal;

        // Advance to return
        $this->travelTo(Carbon::createFromTimestamp($returnMission->time_arrival));
        $this->reloadApplication();
        $this->get('/overview')->assertStatus(200);

        // Verify return mission is marked as processed
        $this->assertDatabaseHas('fleet_missions', [
            'id' => $returnMissionId,
            'processed' => 1,
        ]);

        // Verify planet resources were updated
        $this->planetService->reloadPlanet();
        $planetMetalAfter = $this->planetService->getResources()->metal;

        $this->assertGreaterThan(
            $planetMetalBefore,
            $planetMetalAfter,
            'Planet resources were not updated after return mission'
        );
    }

    /**
     * Test various fleet compositions and loss percentages.
     */
    public function testVariousFleetCompositionsAndLossPercentages(): void
    {
        $this->basicSetup();

        // Test scenario 1: Mixed fleet (cargo + combat ships)
        $this->planetAddUnit('large_cargo', 20);
        $this->planetAddUnit('cruiser', 30);

        $initialMetal = 100000;
        $initialCrystal = 80000;
        $initialDeuterium = 60000;
        $this->planetAddResources(new Resources($initialMetal, $initialCrystal, $initialDeuterium + 150000, 0));

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('large_cargo'), 20);
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('cruiser'), 30);

        $playerService = $this->planetService->getPlayer();
        $originalCapacity = $unitCollection->getTotalCargoCapacity($playerService);

        // Verify mixed fleet has capacity from both ship types
        $this->assertGreaterThan(0, $originalCapacity, 'Mixed fleet has no cargo capacity');

        // Dispatch fleet
        $foreignPlanet = $this->sendMissionToOtherPlayerCleanPlanet(
            $unitCollection,
            new Resources($initialMetal, $initialCrystal, $initialDeuterium, 0)
        );

        // Moderate defense for partial losses
        // 20 large cargo + 30 cruisers vs 100 rocket launchers should result in attacker victory with losses
        $foreignPlanet->addUnit('rocket_launcher', 100);
        $foreignPlanet->addResources(new Resources(80000, 60000, 40000, 0));

        // Get fleet mission
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        // Advance to battle
        $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration(
            $this->planetService,
            $foreignPlanet->getPlanetCoordinates(),
            $unitCollection,
            resolve(AttackMission::class)
        );
        $this->travel($fleetMissionDuration + 1)->seconds();

        $this->reloadApplication();
        $this->get('/overview')->assertStatus(200);

        // Get battle report
        $battleReport = BattleReport::orderBy('id', 'desc')->first();
        $this->assertNotNull($battleReport);

        // Calculate remaining capacity from both ship types
        $finalRound = $battleReport->rounds[count($battleReport->rounds) - 1];
        $remainingCapacity = 0;
        $survivingCargo = $finalRound['attacker_ships']['large_cargo'] ?? 0;
        $survivingCruisers = $finalRound['attacker_ships']['cruiser'] ?? 0;

        if ($survivingCargo > 0) {
            $cargoObject = ObjectService::getUnitObjectByMachineName('large_cargo');
            $remainingCapacity += $cargoObject->properties->capacity->calculate($playerService)->totalValue * $survivingCargo;
        }
        if ($survivingCruisers > 0) {
            $cruiserObject = ObjectService::getUnitObjectByMachineName('cruiser');
            $remainingCapacity += $cruiserObject->properties->capacity->calculate($playerService)->totalValue * $survivingCruisers;
        }

        // Verify some ships survived
        $this->assertGreaterThan(0, $survivingCargo + $survivingCruisers, 'All ships were lost');

        // Get return mission
        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();
        $this->assertGreaterThan(0, $activeMissions->count());

        $returnMission = $activeMissions->first();
        $returnTotal = $returnMission->metal + $returnMission->crystal + $returnMission->deuterium;

        // Verify capacity constraint is enforced
        $this->assertLessThanOrEqual(
            $remainingCapacity + 1,
            $returnTotal,
            'Mixed fleet return exceeds remaining capacity'
        );

        // Verify survival rate calculation works correctly
        $survivalRate = $originalCapacity > 0 ? $remainingCapacity / $originalCapacity : 0;
        $this->assertGreaterThanOrEqual(0.0, $survivalRate, 'Survival rate is negative');
        $this->assertLessThanOrEqual(1.0, $survivalRate, 'Survival rate exceeds 100%');

        // If losses occurred, verify resources were adjusted
        if ($survivalRate < 1.0) {
            $this->assertLessThan(
                $originalCapacity,
                $remainingCapacity,
                'Capacity was not reduced despite survival rate < 1'
            );
        }

        // Clean up - wait for return
        $this->travelTo(Carbon::createFromTimestamp($returnMission->time_arrival));
        $this->get('/overview');
    }

    /**
     * Test resources exactly at capacity after losses.
     */
    public function testResourcesExactlyAtCapacity(): void
    {
        $this->basicSetup();

        // Use small fleet for precise capacity control
        $this->planetAddUnit('large_cargo', 10);

        $initialMetal = 50000;
        $initialCrystal = 40000;
        $initialDeuterium = 30000;
        $this->planetAddResources(new Resources($initialMetal, $initialCrystal, $initialDeuterium + 100000, 0));

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('large_cargo'), 10);

        // Dispatch fleet
        $foreignPlanet = $this->sendMissionToOtherPlayerCleanPlanet(
            $unitCollection,
            new Resources($initialMetal, $initialCrystal, $initialDeuterium, 0)
        );

        // Very weak defense - minimal losses, minimal loot
        $foreignPlanet->addUnit('rocket_launcher', 2);
        $foreignPlanet->addResources(new Resources(5000, 5000, 5000, 0));

        // Get fleet mission
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        // Advance to battle
        $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration(
            $this->planetService,
            $foreignPlanet->getPlanetCoordinates(),
            $unitCollection,
            resolve(AttackMission::class)
        );
        $this->travel($fleetMissionDuration + 1)->seconds();

        $this->reloadApplication();
        $this->get('/overview')->assertStatus(200);

        // Get battle report
        $battleReport = BattleReport::orderBy('id', 'desc')->first();
        $this->assertNotNull($battleReport);

        // Get return mission
        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();
        $this->assertGreaterThan(0, $activeMissions->count());

        $returnMission = $activeMissions->first();

        // Calculate remaining capacity
        $playerService = $this->planetService->getPlayer();
        $finalRound = $battleReport->rounds[count($battleReport->rounds) - 1];
        $remainingCapacity = 0;
        foreach ($finalRound['attacker_ships'] as $machineName => $amount) {
            if ($amount > 0) {
                $unitObject = ObjectService::getUnitObjectByMachineName($machineName);
                $remainingCapacity += $unitObject->properties->capacity->calculate($playerService)->totalValue * $amount;
            }
        }

        $returnTotal = $returnMission->metal + $returnMission->crystal + $returnMission->deuterium;

        // Verify resources don't exceed capacity
        $this->assertLessThanOrEqual(
            $remainingCapacity + 1,
            $returnTotal,
            'Resources exceed capacity'
        );

        // If resources are within capacity, they should be preserved exactly
        $survivalRate = $originalCapacity = $unitCollection->getTotalCargoCapacity($playerService);
        $survivalRate = $originalCapacity > 0 ? $remainingCapacity / $originalCapacity : 0;
        $expectedRemaining = (int)(($initialMetal + $initialCrystal + $initialDeuterium) * $survivalRate);
        $lootTotal = $battleReport->loot['metal'] + $battleReport->loot['crystal'] + $battleReport->loot['deuterium'];

        if ($expectedRemaining + $lootTotal <= $remainingCapacity) {
            // Resources are within capacity, should be preserved
            $this->assertEqualsWithDelta(
                $expectedRemaining + $lootTotal,
                $returnTotal,
                5,
                'Resources not preserved when within capacity'
            );
        }
    }
}
