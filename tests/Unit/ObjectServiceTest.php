<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use OGame\Models\BuildingQueue;
use OGame\Models\Planet;
use OGame\Services\ObjectService;
use Tests\UnitTestCase;

class ObjectServiceTest extends UnitTestCase
{
    use DatabaseTransactions;

    protected ObjectService $object_service;

    protected Planet $planet;

    /**
     * Set up common test components.
     *
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->object_service = new ObjectService();

        $this->planet = Planet::factory()->make(['id' => 1]);
        $this->planetService->setPlanet($this->planet);
    }

    /**
     * Tests maximum building amount returns correct value.
     */
    public function testGetObjectMaxBuildAmount(): void
    {
        $this->createAndSetPlanetModel([]);

        // Test with requirements not met
        $max_build_amount = $this->object_service->getObjectMaxBuildAmount('plasma_turret', $this->planetService, false);
        $this->assertEquals(0, $max_build_amount);

        // Test with object limited to one instance
        $max_build_amount = $this->object_service->getObjectMaxBuildAmount('small_shield_dome', $this->planetService, true);
        $this->assertEquals(1, $max_build_amount);

        $this->createAndSetPlanetModel([
            'small_shield_dome' => 1,
        ]);

        // Test with object limited to one instance which already exists
        $max_build_amount = $this->object_service->getObjectMaxBuildAmount('small_shield_dome', $this->planetService, true);
        $this->assertEquals(0, $max_build_amount);

        $this->createAndSetPlanetModel([
            'metal' => 24000,
            'crystal' => 6000
        ]);

        // Test it calculates max amount correctly
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
        $this->assertFalse($this->object_service->objectRequirementsMet('robot_factory', $this->planetService, $this->playerService, 2));

        $this->planet->robot_factory = 1;

        // Assert that requirements are met if prior levels exists
        $this->assertTrue($this->object_service->objectRequirementsMet('robot_factory', $this->planetService, $this->playerService, 2));

        // Assert that requirements are not met if requisites are missing
        $this->assertFalse($this->object_service->objectRequirementsMet('missile_silo', $this->planetService, $this->playerService, 1));

        // Add shipyard to build queue
        $queue = new BuildingQueue();
        $queue->planet_id = $this->planetService->getPlanetId();
        $queue->object_id = 21;
        $queue->object_level_target = 1;
        $queue->save();

        // Assert that requirements are met if requisites are in build queue
        $this->assertTrue($this->object_service->objectRequirementsMet('missile_silo', $this->planetService, $this->playerService, 1));

        // Assert that research requirements are not met if building requirements are not met
        $this->assertFalse($this->object_service->objectRequirementsMet('computer_technology', $this->planetService, $this->playerService, 1));
    }
}
