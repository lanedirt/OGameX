<?php

namespace Tests\Feature;

use Exception;
use OGame\Models\BuildingQueue;
use OGame\Models\Resources;
use OGame\Services\SettingsService;
use Tests\AccountTestCase;

/**
 * Test AJAX calls to make sure they work as expected.
 */
class BuildQueueTest extends AccountTestCase
{
    /**
     * Verify that building a metal mine works as expected.
     * @throws Exception
     */
    public function testBuildQueueResourcesMetalMine(): void
    {
        // Set the universe speed to 8x for this test.
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('economy_speed', 8);

        // ---
        // Step 1: Issue a request to build a metal mine
        // ---
        $this->addResourceBuildRequest('metal_mine');

        // ---
        // Step 2: Verify the building is in the build queue
        // ---
        // Check if the building is in the queue and is still level 0.
        $response = $this->get('/resources');
        $this->assertObjectLevelOnPage($response, 'metal_mine', 0, 'Metal mine is not still at level 0 directly after build request issued.');

        // ---
        // Step 3: Verify the building is still in the build queue 2 seconds later.
        // ---
        $this->travel(2)->seconds();

        // Check if the building is still in the queue and is still level 0.
        $response = $this->get('/resources');
        $this->assertObjectLevelOnPage($response, 'metal_mine', 0, 'Metal mine is not still at level 0 directly after build request issued.');

        // ---
        // Step 4: Verify the building is finished 1 minute later.
        // ---
        $this->travel(1)->minutes();

        // Check if the building is finished and is now level 1.
        $response = $this->get('/resources');
        $this->assertObjectLevelOnPage($response, 'metal_mine', 1, 'Metal mine is not at level 1 one minute after build request issued.');
    }

    /**
     * Verify that building a robotics factory on the facilities page works as expected.
     * @throws Exception
     */
    public function testBuildQueueFacilitiesRoboticsFactory(): void
    {
        // Set the universe speed to 8x for this test.
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('economy_speed', 8);

        // Add resources to planet that test requires.
        $this->planetAddResources(new Resources(400, 120, 200, 0));

        // ---
        // Step 1: Issue a request to build a robotics factory.
        // ---
        $this->addFacilitiesBuildRequest('robot_factory');

        // ---
        // Step 2: Verify the building is in the build queue
        // ---
        // Check if the building is in the queue and is still level 0.
        $response = $this->get('/facilities');
        $this->assertObjectLevelOnPage($response, 'robot_factory', 0, 'Robotics factory is not still at level 0 directly after build request issued.');

        // ---
        // Step 3: Verify the building is still in the build queue 2 seconds later.
        // ---
        $this->travel(2)->seconds();

        // Check if the building is still in the queue and is still level 0.
        $response = $this->get('/facilities');
        $this->assertObjectLevelOnPage($response, 'robot_factory', 0, 'Robotics factory is not still at level 0 two seconds after build request issued.');

        // ---
        // Step 4: Verify the building is finished 10 minutes later.
        // ---
        $this->travel(10)->minutes();

        // Check if the building is finished and is now level 1.
        $response = $this->get('/facilities');
        $this->assertObjectLevelOnPage($response, 'robot_factory', 1, 'Robotics factory is not at level 1 ten minutes after build request issued.');
    }

    /**
     * Verify that building a robotics factory on the facilities page works as expected.
     * @throws Exception
     */
    public function testBuildQueueFacilitiesRoboticsFactoryMultiQueue(): void
    {
        // Set the universe speed to 8x for this test.
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('economy_speed', 8);

        // Add resources to planet that test requires.
        $this->planetAddResources(new Resources(5000, 5000, 5000, 0));

        // ---
        // Step 1: Issue a request to build two robotics factory upgrades.
        // ---
        $this->addFacilitiesBuildRequest('robot_factory');
        $this->addFacilitiesBuildRequest('robot_factory');

        // ---
        // Step 2: Verify the building is in the build queue
        // ---
        // Check if the building is in the queue and is still level 0.
        $response = $this->get('/facilities');
        $this->assertObjectLevelOnPage($response, 'robot_factory', 0, 'Robotics factory is not still at level 0 directly after build request issued.');

        // ---
        // Step 3: Verify that one building is finished 30s later.
        // ---
        $this->travel(30)->seconds();

        // Check if the building is finished and is now level 1.
        $response = $this->get('/facilities');
        $this->assertObjectLevelOnPage($response, 'robot_factory', 1, 'Robotics factory is not at level 1 30s after build request issued.');

        // ---
        // Step 3: Verify that both building upgrades are finished 5 minutes later.
        // ---
        $this->travel(5)->minutes();

        // Check if the building is finished and is now level 2.
        $response = $this->get('/facilities');
        $this->assertObjectLevelOnPage($response, 'robot_factory', 2, 'Robotics factory is not at level 2 5m after build request issued.');
    }

    /**
     * Verify that building ships without resources fails.
     * @throws Exception
     */
    public function testBuildQueueFailInsufficientResources(): void
    {
        $this->planetDeductResources(new Resources(500, 500, 0, 0));

        // ---
        // Step 1: Issue a request to build a metal mine.
        // ---
        $this->addResourceBuildRequest('metal_mine');

        // ---
        // Step 2: Verify that nothing has been built as there were not enough resources.
        // ---
        $this->travel(1)->hours();

        $response = $this->get('/resources');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'metal_mine', 0, 'Metal Mine has been built while there were no resources.');
    }

    /**
     * Verify that building a fusion reactor without required technology fails.
     * @throws Exception
     */
    public function testBuildQueueFailUnfulfilledRequirements(): void
    {
        // Set the universe speed to 8x for this test.
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('economy_speed', 8);

        $this->planetAddResources(new Resources(1000, 1000, 1000, 0));

        // ---
        // Step 1: Issue a request to build a fusion reactor.
        // ---
        $this->addResourceBuildRequest('fusion_plant', true);

        // ---
        // Step 2: Verify that nothing has been built as the user does not have the required technology.
        // ---
        $this->travel(1)->hours();

        $response = $this->get('/resources');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'fusion_plant', 0, 'Fusion Reactor has been built while player has not satisfied building requirements.');
    }

    /**
     * Verify that shipyard can be queued when robotics factory is in queue.
     * @throws Exception
     */
    public function testBuildQueueFacilitiesShipyardQueuedRequirements(): void
    {
        // Add resource to build required facilities to planet
        $this->planetAddResources(new Resources(5000, 5000, 5000, 0));

        // Assert that building requirements for Shipyard are not met as Robotics Factory is missing
        $response = $this->get('/facilities');
        $response->assertStatus(200);
        $this->assertRequirementsNotMet($response, 'shipyard', 'Shipyard building requirements not met.');

        // Add Robotics Factory level 1 and 2 to build queue
        $this->addFacilitiesBuildRequest('robot_factory');
        $this->addFacilitiesBuildRequest('robot_factory');

        // Add Shipyard level 1 to queue
        $this->addFacilitiesBuildRequest('shipyard');

        // Verify the research is finished 10 minute later.
        $this->travel(10)->minutes();

        $response = $this->get('/facilities');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'shipyard', 1, 'Shipyard is not at level one 10 minutes after build request issued.');
    }

    /**
     * Verify that building construction time is calculated correctly (higher than 0)
     * @throws Exception
     */
    public function testBuildingProductionTime(): void
    {
        // Set the universe speed to 8x for this test.
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('economy_speed', 8);

        // Add resources to planet to initialize planet.
        $this->planetAddResources(new Resources(400, 120, 200, 0));

        $building_construction_time = $this->planetService->getBuildingConstructionTime('metal_mine');
        $this->assertGreaterThan(0, $building_construction_time);
    }

    /**
     * Verify that with a very high economy speed, the building construction time is at least 1 second which is
     * the minimum for all objects in the game.
     * @throws Exception
     */
    public function testBuildingProductionTimeHighSpeed(): void
    {
        // Set the universe speed to 8x for this test.
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('economy_speed', 8);

        // Set robot factory to level 99 to get very fast building construction times.
        $this->planetSetObjectLevel('robot_factory', 99);

        // Check that the building construction time is at least 1 second even with such a high robot factory.
        $building_construction_time = $this->planetService->getBuildingConstructionTime('metal_mine');
        $this->assertEquals(1, $building_construction_time);
    }

    /**
     * Verify that ongoing researching prevents upgrade of research lab.
     * @throws Exception
     */
    public function testResearchingPreventsResearchLabUpgrading(): void
    {
        // Add required resources for research to planet
        $this->planetAddResources(new Resources(5000, 5000, 5000, 0));
        $this->planetSetObjectLevel('research_lab', 1);

        // Add Energy Technology to research queue
        $this->addResearchBuildRequest('energy_technology');
        $response = $this->get('/research');
        $response->assertStatus(200);
        $this->assertObjectInQueue($response, 'energy_technology', 1, 'Energy Technology level 1 is not in research queue');

        $this->addFacilitiesBuildRequest('research_lab');

        // Verify that Research Lab is not in build queue
        $response = $this->get('/facilities');
        $response->assertStatus(200);
        $this->assertObjectNotInQueue($response, 'research_lab', 'Research lab is in build queue but should not be added.');
    }

    /**
     * Tests object building queue status.
     */
    public function testIsBuildingObject(): void
    {
        // Add level 3 shipyard to building queue
        $queue = new BuildingQueue();
        $queue->planet_id = $this->planetService->getPlanetId();
        $queue->object_id = 21;
        $queue->object_level_target = 3;
        $queue->save();

        $this->assertTrue($this->planetService->isBuildingObject('shipyard', 3));
    }

    /**
     * Verify that building a high level metal mine with very long construction time works.
     * This is to test that the database can handle very large numbers for resources and construction time.
     */
    public function testBuildQueueHighLevelMetalMine(): void
    {
        // Set the universe speed to 8x for this test.
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('economy_speed', 8);

        // Set metal mine to level 50
        $this->planetSetObjectLevel('metal_mine', 50);

        // Add massive amount of resources to planet for the upgrade
        $this->planetAddResources(new Resources(40000000000, 10000000000, 0, 0));

        // ---
        // Step 1: Issue a request to upgrade metal mine to level 51
        // ---
        $this->addResourceBuildRequest('metal_mine');

        // ---
        // Step 2: Verify the building is in the build queue
        // ---
        $response = $this->get('/resources');
        $this->assertObjectLevelOnPage($response, 'metal_mine', 50, 'Metal mine is not at level 50 directly after build request issued.');
        $this->assertObjectInQueue($response, 'metal_mine', 51, 'Metal mine level 51 is not in build queue.');

        // ---
        // Step 3: Verify the building is finished after 15,000 weeks
        // ---
        $this->travel(15000)->weeks();

        // Check if the building is finished and is now level 51
        $response = $this->get('/resources');
        $this->assertObjectLevelOnPage($response, 'metal_mine', 51, 'Metal mine is not at level 51 after construction time has passed.');
    }
}
