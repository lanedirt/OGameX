<?php

namespace Tests\Feature;

use Illuminate\Support\Carbon;
use OGame\Factories\PlanetServiceFactory;
use OGame\Models\FleetMission;
use OGame\Services\JumpGateService;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;
use Tests\MoonTestCase;

/**
 * Test class for Jump Gate functionality.
 * Tests ship transfer between moons, cooldown mechanics, and race condition prevention.
 */
class JumpGateTest extends MoonTestCase
{
    protected JumpGateService $jumpGateService;
    protected PlanetService $secondMoonService;

    /**
     * Set up the test environment with two moons that have Jump Gates.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Set fleet war speed to 1 for predictable cooldown calculations
        $settingsService = resolve(\OGame\Services\SettingsService::class);
        $settingsService->set('fleet_speed_war', 1);

        $this->jumpGateService = resolve(JumpGateService::class);

        // Set up Jump Gate on first moon
        $jumpGateObject = ObjectService::getObjectByMachineName('jump_gate');
        $this->moonService->setObjectLevel($jumpGateObject->id, 1, true);

        // Create second moon for the second planet
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $this->secondMoonService = $planetServiceFactory->createMoonForPlanet($this->secondPlanetService, 100000, 20);

        // Set up Jump Gate on second moon
        $this->secondMoonService->setObjectLevel($jumpGateObject->id, 1, true);
        $this->secondMoonService->reloadPlanet();

        // Add ships to first moon for testing
        $this->moonService->addUnit('small_cargo', 10, true);
        $this->moonService->addUnit('light_fighter', 5, true);
        $this->moonService->reloadPlanet();
    }

    /**
     * Test that ships can be transferred between moons via Jump Gate.
     */
    public function testTransferShipsBetweenMoons(): void
    {
        // Verify initial state
        $this->assertEquals(10, $this->moonService->getObjectAmount('small_cargo'));
        $this->assertEquals(0, $this->secondMoonService->getObjectAmount('small_cargo'));

        // Transfer ships
        $result = $this->jumpGateService->transferShips(
            $this->moonService,
            $this->secondMoonService,
            ['small_cargo' => 5]
        );

        $this->assertTrue($result);

        // Reload and verify
        $this->moonService->reloadPlanet();
        $this->secondMoonService->reloadPlanet();

        $this->assertEquals(5, $this->moonService->getObjectAmount('small_cargo'));
        $this->assertEquals(5, $this->secondMoonService->getObjectAmount('small_cargo'));
    }

    /**
     * Test that transfer fails when source moon doesn't have enough ships.
     */
    public function testTransferFailsWithInsufficientShips(): void
    {
        $result = $this->jumpGateService->transferShips(
            $this->moonService,
            $this->secondMoonService,
            ['small_cargo' => 100] // More than available
        );

        $this->assertFalse($result);

        // Verify no ships were transferred
        $this->moonService->reloadPlanet();
        $this->assertEquals(10, $this->moonService->getObjectAmount('small_cargo'));
    }

    /**
     * Test that solar satellites cannot be transferred.
     */
    public function testSolarSatellitesCannotBeTransferred(): void
    {
        // Add solar satellites to moon
        $this->moonService->addUnit('solar_satellite', 10, true);
        $this->moonService->reloadPlanet();

        $result = $this->jumpGateService->transferShips(
            $this->moonService,
            $this->secondMoonService,
            ['solar_satellite' => 5]
        );

        $this->assertFalse($result);
    }

    /**
     * Test that cooldown is set on both moons after transfer.
     */
    public function testCooldownSetAfterTransfer(): void
    {
        // Verify no cooldown initially
        $this->assertFalse($this->jumpGateService->isOnCooldown($this->moonService));
        $this->assertFalse($this->jumpGateService->isOnCooldown($this->secondMoonService));

        // Set cooldown (simulating what happens after transfer)
        $this->jumpGateService->setCooldown($this->moonService, $this->secondMoonService);

        // Verify cooldown is now active on both moons
        $this->assertTrue($this->jumpGateService->isOnCooldown($this->moonService));
        $this->assertTrue($this->jumpGateService->isOnCooldown($this->secondMoonService));

        // Verify remaining cooldown is greater than 0
        $this->assertGreaterThan(0, $this->jumpGateService->getRemainingCooldown($this->moonService));
        $this->assertGreaterThan(0, $this->jumpGateService->getRemainingCooldown($this->secondMoonService));
    }

    /**
     * Test that cooldown expires after the calculated time.
     */
    public function testCooldownExpires(): void
    {
        $this->jumpGateService->setCooldown($this->moonService, $this->secondMoonService);

        // Get remaining cooldown
        $remaining = $this->jumpGateService->getRemainingCooldown($this->moonService);

        // Travel past the cooldown
        $this->travel($remaining + 1)->seconds();

        // Reload planet to get fresh data
        $this->moonService->reloadPlanet();

        // Cooldown should be expired
        $this->assertFalse($this->jumpGateService->isOnCooldown($this->moonService));
        $this->assertEquals(0, $this->jumpGateService->getRemainingCooldown($this->moonService));
    }

    /**
     * Test that higher Jump Gate level reduces cooldown.
     * At fleet_speed_war=1: base_time = 60 minutes = 3600 seconds
     * Level 1: 0% reduction = 3600s
     * Level 5: 40% reduction = 2160s
     */
    public function testHigherLevelReducesCooldown(): void
    {
        // Level 1: 3600 seconds (60 minutes, no reduction)
        $cooldownLevel1 = $this->jumpGateService->calculateCooldown(1);
        $this->assertEquals(3600, $cooldownLevel1);

        // Level 5: 2160 seconds (40% reduction from 3600)
        $cooldownLevel5 = $this->jumpGateService->calculateCooldown(5);
        $this->assertEquals(2160, $cooldownLevel5);

        // Higher level should have shorter cooldown
        $this->assertLessThan($cooldownLevel1, $cooldownLevel5);
    }

    /**
     * Test that eligible targets excludes current moon.
     */
    public function testEligibleTargetsExcludesCurrentMoon(): void
    {
        $player = $this->moonService->getPlayer();
        $eligibleTargets = $this->jumpGateService->getEligibleTargets($player, $this->moonService);

        // Should not include the current moon
        foreach ($eligibleTargets as $target) {
            $this->assertNotEquals($this->moonService->getPlanetId(), $target->getPlanetId());
        }
    }

    /**
     * Test that eligible targets excludes moons on cooldown.
     */
    public function testEligibleTargetsExcludesCooldownMoons(): void
    {
        // Set cooldown on second moon (unix timestamp)
        $this->secondMoonService->setJumpGateCooldown((int) Carbon::now()->addHour()->timestamp);

        $player = $this->moonService->getPlayer();
        $eligibleTargets = $this->jumpGateService->getEligibleTargets($player, $this->moonService);

        // Second moon should not be in eligible targets
        foreach ($eligibleTargets as $target) {
            $this->assertNotEquals($this->secondMoonService->getPlanetId(), $target->getPlanetId());
        }
    }

    /**
     * Test that eligible targets excludes moons without Jump Gate.
     */
    public function testEligibleTargetsExcludesMoonsWithoutJumpGate(): void
    {
        // Remove Jump Gate from second moon
        $jumpGateObject = ObjectService::getObjectByMachineName('jump_gate');
        $this->secondMoonService->setObjectLevel($jumpGateObject->id, 0, true);

        $player = $this->moonService->getPlayer();
        $eligibleTargets = $this->jumpGateService->getEligibleTargets($player, $this->moonService);

        // Second moon should not be in eligible targets
        foreach ($eligibleTargets as $target) {
            $this->assertNotEquals($this->secondMoonService->getPlanetId(), $target->getPlanetId());
        }
    }

    /**
     * Test race condition prevention - detects unprocessed arrived fleet.
     */
    public function testDetectsUnprocessedArrivedFleet(): void
    {
        // Initially no unprocessed fleets
        $this->assertFalse($this->jumpGateService->hasUnprocessedArrivedFleet($this->moonService));

        // Create a foreign player's attack fleet that has arrived but not processed
        $foreignPlanet = $this->getNearbyForeignPlanet();

        // Create fleet mission directly in database
        $fleetMission = new FleetMission();
        $fleetMission->user_id = $foreignPlanet->getPlayer()->getId();
        $fleetMission->planet_id_from = $foreignPlanet->getPlanetId();
        $fleetMission->planet_id_to = $this->moonService->getPlanetId();
        $fleetMission->mission_type = 1; // Attack
        $fleetMission->time_departure = (int) Carbon::now()->subMinutes(10)->timestamp;
        $fleetMission->time_arrival = (int) Carbon::now()->subMinutes(1)->timestamp; // Already arrived
        $fleetMission->processed = 0; // Not yet processed
        $fleetMission->canceled = 0;
        $fleetMission->light_fighter = 5;
        $fleetMission->save();

        // Now should detect unprocessed fleet
        $this->assertTrue($this->jumpGateService->hasUnprocessedArrivedFleet($this->moonService));
    }

    /**
     * Test that fleet still in transit does not trigger race condition check.
     */
    public function testFleetInTransitDoesNotTriggerRaceCondition(): void
    {
        // Create a foreign player's attack fleet that has NOT arrived yet
        $foreignPlanet = $this->getNearbyForeignPlanet();

        $fleetMission = new FleetMission();
        $fleetMission->user_id = $foreignPlanet->getPlayer()->getId();
        $fleetMission->planet_id_from = $foreignPlanet->getPlanetId();
        $fleetMission->planet_id_to = $this->moonService->getPlanetId();
        $fleetMission->mission_type = 1; // Attack
        $fleetMission->time_departure = (int) Carbon::now()->timestamp;
        $fleetMission->time_arrival = (int) Carbon::now()->addHour()->timestamp; // Still in transit
        $fleetMission->processed = 0;
        $fleetMission->canceled = 0;
        $fleetMission->light_fighter = 5;
        $fleetMission->save();

        // Should NOT detect as unprocessed arrived fleet (still in transit)
        $this->assertFalse($this->jumpGateService->hasUnprocessedArrivedFleet($this->moonService));
    }

    /**
     * Test that own player's fleet does not trigger race condition check.
     */
    public function testOwnFleetDoesNotTriggerRaceCondition(): void
    {
        // Create own player's return fleet
        $fleetMission = new FleetMission();
        $fleetMission->user_id = $this->moonService->getPlayer()->getId(); // Own player
        $fleetMission->planet_id_from = $this->secondPlanetService->getPlanetId();
        $fleetMission->planet_id_to = $this->moonService->getPlanetId();
        $fleetMission->mission_type = 3; // Transport
        $fleetMission->time_departure = (int) Carbon::now()->subMinutes(10)->timestamp;
        $fleetMission->time_arrival = (int) Carbon::now()->subMinutes(1)->timestamp; // Already arrived
        $fleetMission->processed = 0;
        $fleetMission->canceled = 0;
        $fleetMission->small_cargo = 5;
        $fleetMission->save();

        // Should NOT detect as unprocessed hostile fleet (own player)
        $this->assertFalse($this->jumpGateService->hasUnprocessedArrivedFleet($this->moonService));
    }

    /**
     * Test that processed fleet does not trigger race condition check.
     */
    public function testProcessedFleetDoesNotTriggerRaceCondition(): void
    {
        $foreignPlanet = $this->getNearbyForeignPlanet();

        $fleetMission = new FleetMission();
        $fleetMission->user_id = $foreignPlanet->getPlayer()->getId();
        $fleetMission->planet_id_from = $foreignPlanet->getPlanetId();
        $fleetMission->planet_id_to = $this->moonService->getPlanetId();
        $fleetMission->mission_type = 1; // Attack
        $fleetMission->time_departure = (int) Carbon::now()->subMinutes(10)->timestamp;
        $fleetMission->time_arrival = (int) Carbon::now()->subMinutes(1)->timestamp;
        $fleetMission->processed = 1; // Already processed
        $fleetMission->canceled = 0;
        $fleetMission->light_fighter = 5;
        $fleetMission->save();

        // Should NOT detect (already processed)
        $this->assertFalse($this->jumpGateService->hasUnprocessedArrivedFleet($this->moonService));
    }

    /**
     * Test Jump Gate controller blocks transfer when hostile fleet is being processed.
     * Uses Laravel's partialMock to simulate the race condition since middleware processes fleets before controller.
     */
    public function testControllerBlocksTransferDuringFleetProcessing(): void
    {
        // Switch to moon context
        $this->switchToMoon();

        // Create a partial mock of JumpGateService that returns true for hasUnprocessedArrivedFleet
        // This simulates the race condition where a fleet has arrived but hasn't been processed yet
        // (In real scenario, the middleware processes fleets before controller runs, so we mock this)
        $this->partialMock(JumpGateService::class, function ($mock): void {
            $mock->shouldReceive('hasUnprocessedArrivedFleet')
                ->andReturn(true);
        });

        // Attempt to use Jump Gate via controller
        $response = $this->post('/ajax/jumpgate/execute', [
            '_token' => csrf_token(),
            'targetMoonId' => $this->secondMoonService->getPlanetId(),
            'ship_202' => 5, // small_cargo
        ]);

        // Should get error response
        $response->assertStatus(500);
        $response->assertSee('fleet mission is being processed');
    }

    /**
     * Test Jump Gate controller allows transfer when no fleet is being processed.
     */
    public function testControllerAllowsTransferWhenNoFleetProcessing(): void
    {
        // Switch to moon context
        $this->switchToMoon();

        // Attempt to use Jump Gate via controller
        $response = $this->post('/ajax/jumpgate/execute', [
            '_token' => csrf_token(),
            'targetMoonId' => $this->secondMoonService->getPlanetId(),
            'ship_202' => 5, // small_cargo
        ]);

        // Should succeed
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify ships transferred
        $this->moonService->reloadPlanet();
        $this->secondMoonService->reloadPlanet();

        $this->assertEquals(5, $this->moonService->getObjectAmount('small_cargo'));
        $this->assertEquals(5, $this->secondMoonService->getObjectAmount('small_cargo'));
    }

    /**
     * Test default target can be set and retrieved.
     */
    public function testDefaultTargetSetAndGet(): void
    {
        // Initially no default target
        $defaultTarget = $this->jumpGateService->getDefaultTarget(
            $this->moonService,
            $this->moonService->getPlayer()
        );
        $this->assertNull($defaultTarget);

        // Set default target
        $this->jumpGateService->setDefaultTarget(
            $this->moonService,
            $this->secondMoonService->getPlanetId()
        );

        // Reload moon and player to get fresh data
        $this->moonService->reloadPlanet();
        $player = $this->moonService->getPlayer();
        $player->load($player->getId());

        $defaultTarget = $this->jumpGateService->getDefaultTarget(
            $this->moonService,
            $player
        );

        $this->assertNotNull($defaultTarget);
        $this->assertEquals($this->secondMoonService->getPlanetId(), $defaultTarget->getPlanetId());
    }

    /**
     * Test cooldown format output.
     */
    public function testCooldownFormat(): void
    {
        // Test various formats
        $this->assertEquals('0s', $this->jumpGateService->formatCooldown(0));
        $this->assertEquals('30s', $this->jumpGateService->formatCooldown(30));
        $this->assertEquals('1m', $this->jumpGateService->formatCooldown(60));
        $this->assertEquals('1m 30s', $this->jumpGateService->formatCooldown(90));
        $this->assertEquals('5m', $this->jumpGateService->formatCooldown(300));
        $this->assertEquals('10m 15s', $this->jumpGateService->formatCooldown(615));
    }

    /**
     * Test multiple ship types can be transferred at once.
     */
    public function testMultipleShipTypesTransfer(): void
    {
        $result = $this->jumpGateService->transferShips(
            $this->moonService,
            $this->secondMoonService,
            [
                'small_cargo' => 3,
                'light_fighter' => 2,
            ]
        );

        $this->assertTrue($result);

        $this->moonService->reloadPlanet();
        $this->secondMoonService->reloadPlanet();

        // Verify source moon
        $this->assertEquals(7, $this->moonService->getObjectAmount('small_cargo'));
        $this->assertEquals(3, $this->moonService->getObjectAmount('light_fighter'));

        // Verify target moon
        $this->assertEquals(3, $this->secondMoonService->getObjectAmount('small_cargo'));
        $this->assertEquals(2, $this->secondMoonService->getObjectAmount('light_fighter'));
    }

    /**
     * Test getTransferableShips returns correct ship list.
     */
    public function testGetTransferableShipsExcludesSolarSatellite(): void
    {
        $ships = $this->jumpGateService->getTransferableShips();

        $this->assertNotContains('solar_satellite', $ships);
        $this->assertContains('small_cargo', $ships);
        $this->assertContains('light_fighter', $ships);
        $this->assertContains('deathstar', $ships);
    }
}
