<?php

namespace Tests\Unit;

use OGame\Models\BuildingQueue;
use OGame\Models\Resources;
use OGame\Services\ObjectService;
use Tests\AccountTestCase;

class ObjectServiceTest extends AccountTestCase
{
    protected ObjectService $object_service;

    /**
     * Set up common test components.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->object_service = new ObjectService();
    }

    /**
     * Tests maximum building amount returns correct value.
     */
    public function testGetObjectMaxBuildAmount(): void
    {
        // Test with requirements not met
        $max_build_amount = $this->object_service->getObjectMaxBuildAmount('plasma_turret', $this->planetService, false);
        $this->assertEquals(0, $max_build_amount);

        // Test with object limited to one instance
        $max_build_amount = $this->object_service->getObjectMaxBuildAmount('small_shield_dome', $this->planetService, true);
        $this->assertEquals(1, $max_build_amount);

        // Test with object limited to one instance which already exists
        $this->planetSetObjectLevel('small_shield_dome', 1);
        $max_build_amount = $this->object_service->getObjectMaxBuildAmount('small_shield_dome', $this->planetService, true);
        $this->assertEquals(0, $max_build_amount);

        // Test it calculates max amount correctly
        $this->planetAddResources(new Resources(24000, 6000, 0, 0));
        $max_build_amount = $this->object_service->getObjectMaxBuildAmount('anti_ballistic_missile', $this->planetService, true);
        $this->assertEquals(3, $max_build_amount);
    }

    /**
     * Tests object requirements are verified against prior levels, research and buildings
     * including items in building and research queues.
     */
    public function testObjectRequirementsMet(): void
    {
        // Assert that requirements are not met if prior levels doesn't exist
        $this->assertFalse($this->object_service->objectRequirementsMet('robot_factory', $this->planetService, $this->planetService->getPlayer(), 2));

        $this->planetSetObjectLevel('robot_factory', 1);

        // Assert that requirements are met if prior levels exists
        $this->assertTrue($this->object_service->objectRequirementsMet('robot_factory', $this->planetService, $this->planetService->getPlayer(), 2));

        // Assert that requirements are not met if requisites are missing
        $this->assertFalse($this->object_service->objectRequirementsMet('missile_silo', $this->planetService, $this->planetService->getPlayer(), 1));

        // Add  to build queue
        $queue = new BuildingQueue();
        $queue->planet_id = $this->planetService->getPlanetId();
        $queue->object_id = 21;
        $queue->object_level_target = 1;
        $queue->save();

        // Assert that requirements are met if requisites are in build queue
        $this->assertTrue($this->object_service->objectRequirementsMet('missile_silo', $this->planetService, $this->planetService->getPlayer(), 1));

        // Assert that research requirements are not met if building requirements are not met
        $this->assertFalse($this->object_service->objectRequirementsMet('computer_technology', $this->planetService, $this->planetService->getPlayer(), 1));
    }
}
