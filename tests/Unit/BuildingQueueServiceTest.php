<?php

namespace Tests\Unit;

use Exception;
use OGame\Models\Resources;
use OGame\Models\User;
use OGame\Services\BuildingQueueService;
use OGame\Services\ObjectService;
use Tests\UnitTestCase;

class BuildingQueueServiceTest extends UnitTestCase
{
    protected BuildingQueueService $buildingQueueService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildingQueueService = resolve(BuildingQueueService::class);
    }

    /**
     * Test adding a downgrade to the building queue.
     */
    public function testAddDowngrade(): void
    {
        // Create user in database for foreign key constraints
        $user = User::factory()->create();

        // Create planet in database for foreign key constraints (use random coordinates to avoid conflicts)
        $planet = \OGame\Models\Planet::factory()->create([
            'user_id' => $user->id,
            'galaxy' => rand(1, 9),
            'system' => rand(1, 499),
            'planet' => rand(1, 15),
            'metal_mine' => 5,
            'metal' => 1000000,
            'crystal' => 1000000,
            'deuterium' => 1000000,
        ]);
        $this->planetService->setPlanet($planet);
        $this->planetService->updateResourceProductionStats(false);

        $this->createAndSetUserTechModel([
            'ion_technology' => 0,
        ]);

        $building = ObjectService::getObjectByMachineName('metal_mine');
        $initial_level = $this->planetService->getObjectLevel('metal_mine');
        $initial_metal = $this->planetService->metal()->get();

        // Add downgrade request
        $this->buildingQueueService->addDowngrade($this->planetService, $building->id);

        // Verify queue item was created
        $queue_items = $this->buildingQueueService->retrieveQueueItems($this->planetService);
        $this->assertCount(1, $queue_items);

        $queue_item = $queue_items->first();
        $this->assertEquals($building->id, $queue_item->object_id);
        $this->assertEquals($initial_level - 1, $queue_item->object_level_target);
        $this->assertTrue((bool)($queue_item->is_downgrade ?? false), 'is_downgrade should be true');
    }

    /**
     * Test that downgrade fails when building is at level 0.
     */
    public function testAddDowngradeLevelZero(): void
    {
        $this->createAndSetPlanetModel([
            'metal_mine' => 0,
        ]);

        $building = ObjectService::getObjectByMachineName('metal_mine');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Cannot downgrade building at level 0');

        $this->buildingQueueService->addDowngrade($this->planetService, $building->id);
    }

    /**
     * Test that downgrade deducts resources when started.
     */
    public function testDowngradeDeductsResources(): void
    {
        // Create user in database for foreign key constraints
        $user = User::factory()->create();

        // Create planet in database for foreign key constraints (use random coordinates to avoid conflicts)
        $planet = \OGame\Models\Planet::factory()->create([
            'user_id' => $user->id,
            'galaxy' => rand(1, 9),
            'system' => rand(1, 499),
            'planet' => rand(1, 15),
            'metal_mine' => 5,
            'metal' => 1000000,
            'crystal' => 1000000,
            'deuterium' => 1000000,
            'robot_factory' => 10,
            'nano_factory' => 5,
        ]);
        $this->planetService->setPlanet($planet);
        $this->planetService->updateResourceProductionStats(false);

        $this->createAndSetUserTechModel([
            'ion_technology' => 0,
        ]);

        $building = ObjectService::getObjectByMachineName('metal_mine');
        $downgrade_price = ObjectService::getObjectDowngradePrice('metal_mine', $this->planetService);

        $initial_metal = $this->planetService->metal()->get();
        $initial_crystal = $this->planetService->crystal()->get();
        $initial_deuterium = $this->planetService->deuterium()->get();

        // Add downgrade request
        $this->buildingQueueService->addDowngrade($this->planetService, $building->id);

        // Start the downgrade (this should deduct resources)
        $this->buildingQueueService->start($this->planetService);

        // Reload planet from database to get updated resources
        $planet->refresh();
        $this->planetService->setPlanet($planet);

        // Verify resources were deducted
        $this->assertEquals($initial_metal - $downgrade_price->metal->get(), $this->planetService->metal()->get());
        $this->assertEquals($initial_crystal - $downgrade_price->crystal->get(), $this->planetService->crystal()->get());
        $this->assertEquals($initial_deuterium - $downgrade_price->deuterium->get(), $this->planetService->deuterium()->get());
    }

    /**
     * Test that downgrade fails when not enough resources.
     */
    public function testAddDowngradeNotEnoughResources(): void
    {
        $this->createAndSetPlanetModel([
            'metal_mine' => 5,
            'metal' => 0,
            'crystal' => 0,
            'deuterium' => 0,
        ]);

        $building = ObjectService::getObjectByMachineName('metal_mine');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Not enough resources');

        $this->buildingQueueService->addDowngrade($this->planetService, $building->id);
    }

    /**
     * Test that downgrade in queue is not lost when upgrade completes before it.
     */
    public function testDowngradeNotLostAfterUpgrade(): void
    {
        // Create user and planet
        $user = User::factory()->create();
        $planet = \OGame\Models\Planet::factory()->create([
            'user_id' => $user->id,
            'galaxy' => rand(1, 9),
            'system' => rand(1, 499),
            'planet' => rand(1, 15),
            'metal_mine' => 4,
            'metal' => 1000000,
            'crystal' => 1000000,
            'deuterium' => 1000000,
            'robot_factory' => 10,
            'nano_factory' => 5,
        ]);
        $this->planetService->setPlanet($planet);
        $this->planetService->updateResourceProductionStats(false);

        $this->createAndSetUserTechModel([
            'ion_technology' => 0,
        ]);

        $building = ObjectService::getObjectByMachineName('metal_mine');

        // Step 1: Add upgrade to queue (level 4 -> 5)
        $this->buildingQueueService->add($this->planetService, $building->id);

        // Step 2: Add downgrade to queue (should be level 4 -> 3, but will be adjusted when upgrade completes)
        $this->buildingQueueService->addDowngrade($this->planetService, $building->id);

        // Verify both items are in queue
        $queue_items = $this->buildingQueueService->retrieveQueueItems($this->planetService);
        $this->assertCount(2, $queue_items);

        // Find upgrade and downgrade items (order may vary)
        $upgrade_item = null;
        $downgrade_item = null;
        foreach ($queue_items as $item) {
            $is_downgrade = (bool)($item->is_downgrade ?? false);
            if (!$is_downgrade) {
                $upgrade_item = $item;
            } else {
                $downgrade_item = $item;
            }
        }

        $this->assertNotNull($upgrade_item, 'Upgrade item should exist. Items: ' . json_encode($queue_items->map(fn ($i) => ['id' => $i->id, 'is_downgrade' => $i->is_downgrade, 'target' => $i->object_level_target])->toArray()));
        $this->assertNotNull($downgrade_item, 'Downgrade item should exist');
        $this->assertFalse((bool)($upgrade_item->is_downgrade ?? false), 'Upgrade item should have is_downgrade = false');
        $this->assertEquals(5, $upgrade_item->object_level_target);
        $this->assertTrue((bool)($downgrade_item->is_downgrade ?? false), 'Downgrade item should have is_downgrade = true');
        // Downgrade target should be 4 (from level 5 after upgrade completes), not 3 (from current level 4)
        $this->assertEquals(4, $downgrade_item->object_level_target, 'Downgrade target should be max_target_level - 1 (5 - 1 = 4)');

        // Step 3: Start the upgrade
        $this->buildingQueueService->start($this->planetService);

        // Verify upgrade started
        $upgrade_item->refresh();
        $this->assertEquals(1, $upgrade_item->building, 'Upgrade should be started');

        // Simulate upgrade completion by updating building level and marking as processed
        $planet->metal_mine = 5;
        $planet->save();
        $this->planetService->setPlanet($planet);
        $upgrade_item->processed = 1;
        $upgrade_item->save();

        // Step 4: Start the downgrade (should adjust target level from 3 to 4)
        $this->buildingQueueService->start($this->planetService);

        // Verify downgrade item still exists and target was updated
        $downgrade_item->refresh();
        $this->assertEquals(4, $downgrade_item->object_level_target, 'Downgrade target should be updated to current_level - 1 (5 - 1 = 4)');
        $this->assertTrue((bool)($downgrade_item->is_downgrade ?? false), 'Downgrade item should still have is_downgrade = true');
        $this->assertEquals(1, $downgrade_item->building, 'Downgrade should be started');
    }

    /**
     * Test that upgrade after downgrade calculates correct target level.
     * Scenario: Building at level 3, downgrade to 2, then upgrade should target level 3.
     */
    public function testUpgradeAfterDowngrade(): void
    {
        // Create user and planet
        $user = User::factory()->create();
        $planet = \OGame\Models\Planet::factory()->create([
            'user_id' => $user->id,
            'galaxy' => rand(1, 9),
            'system' => rand(1, 499),
            'planet' => rand(1, 15),
            'metal_mine' => 3,
            'metal' => 1000000,
            'crystal' => 1000000,
            'deuterium' => 1000000,
            'robot_factory' => 10,
            'nano_factory' => 5,
        ]);
        $this->planetService->setPlanet($planet);
        $this->planetService->updateResourceProductionStats(false);

        $this->createAndSetUserTechModel([
            'ion_technology' => 0,
        ]);

        $building = ObjectService::getObjectByMachineName('metal_mine');

        // Step 1: Add downgrade to queue (level 3 -> 2)
        $this->buildingQueueService->addDowngrade($this->planetService, $building->id);

        // Step 2: Add upgrade to queue (should target level 3, not level 4)
        $this->buildingQueueService->add($this->planetService, $building->id);

        // Verify both items are in queue
        $queue_items = $this->buildingQueueService->retrieveQueueItems($this->planetService);
        $this->assertCount(2, $queue_items);

        // Find downgrade and upgrade items
        $downgrade_item = null;
        $upgrade_item = null;
        foreach ($queue_items as $item) {
            $is_downgrade = (bool)($item->is_downgrade ?? false);
            if ($is_downgrade) {
                $downgrade_item = $item;
            } else {
                $upgrade_item = $item;
            }
        }

        $this->assertNotNull($downgrade_item, 'Downgrade item should exist');
        $this->assertNotNull($upgrade_item, 'Upgrade item should exist');
        $this->assertEquals(2, $downgrade_item->object_level_target, 'Downgrade should target level 2');
        $this->assertEquals(3, $upgrade_item->object_level_target, 'Upgrade should target level 3 (after downgrade completes), not level 4');
    }

    /**
     * Test that multiple upgrades after downgrade calculate correct target levels.
     * Scenario: Building level 5, downgrade to 4, then add 2 upgrades.
     * Expected: Upgrade 1 targets 5, Upgrade 2 targets 6.
     */
    public function testMultipleUpgradesAfterDowngrade(): void
    {
        // Create user and planet
        $user = User::factory()->create();
        $planet = \OGame\Models\Planet::factory()->create([
            'user_id' => $user->id,
            'galaxy' => rand(1, 9),
            'system' => rand(1, 499),
            'planet' => rand(1, 15),
            'metal_mine' => 5,
            'metal' => 1000000,
            'crystal' => 1000000,
            'deuterium' => 1000000,
            'robot_factory' => 10,
            'nano_factory' => 5,
        ]);
        $this->planetService->setPlanet($planet);
        $this->planetService->updateResourceProductionStats(false);

        $this->createAndSetUserTechModel([
            'ion_technology' => 0,
        ]);

        $building = ObjectService::getObjectByMachineName('metal_mine');

        // Step 1: Add downgrade to queue (level 5 -> 4)
        $this->buildingQueueService->addDowngrade($this->planetService, $building->id);

        // Step 2: Add first upgrade to queue (should target level 5, not level 6)
        $this->buildingQueueService->add($this->planetService, $building->id);

        // Step 3: Add second upgrade to queue (should target level 6, not level 7)
        $this->buildingQueueService->add($this->planetService, $building->id);

        // Verify all 3 items are in queue
        $queue_items = $this->buildingQueueService->retrieveQueueItems($this->planetService);
        $this->assertCount(3, $queue_items);

        // Find downgrade and upgrade items
        $downgrade_item = null;
        $upgrade_items = [];
        foreach ($queue_items as $item) {
            $is_downgrade = (bool)($item->is_downgrade ?? false);
            if ($is_downgrade) {
                $downgrade_item = $item;
            } else {
                $upgrade_items[] = $item;
            }
        }

        $this->assertNotNull($downgrade_item, 'Downgrade item should exist');
        $this->assertCount(2, $upgrade_items, 'Should have 2 upgrade items');

        // Verify target levels
        $this->assertEquals(4, $downgrade_item->object_level_target, 'Downgrade should target level 4');

        // Sort upgrade items by ID to ensure correct order
        usort($upgrade_items, function ($a, $b) {
            return $a->id <=> $b->id;
        });

        $this->assertEquals(5, $upgrade_items[0]->object_level_target, 'First upgrade should target level 5 (after downgrade completes)');
        $this->assertEquals(6, $upgrade_items[1]->object_level_target, 'Second upgrade should target level 6 (after first upgrade completes)');
    }
}
