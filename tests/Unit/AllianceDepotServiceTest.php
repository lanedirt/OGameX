<?php

namespace Tests\Unit;

use OGame\Models\FleetMission;
use OGame\Services\AllianceDepotService;
use Tests\UnitTestCase;

/**
 * Test that Alliance Depot service works as expected.
 */
class AllianceDepotServiceTest extends UnitTestCase
{
    private AllianceDepotService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AllianceDepotService();
    }

    /**
     * Test deuterium cost calculation for different ship types.
     */
    public function testCalculateSupplyRocketCost(): void
    {
        // Create a mock fleet mission with various ships
        $mission = new FleetMission();
        $mission->small_cargo = 10;      // 10 * 1 deut/hour = 10
        $mission->large_cargo = 5;       // 5 * 5 deut/hour = 25
        $mission->light_fighter = 20;    // 20 * 2 deut/hour = 40
        $mission->cruiser = 3;           // 3 * 30 deut/hour = 90
        $mission->battle_ship = 2;       // 2 * 60 deut/hour = 120
        // Total per hour: 285

        // Test 1 hour extension
        $cost1Hour = $this->service->calculateSupplyRocketCost($mission, 1);
        $this->assertEquals(285, $cost1Hour, 'Cost for 1 hour should be 285');

        // Test 4 hour extension
        $cost4Hours = $this->service->calculateSupplyRocketCost($mission, 4);
        $this->assertEquals(1140, $cost4Hours, 'Cost for 4 hours should be 1140 (285 * 4)');
    }

    /**
     * Test that fleets holding less than 1 hour cannot be extended.
     */
    public function testCannotExtendShortHold(): void
    {
        $currentTime = time();

        // Create outbound mission that arrived with 30 minutes (1800 seconds) hold time in GAME time
        $outbound = new FleetMission();
        $outbound->mission_type = 5;
        $outbound->time_arrival = $currentTime - 900; // Arrived 15 minutes ago
        $outbound->time_holding = 1800; // 30 minutes hold time in game-time (less than 1 hour)
        $outbound->processed = 0;
        $outbound->canceled = 0;

        // Create return mission departing in 15 minutes (with 200x speed multiplier: 1800/200 = 9 seconds real-world)
        $return = new FleetMission();
        $return->mission_type = 5;
        $return->time_departure = $currentTime + 9; // Departs in 9 seconds (1800 game-time / 200 speed)
        $return->time_arrival = $currentTime + 1000; // Some future time
        $return->canceled = 0;

        // Should not be extendable (holding < 1 hour originally)
        $canExtend = $this->service->canExtendHoldTime($outbound, $return);
        $this->assertFalse($canExtend, 'Fleet holding less than 1 hour should not be extendable');
    }

    /**
     * Test that fleets holding 1+ hours can be extended.
     */
    public function testCanExtendLongHold(): void
    {
        $currentTime = time();

        // Create outbound mission that arrived with 2 hours (7200 seconds) hold time in GAME time
        $outbound = new FleetMission();
        $outbound->mission_type = 5;
        $outbound->time_arrival = $currentTime - 3600; // Arrived 1 hour ago
        $outbound->time_holding = 7200; // 2 hours hold time in game-time
        $outbound->processed = 0;
        $outbound->canceled = 0;

        // Create return mission departing in 36 seconds (7200 game-time / 200 speed = 36 seconds real-world)
        $return = new FleetMission();
        $return->mission_type = 5;
        $return->time_departure = $currentTime + 36; // Departs in 36 seconds
        $return->time_arrival = $currentTime + 1000; // Some future time
        $return->canceled = 0;

        // Should be extendable (holding >= 1 hour)
        $canExtend = $this->service->canExtendHoldTime($outbound, $return);
        $this->assertTrue($canExtend, 'Fleet holding 2 hours should be extendable');
    }

    /**
     * Test supply capacity calculation.
     */
    public function testSupplyCapacity(): void
    {
        $this->assertEquals(10000, $this->service->getSupplyCapacity(1), 'Level 1 should have 10,000 capacity');
        $this->assertEquals(20000, $this->service->getSupplyCapacity(2), 'Level 2 should have 20,000 capacity');
        $this->assertEquals(50000, $this->service->getSupplyCapacity(5), 'Level 5 should have 50,000 capacity');
    }

    /**
     * Test cost calculation with decimal ships (Death Star, Espionage Probe).
     */
    public function testCalculateCostWithDecimalShips(): void
    {
        $mission = new FleetMission();
        $mission->espionage_probe = 100;  // 100 * 0.1 = 10 deut/hour
        $mission->deathstar = 10;         // 10 * 0.1 = 1 deut/hour
        // Total per hour: 11

        $cost = $this->service->calculateSupplyRocketCost($mission, 1);
        $this->assertEquals(11, $cost, 'Cost should handle decimal ship consumption correctly');
    }

    /**
     * Test that fleet that hasn't arrived yet cannot be extended.
     */
    public function testCannotExtendFleetNotYetArrived(): void
    {
        $currentTime = time();

        // Create outbound mission that hasn't arrived yet
        $outbound = new FleetMission();
        $outbound->mission_type = 5;
        $outbound->time_arrival = $currentTime + 3600; // Arrives in 1 hour
        $outbound->processed = 0;
        $outbound->canceled = 0;

        // Create return mission
        $return = new FleetMission();
        $return->mission_type = 5;
        $return->time_departure = $currentTime + 7200;
        $return->time_arrival = $currentTime + 10800;
        $return->canceled = 0;

        // Should not be extendable (not arrived yet)
        $canExtend = $this->service->canExtendHoldTime($outbound, $return);
        $this->assertFalse($canExtend, 'Fleet that has not arrived should not be extendable');
    }

    /**
     * Test that fleet with return already departed cannot be extended.
     */
    public function testCannotExtendFleetAlreadyReturning(): void
    {
        $currentTime = time();

        // Create outbound mission that arrived
        $outbound = new FleetMission();
        $outbound->mission_type = 5;
        $outbound->time_arrival = $currentTime - 7200; // Arrived 2 hours ago
        $outbound->processed = 0;
        $outbound->canceled = 0;

        // Create return mission that already departed
        $return = new FleetMission();
        $return->mission_type = 5;
        $return->time_departure = $currentTime - 1800; // Departed 30 minutes ago
        $return->time_arrival = $currentTime + 1800;
        $return->canceled = 0;

        // Should not be extendable (already returning)
        $canExtend = $this->service->canExtendHoldTime($outbound, $return);
        $this->assertFalse($canExtend, 'Fleet that is already returning should not be extendable');
    }
}
