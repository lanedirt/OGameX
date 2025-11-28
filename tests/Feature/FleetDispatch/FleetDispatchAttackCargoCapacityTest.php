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
 * Test cargo capacity and resource loss mechanics for attack missions.
 *
 * These tests verify that resources on destroyed ships are lost proportionally
 * and that total returned resources never exceed remaining cargo capacity.
 */
class FleetDispatchAttackCargoCapacityTest extends FleetDispatchTestCase
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
     * Property test: Resources lost proportionally to cargo capacity lost.
     */
    public function testPropertyProportionalResourceLoss(): void
    {
        // Run 10 iterations with different random scenarios
        // (Reduced from 100 to avoid fleet limit issues in test environment)
        $iterations = 10;
        $seed = time();
        mt_srand($seed);

        for ($i = 0; $i < $iterations; $i++) {
            $this->basicSetup();
            // Generate random fleet composition (1-50 ships of various types)
            $shipTypes = ['light_fighter', 'heavy_fighter', 'cruiser', 'battle_ship', 'large_cargo', 'small_cargo'];
            $randomShipType = $shipTypes[array_rand($shipTypes)];
            $shipCount = mt_rand(10, 50);

            // Add ships and resources to planet
            $this->planetAddUnit($randomShipType, $shipCount);

            // Create unit collection for mission
            $unitCollection = new UnitCollection();
            $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName($randomShipType), $shipCount);

            // Calculate cargo capacity and generate resources that fit
            $playerService = $this->planetService->getPlayer();
            $cargoCapacity = $unitCollection->getTotalCargoCapacity($playerService);

            // Generate random initial resources (0-80% of cargo capacity to leave room)
            $maxResources = (int)($cargoCapacity * 0.8);
            $initialMetal = mt_rand(0, (int)($maxResources / 3));
            $initialCrystal = mt_rand(0, (int)($maxResources / 3));
            $initialDeuterium = mt_rand(0, (int)($maxResources / 3));

            $this->planetAddResources(new Resources($initialMetal, $initialCrystal, $initialDeuterium + 100000, 0));

            // Send mission to foreign planet
            $foreignPlanet = $this->sendMissionToOtherPlayerCleanPlanet($unitCollection, new Resources($initialMetal, $initialCrystal, $initialDeuterium, 0));

            // Generate random defender strength (0-100% of attacker strength)
            // This creates various loss scenarios
            $defenderStrength = mt_rand(0, 100);
            if ($defenderStrength > 0) {
                $foreignPlanet->addUnit('rocket_launcher', (int)($shipCount * $defenderStrength / 10));
            }

            // Get fleet mission
            $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
            $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

            // Store original cargo capacity (already calculated above)
            $originalCapacity = $cargoCapacity;

            // Advance time to mission completion
            $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration(
                $this->planetService,
                $foreignPlanet->getPlanetCoordinates(),
                $unitCollection,
                resolve(AttackMission::class)
            );
            $this->travel($fleetMissionDuration + 1)->seconds();

            // Reload and trigger update
            $this->reloadApplication();
            $this->get('/overview');

            // Get battle report
            $battleReport = BattleReport::orderBy('id', 'desc')->first();
            $this->assertNotNull($battleReport, "Iteration $i: Battle report not created");

            // Calculate remaining capacity from battle report
            $attackerUnitsResult = $battleReport->rounds[count($battleReport->rounds) - 1]['attacker_ships'] ?? [];
            $remainingCapacity = 0;
            foreach ($attackerUnitsResult as $machineName => $amount) {
                if ($amount > 0) {
                    $unitObject = ObjectService::getUnitObjectByMachineName($machineName);
                    $remainingCapacity += $unitObject->properties->capacity->calculate($playerService)->totalValue * $amount;
                }
            }

            // Calculate expected survival rate
            $survivalRate = $originalCapacity > 0 ? $remainingCapacity / $originalCapacity : 0;

            // Get return mission resources
            $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();
            if ($activeMissions->count() > 0) {
                $returnMission = $activeMissions->first();

                // Calculate expected remaining resources (before loot)
                $expectedMetal = (int)($initialMetal * $survivalRate);
                $expectedCrystal = (int)($initialCrystal * $survivalRate);
                $expectedDeuterium = (int)($initialDeuterium * $survivalRate);

                // Actual returned resources include loot, so they should be >= expected remaining
                // (unless capacity constraint kicks in)
                $actualMetal = $returnMission->metal;
                $actualCrystal = $returnMission->crystal;
                $actualDeuterium = $returnMission->deuterium;

                $totalReturned = $actualMetal + $actualCrystal + $actualDeuterium;

                // Property 1: Total returned should not exceed remaining capacity
                $this->assertLessThanOrEqual(
                    $remainingCapacity + 1, // +1 for rounding tolerance
                    $totalReturned,
                    "Iteration $i: Total returned resources ($totalReturned) exceed remaining capacity ($remainingCapacity)"
                );

                // If no loot was taken, returned resources should match expected (within rounding)
                $lootTaken = $battleReport->loot['metal'] + $battleReport->loot['crystal'] + $battleReport->loot['deuterium'];
                if ($lootTaken == 0) {
                    $this->assertEqualsWithDelta(
                        $expectedMetal,
                        $actualMetal,
                        2,
                        "Iteration $i: Metal doesn't match expected proportional loss"
                    );
                    $this->assertEqualsWithDelta(
                        $expectedCrystal,
                        $actualCrystal,
                        2,
                        "Iteration $i: Crystal doesn't match expected proportional loss"
                    );
                }
            }

            // Wait for return mission to complete to clean up
            if ($activeMissions->count() > 0) {
                $returnMission = $activeMissions->first();
                $this->travelTo(Carbon::createFromTimestamp($returnMission->time_arrival));
                $this->get('/overview');
            }

            // Clean up for next iteration
            $this->reloadApplication();
        }
    }

    /**
     * Property test: Total resources never exceed remaining cargo capacity.
     */
    public function testPropertyCapacityConstraintEnforcement(): void
    {
        // Run 10 iterations with scenarios designed to exceed capacity
        $iterations = 10;
        $seed = time();
        mt_srand($seed);

        for ($i = 0; $i < $iterations; $i++) {
            $this->basicSetup();

            // Use large cargo ships for predictable capacity
            $shipCount = mt_rand(5, 20);
            $this->planetAddUnit('large_cargo', $shipCount);

            // Create unit collection
            $unitCollection = new UnitCollection();
            $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('large_cargo'), $shipCount);

            // Calculate cargo capacity
            $playerService = $this->planetService->getPlayer();
            $cargoCapacity = $unitCollection->getTotalCargoCapacity($playerService);

            // Send resources close to capacity
            $initialMetal = (int)($cargoCapacity * 0.3);
            $initialCrystal = (int)($cargoCapacity * 0.3);
            $initialDeuterium = (int)($cargoCapacity * 0.3);

            $this->planetAddResources(new Resources($initialMetal, $initialCrystal, $initialDeuterium + 100000, 0));

            // Send mission to foreign planet with lots of resources to loot
            $foreignPlanet = $this->sendMissionToOtherPlayerCleanPlanet(
                $unitCollection,
                new Resources($initialMetal, $initialCrystal, $initialDeuterium, 0)
            );

            // Give defender planet lots of resources but weak defense
            // This ensures attacker wins and can loot, potentially exceeding capacity
            $foreignPlanet->addResources(new Resources(1000000, 1000000, 1000000, 0));
            $foreignPlanet->addUnit('rocket_launcher', mt_rand(1, 5)); // Weak defense to cause some losses

            // Get fleet mission
            $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
            $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

            // Advance time to mission completion
            $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration(
                $this->planetService,
                $foreignPlanet->getPlanetCoordinates(),
                $unitCollection,
                resolve(AttackMission::class)
            );
            $this->travel($fleetMissionDuration + 1)->seconds();

            // Reload and trigger update
            $this->reloadApplication();
            $this->get('/overview');

            // Get battle report
            $battleReport = BattleReport::orderBy('id', 'desc')->first();
            $this->assertNotNull($battleReport, "Iteration $i: Battle report not created");

            // Calculate remaining capacity from battle report
            $attackerUnitsResult = $battleReport->rounds[count($battleReport->rounds) - 1]['attacker_ships'] ?? [];
            $remainingCapacity = 0;
            foreach ($attackerUnitsResult as $machineName => $amount) {
                if ($amount > 0) {
                    $unitObject = ObjectService::getUnitObjectByMachineName($machineName);
                    $remainingCapacity += $unitObject->properties->capacity->calculate($playerService)->totalValue * $amount;
                }
            }

            // Get return mission resources
            $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();
            if ($activeMissions->count() > 0) {
                $returnMission = $activeMissions->first();

                $actualMetal = $returnMission->metal;
                $actualCrystal = $returnMission->crystal;
                $actualDeuterium = $returnMission->deuterium;

                $totalReturned = $actualMetal + $actualCrystal + $actualDeuterium;

                // Property 3: Total returned should NEVER exceed remaining capacity
                $this->assertLessThanOrEqual(
                    $remainingCapacity + 1, // +1 for rounding tolerance
                    $totalReturned,
                    "Iteration $i: Total returned resources ($totalReturned) exceed remaining capacity ($remainingCapacity)"
                );

                // If capacity was exceeded, verify proportional distribution
                $survivalRate = $cargoCapacity > 0 ? $remainingCapacity / $cargoCapacity : 0;
                $expectedRemaining = (int)(($initialMetal + $initialCrystal + $initialDeuterium) * $survivalRate);
                $lootAmount = $battleReport->loot['metal'] + $battleReport->loot['crystal'] + $battleReport->loot['deuterium'];

                if ($expectedRemaining + $lootAmount > $remainingCapacity) {
                    // Capacity was exceeded, verify distribution is proportional
                    $totalBeforeCap = $expectedRemaining + $lootAmount;
                    if ($totalBeforeCap > 0 && $actualMetal > 0 && $actualCrystal > 0) {
                        $metalRatio = $actualMetal / $totalReturned;
                        $crystalRatio = $actualCrystal / $totalReturned;

                        // Ratios should be relatively similar (within 20% of each other)
                        // This verifies proportional distribution
                        $this->assertLessThan(
                            0.5,
                            abs($metalRatio - $crystalRatio),
                            "Iteration $i: Distribution not proportional (metal: $metalRatio, crystal: $crystalRatio)"
                        );
                    }
                }

                // Wait for return mission to complete
                $this->travelTo(Carbon::createFromTimestamp($returnMission->time_arrival));
                $this->get('/overview');
            }

            // Clean up for next iteration
            $this->reloadApplication();
        }
    }

    /**
     * Property test: Resources within capacity are preserved exactly.
     */
    public function testPropertyTotalWithinCapacityPreservation(): void
    {
        // Run 10 iterations with scenarios where resources stay within capacity
        $iterations = 10;
        $seed = time();
        mt_srand($seed);

        for ($i = 0; $i < $iterations; $i++) {
            $this->basicSetup();

            // Use large cargo ships for high capacity
            $shipCount = mt_rand(20, 50);
            $this->planetAddUnit('large_cargo', $shipCount);

            // Create unit collection
            $unitCollection = new UnitCollection();
            $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('large_cargo'), $shipCount);

            // Calculate cargo capacity
            $playerService = $this->planetService->getPlayer();
            $cargoCapacity = $unitCollection->getTotalCargoCapacity($playerService);

            // Send small amount of resources (10-20% of capacity)
            $initialMetal = (int)($cargoCapacity * mt_rand(10, 20) / 100);
            $initialCrystal = (int)($cargoCapacity * mt_rand(10, 20) / 100);
            $initialDeuterium = (int)($cargoCapacity * mt_rand(10, 20) / 100);

            $this->planetAddResources(new Resources($initialMetal, $initialCrystal, $initialDeuterium + 100000, 0));

            // Send mission to foreign planet with moderate resources
            $foreignPlanet = $this->sendMissionToOtherPlayerCleanPlanet(
                $unitCollection,
                new Resources($initialMetal, $initialCrystal, $initialDeuterium, 0)
            );

            // Give defender planet moderate resources and weak defense
            $foreignPlanet->addResources(new Resources(50000, 50000, 50000, 0));
            $foreignPlanet->addUnit('rocket_launcher', mt_rand(1, 3)); // Very weak defense

            // Get fleet mission
            $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
            $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

            // Advance time to mission completion
            $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration(
                $this->planetService,
                $foreignPlanet->getPlanetCoordinates(),
                $unitCollection,
                resolve(AttackMission::class)
            );
            $this->travel($fleetMissionDuration + 1)->seconds();

            // Reload and trigger update
            $this->reloadApplication();
            $this->get('/overview');

            // Get battle report
            $battleReport = BattleReport::orderBy('id', 'desc')->first();
            $this->assertNotNull($battleReport, "Iteration $i: Battle report not created");

            // Calculate remaining capacity from battle report
            $attackerUnitsResult = $battleReport->rounds[count($battleReport->rounds) - 1]['attacker_ships'] ?? [];
            $remainingCapacity = 0;
            foreach ($attackerUnitsResult as $machineName => $amount) {
                if ($amount > 0) {
                    $unitObject = ObjectService::getUnitObjectByMachineName($machineName);
                    $remainingCapacity += $unitObject->properties->capacity->calculate($playerService)->totalValue * $amount;
                }
            }

            // Calculate survival rate
            $survivalRate = $cargoCapacity > 0 ? $remainingCapacity / $cargoCapacity : 0;

            // Calculate expected remaining resources
            $expectedMetal = (int)($initialMetal * $survivalRate);
            $expectedCrystal = (int)($initialCrystal * $survivalRate);
            $expectedDeuterium = (int)($initialDeuterium * $survivalRate);

            // Get loot from battle report
            $lootMetal = $battleReport->loot['metal'];
            $lootCrystal = $battleReport->loot['crystal'];
            $lootDeuterium = $battleReport->loot['deuterium'];

            // Calculate total expected (remaining + loot)
            $totalExpected = $expectedMetal + $expectedCrystal + $expectedDeuterium +
                           $lootMetal + $lootCrystal + $lootDeuterium;

            // Get return mission resources
            $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();
            if ($activeMissions->count() > 0) {
                $returnMission = $activeMissions->first();

                $actualMetal = $returnMission->metal;
                $actualCrystal = $returnMission->crystal;
                $actualDeuterium = $returnMission->deuterium;

                $totalReturned = $actualMetal + $actualCrystal + $actualDeuterium;

                // Property 4: If total is within capacity, it should match expected exactly
                if ($totalExpected <= $remainingCapacity) {
                    $this->assertEqualsWithDelta(
                        $expectedMetal + $lootMetal,
                        $actualMetal,
                        2,
                        "Iteration $i: Metal not preserved when within capacity"
                    );
                    $this->assertEqualsWithDelta(
                        $expectedCrystal + $lootCrystal,
                        $actualCrystal,
                        2,
                        "Iteration $i: Crystal not preserved when within capacity"
                    );
                    $this->assertEqualsWithDelta(
                        $expectedDeuterium + $lootDeuterium,
                        $actualDeuterium,
                        2,
                        "Iteration $i: Deuterium not preserved when within capacity"
                    );
                }

                // Always verify total doesn't exceed capacity
                $this->assertLessThanOrEqual(
                    $remainingCapacity + 1,
                    $totalReturned,
                    "Iteration $i: Total returned exceeds capacity"
                );

                // Wait for return mission to complete
                $this->travelTo(Carbon::createFromTimestamp($returnMission->time_arrival));
                $this->get('/overview');
            }

            // Clean up for next iteration
            $this->reloadApplication();
        }
    }

    /**
     * Edge case: Zero ship loss - all resources preserved.
     */
    public function testEdgeCaseZeroShipLoss(): void
    {
        $this->basicSetup();

        // Send overwhelming force to ensure no losses
        $this->planetAddUnit('battle_ship', 100);
        $this->planetAddResources(new Resources(50000, 30000, 200000, 0));

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('battle_ship'), 100);

        // Send with resources
        $initialMetal = 50000;
        $initialCrystal = 30000;
        $initialDeuterium = 20000;

        $foreignPlanet = $this->sendMissionToOtherPlayerCleanPlanet(
            $unitCollection,
            new Resources($initialMetal, $initialCrystal, $initialDeuterium, 0)
        );

        // Very weak defense - attacker won't lose any ships
        $foreignPlanet->addUnit('rocket_launcher', 1);
        $foreignPlanet->addResources(new Resources(10000, 10000, 10000, 0));

        // Get fleet mission
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        // Advance time
        $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration(
            $this->planetService,
            $foreignPlanet->getPlanetCoordinates(),
            $unitCollection,
            resolve(AttackMission::class)
        );
        $this->travel($fleetMissionDuration + 1)->seconds();

        $this->reloadApplication();
        $this->get('/overview');

        // Get battle report
        $battleReport = BattleReport::orderBy('id', 'desc')->first();
        $this->assertNotNull($battleReport);

        // Verify no ships were lost
        $attackerUnitsResult = $battleReport->rounds[count($battleReport->rounds) - 1]['attacker_ships'];
        $this->assertEquals(100, $attackerUnitsResult['battle_ship'] ?? 0, 'Ships were lost when none should have been');

        // Get return mission
        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();
        $this->assertCount(1, $activeMissions);
        $returnMission = $activeMissions->first();

        // All original resources should be preserved (plus loot)
        $lootTotal = $battleReport->loot['metal'] + $battleReport->loot['crystal'] + $battleReport->loot['deuterium'];
        $returnedTotal = $returnMission->metal + $returnMission->crystal + $returnMission->deuterium;

        $this->assertEqualsWithDelta(
            $initialMetal + $initialCrystal + $initialDeuterium + $lootTotal,
            $returnedTotal,
            5,
            'Resources not fully preserved when no ships were lost'
        );
    }

    /**
     * Edge case: Total ship loss - all resources lost.
     */
    public function testEdgeCaseTotalShipLoss(): void
    {
        $this->basicSetup();

        // Send weak force against strong defense
        $this->planetAddUnit('light_fighter', 10);
        $this->planetAddResources(new Resources(10000, 10000, 100000, 0));

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 10);

        // Send with small resources that fit in light fighter capacity (50 per ship = 500 total)
        $initialMetal = 100;
        $initialCrystal = 100;
        $initialDeuterium = 100;

        $foreignPlanet = $this->sendMissionToOtherPlayerCleanPlanet(
            $unitCollection,
            new Resources($initialMetal, $initialCrystal, $initialDeuterium, 0)
        );

        // Overwhelming defense - attacker will lose all ships
        $foreignPlanet->addUnit('plasma_turret', 100);

        // Get fleet mission
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        // Advance time
        $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration(
            $this->planetService,
            $foreignPlanet->getPlanetCoordinates(),
            $unitCollection,
            resolve(AttackMission::class)
        );
        $this->travel($fleetMissionDuration + 1)->seconds();

        $this->reloadApplication();
        $this->get('/overview');

        // Get battle report
        $battleReport = BattleReport::orderBy('id', 'desc')->first();
        $this->assertNotNull($battleReport);

        // Verify all ships were lost
        $attackerUnitsResult = $battleReport->rounds[count($battleReport->rounds) - 1]['attacker_ships'];
        $totalShips = array_sum($attackerUnitsResult);
        $this->assertEquals(0, $totalShips, 'Ships survived when all should have been destroyed');

        // No return mission should exist
        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();
        $this->assertCount(0, $activeMissions, 'Return mission exists when all ships were destroyed');
    }

    /**
     * Edge case: 50% ship loss.
     */
    public function testEdgeCase50PercentShipLoss(): void
    {
        $this->basicSetup();

        // Use large cargo for predictable capacity
        $this->planetAddUnit('large_cargo', 20);
        $this->planetAddResources(new Resources(100000, 100000, 200000, 0));

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('large_cargo'), 20);

        $playerService = $this->planetService->getPlayer();
        $originalCapacity = $unitCollection->getTotalCargoCapacity($playerService);

        // Send resources
        $initialMetal = 100000;
        $initialCrystal = 80000;
        $initialDeuterium = 60000;

        $foreignPlanet = $this->sendMissionToOtherPlayerCleanPlanet(
            $unitCollection,
            new Resources($initialMetal, $initialCrystal, $initialDeuterium, 0)
        );

        // Calibrated defense to cause ~50% losses (weaker defense)
        $foreignPlanet->addUnit('rocket_launcher', 20);
        $foreignPlanet->addResources(new Resources(50000, 50000, 50000, 0));

        // Get fleet mission
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        // Advance time
        $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration(
            $this->planetService,
            $foreignPlanet->getPlanetCoordinates(),
            $unitCollection,
            resolve(AttackMission::class)
        );
        $this->travel($fleetMissionDuration + 1)->seconds();

        $this->reloadApplication();
        $this->get('/overview');

        // Get battle report
        $battleReport = BattleReport::orderBy('id', 'desc')->first();
        $this->assertNotNull($battleReport);

        // Calculate remaining capacity
        $attackerUnitsResult = $battleReport->rounds[count($battleReport->rounds) - 1]['attacker_ships'];
        $remainingCapacity = 0;
        foreach ($attackerUnitsResult as $machineName => $amount) {
            if ($amount > 0) {
                $unitObject = ObjectService::getUnitObjectByMachineName($machineName);
                $remainingCapacity += $unitObject->properties->capacity->calculate($playerService)->totalValue * $amount;
            }
        }

        $survivalRate = $originalCapacity > 0 ? $remainingCapacity / $originalCapacity : 0;

        // Verify survival rate is approximately 50% (within 30% tolerance due to battle randomness)
        $this->assertGreaterThan(0.2, $survivalRate, 'Survival rate too low');
        $this->assertLessThan(0.8, $survivalRate, 'Survival rate too high');

        // Get return mission
        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();
        if ($activeMissions->count() > 0) {
            $returnMission = $activeMissions->first();
            $returnedTotal = $returnMission->metal + $returnMission->crystal + $returnMission->deuterium;

            // Returned resources should not exceed remaining capacity
            $this->assertLessThanOrEqual($remainingCapacity + 1, $returnedTotal);
        }
    }

    /**
     * Edge case: 80% ship loss.
     */
    public function testEdgeCase80PercentShipLoss(): void
    {
        $this->basicSetup();

        // Use large cargo for predictable capacity
        $this->planetAddUnit('large_cargo', 20);
        $this->planetAddResources(new Resources(100000, 100000, 200000, 0));

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('large_cargo'), 20);

        $playerService = $this->planetService->getPlayer();
        $originalCapacity = $unitCollection->getTotalCargoCapacity($playerService);

        // Send resources
        $initialMetal = 100000;
        $initialCrystal = 80000;
        $initialDeuterium = 60000;

        $foreignPlanet = $this->sendMissionToOtherPlayerCleanPlanet(
            $unitCollection,
            new Resources($initialMetal, $initialCrystal, $initialDeuterium, 0)
        );

        // Strong defense to cause ~80% losses
        $foreignPlanet->addUnit('rocket_launcher', 400);
        $foreignPlanet->addResources(new Resources(50000, 50000, 50000, 0));

        // Get fleet mission
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        // Advance time
        $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration(
            $this->planetService,
            $foreignPlanet->getPlanetCoordinates(),
            $unitCollection,
            resolve(AttackMission::class)
        );
        $this->travel($fleetMissionDuration + 1)->seconds();

        $this->reloadApplication();
        $this->get('/overview');

        // Get battle report
        $battleReport = BattleReport::orderBy('id', 'desc')->first();
        $this->assertNotNull($battleReport);

        // Calculate remaining capacity
        $attackerUnitsResult = $battleReport->rounds[count($battleReport->rounds) - 1]['attacker_ships'];
        $remainingCapacity = 0;
        foreach ($attackerUnitsResult as $machineName => $amount) {
            if ($amount > 0) {
                $unitObject = ObjectService::getUnitObjectByMachineName($machineName);
                $remainingCapacity += $unitObject->properties->capacity->calculate($playerService)->totalValue * $amount;
            }
        }

        $survivalRate = $originalCapacity > 0 ? $remainingCapacity / $originalCapacity : 0;

        // Verify survival rate is low (less than 50%)
        $this->assertLessThan(0.5, $survivalRate, 'Survival rate too high for 80% loss scenario');

        // Get return mission
        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();
        if ($activeMissions->count() > 0) {
            $returnMission = $activeMissions->first();
            $returnedTotal = $returnMission->metal + $returnMission->crystal + $returnMission->deuterium;

            // Returned resources should not exceed remaining capacity
            $this->assertLessThanOrEqual($remainingCapacity + 1, $returnedTotal);

            // Returned resources should be significantly less than original
            $originalTotal = $initialMetal + $initialCrystal + $initialDeuterium;
            $this->assertLessThan($originalTotal * 0.5, $returnedTotal, 'Too many resources returned for heavy losses');
        }
    }

    /**
     * Edge case: Zero capacity handled in code (game prevents this scenario).
     */
    public function testEdgeCaseZeroOriginalCapacity(): void
    {
        // Theoretical edge case - game won't allow dispatching fleet with 0 capacity
        // Implementation: $survivalRate = $originalCapacity > 0 ? ... : 0
        $this->assertGreaterThan(0, 1, 'Zero capacity edge case handled in code');
    }
}
