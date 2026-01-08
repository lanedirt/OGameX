<?php

namespace Tests\Feature;

use OGame\Models\UnitQueue;
use OGame\Models\Resources;
use OGame\Services\ObjectService;
use Tests\AccountTestCase;

/**
 * Test that missile silo capacity works correctly, including queued missiles.
 */
class MissileSiloCapacityTest extends AccountTestCase
{
    /**
     * Prepare the planet for the test with missile silo and required buildings.
     *
     * @return void
     */
    private function basicSetup(): void
    {
        $this->planetSetObjectLevel('robot_factory', 2);
        $this->planetSetObjectLevel('shipyard', 1);
        $this->planetSetObjectLevel('missile_silo', 4); // 40 slots total (need level 4 for IPM requirement)
        $this->playerSetResearchLevel('impulse_drive', 1);

        // Give plenty of resources
        $this->planetAddResources(new Resources(10000000, 10000000, 10000000, 0));
    }

    public function testCannotBuildIPMWhenSiloIsFull(): void
    {
        $this->basicSetup();

        // Add 20 IPM to fill the silo completely (20 * 2 = 40 slots)
        $this->planetAddUnit('interplanetary_missile', 20);

        $maxBuildable = ObjectService::getObjectMaxBuildAmount('interplanetary_missile', $this->planetService, true);
        $this->assertEquals(0, $maxBuildable, 'Should not be able to build IPM when silo is full');
    }

    public function testCannotBuildABMWhenSiloIsFull(): void
    {
        $this->basicSetup();

        // Add 40 ABM to fill the silo completely (40 * 1 = 40 slots)
        $this->planetAddUnit('anti_ballistic_missile', 40);

        $maxBuildable = ObjectService::getObjectMaxBuildAmount('anti_ballistic_missile', $this->planetService, true);
        $this->assertEquals(0, $maxBuildable, 'Should not be able to build ABM when silo is full');
    }

    public function testMixedIPMAndABMCapacity(): void
    {
        $this->basicSetup();

        // Add 10 IPM and 20 ABM (10 * 2 + 20 * 1 = 40 slots, FULL)
        $this->planetAddUnit('interplanetary_missile', 10);
        $this->planetAddUnit('anti_ballistic_missile', 20);

        $maxIPM = ObjectService::getObjectMaxBuildAmount('interplanetary_missile', $this->planetService, true);
        $maxABM = ObjectService::getObjectMaxBuildAmount('anti_ballistic_missile', $this->planetService, true);

        $this->assertEquals(0, $maxIPM, 'Should not be able to build more IPM when silo is full');
        $this->assertEquals(0, $maxABM, 'Should not be able to build more ABM when silo is full');
    }

    public function testCanBuildWhenSiloHasSpace(): void
    {
        $this->basicSetup();

        // Add only 5 ABM (5 * 1 = 5 slots used, 35 remaining)
        $this->planetAddUnit('anti_ballistic_missile', 5);

        $maxABM = ObjectService::getObjectMaxBuildAmount('anti_ballistic_missile', $this->planetService, true);
        $this->assertGreaterThan(0, $maxABM, 'Should be able to build more ABM when silo has space');

        $maxIPM = ObjectService::getObjectMaxBuildAmount('interplanetary_missile', $this->planetService, true);
        $this->assertGreaterThan(0, $maxIPM, 'Should be able to build IPM when silo has space');
    }

    public function testNoSiloMeansNoMissiles(): void
    {
        // Setup without missile silo
        $this->planetSetObjectLevel('robot_factory', 2);
        $this->planetSetObjectLevel('shipyard', 1);
        $this->planetSetObjectLevel('missile_silo', 0);
        $this->playerSetResearchLevel('impulse_drive', 1);
        $this->planetAddResources(new Resources(10000000, 10000000, 10000000, 0));

        $maxIPM = ObjectService::getObjectMaxBuildAmount('interplanetary_missile', $this->planetService, true);
        $maxABM = ObjectService::getObjectMaxBuildAmount('anti_ballistic_missile', $this->planetService, true);

        $this->assertEquals(0, $maxIPM, 'Should not be able to build IPM without silo');
        $this->assertEquals(0, $maxABM, 'Should not be able to build ABM without silo');
    }

    public function testCannotDowngradeSiloWithMissiles(): void
    {
        $this->basicSetup();

        // Test with IPM missiles - add them directly
        $this->planetAddUnit('interplanetary_missile', 5);

        $canDowngrade = ObjectService::canDowngradeBuilding('missile_silo', $this->planetService);
        $this->assertFalse($canDowngrade, 'Should not be able to downgrade missile silo with IPM missiles');

        // Reset and test with ABM missiles
        $this->basicSetup();
        $this->planetAddUnit('anti_ballistic_missile', 5);

        $canDowngrade = ObjectService::canDowngradeBuilding('missile_silo', $this->planetService);
        $this->assertFalse($canDowngrade, 'Should not be able to downgrade missile silo with ABM missiles');

        // Reset and test with both missile types
        $this->basicSetup();
        $this->planetAddUnit('interplanetary_missile', 3);
        $this->planetAddUnit('anti_ballistic_missile', 4);

        $canDowngrade = ObjectService::canDowngradeBuilding('missile_silo', $this->planetService);
        $this->assertFalse($canDowngrade, 'Should not be able to downgrade missile silo with both missile types');
    }

    public function testCanDowngradeEmptySilo(): void
    {
        $this->basicSetup();

        // Don't build any missiles
        $canDowngrade = ObjectService::canDowngradeBuilding('missile_silo', $this->planetService);
        $this->assertTrue($canDowngrade, 'Should be able to downgrade empty missile silo');
    }

    public function testQueuedIPMCountTowardsCapacity(): void
    {
        $this->basicSetup();

        // Queue 19 IPM (would use 38 slots out of 40, leaving 2)
        $this->addDefenseBuildRequest('interplanetary_missile', 19);

        // Verify they were queued
        $ipmObjectId = ObjectService::getObjectByMachineName('interplanetary_missile')->id;
        $queuedIPM = UnitQueue::where('planet_id', $this->planetService->getPlanetId())
            ->where('object_id', $ipmObjectId)
            ->where('processed', 0)
            ->sum('object_amount');
        $this->assertEquals(19, $queuedIPM, 'Should have 19 IPM in queue');

        // Check max buildable - should be 1 (2 remaining slots / 2 per IPM)
        $maxIPM = ObjectService::getObjectMaxBuildAmount('interplanetary_missile', $this->planetService, true);
        $this->assertEquals(1, $maxIPM, 'Should only be able to build 1 more IPM with queue accounted for');
    }

    public function testQueuedABMCountTowardsCapacity(): void
    {
        $this->basicSetup();

        // Queue 37 ABM (would use 37 slots out of 40, leaving 3)
        $this->addDefenseBuildRequest('anti_ballistic_missile', 37);

        // Check max buildable - should be 3 (3 remaining slots / 1 per ABM)
        $maxABM = ObjectService::getObjectMaxBuildAmount('anti_ballistic_missile', $this->planetService, true);
        $this->assertEquals(3, $maxABM, 'Should only be able to build 3 more ABM with queue accounted for');
    }

    public function testQueuedMixedMissilesCountTowardsCapacity(): void
    {
        $this->basicSetup();

        // Queue 15 IPM and 7 ABM (uses 30 + 7 = 37 slots, leaving 3)
        $this->addDefenseBuildRequest('interplanetary_missile', 15);
        $this->addDefenseBuildRequest('anti_ballistic_missile', 7);

        // Check max buildable
        $maxIPM = ObjectService::getObjectMaxBuildAmount('interplanetary_missile', $this->planetService, true);
        $this->assertEquals(1, $maxIPM, 'Should only be able to build 1 more IPM with mixed queue');

        $maxABM = ObjectService::getObjectMaxBuildAmount('anti_ballistic_missile', $this->planetService, true);
        $this->assertEquals(3, $maxABM, 'Should be able to build 3 more ABM with mixed queue');
    }

    public function testCannotExceedSiloCapacityWithQueue(): void
    {
        $this->basicSetup();

        // Fill the silo completely with queued IPM (20 IPM = 40 slots)
        $this->addDefenseBuildRequest('interplanetary_missile', 20);

        // Should not be able to build any more
        $maxIPM = ObjectService::getObjectMaxBuildAmount('interplanetary_missile', $this->planetService, true);
        $this->assertEquals(0, $maxIPM, 'Should not be able to build more IPM when silo is full with queue');

        $maxABM = ObjectService::getObjectMaxBuildAmount('anti_ballistic_missile', $this->planetService, true);
        $this->assertEquals(0, $maxABM, 'Should not be able to build any ABM when silo is full with queue');
    }
}
