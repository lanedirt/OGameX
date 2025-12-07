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
}
