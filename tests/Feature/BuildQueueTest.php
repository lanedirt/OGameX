<?php

namespace Tests\Feature;

use Exception;
use OGame\Models\BuildingQueue;
use OGame\Models\Resources;
use OGame\Services\ObjectService;
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

    /**
     * Verify that downgrading a building works as expected.
     * @throws Exception
     */
    public function testDowngradeBuildingSuccess(): void
    {
        // Set the universe speed to 8x for this test.
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('economy_speed', 8);

        // Set building to level 5 and add enough resources for downgrade
        $this->planetSetObjectLevel('metal_mine', 5);
        $downgrade_price = ObjectService::getObjectDowngradePrice('metal_mine', $this->planetService);
        $this->planetAddResources(new Resources(
            $downgrade_price->metal->get() + 10000,
            $downgrade_price->crystal->get() + 10000,
            $downgrade_price->deuterium->get() + 10000,
            0
        ));

        $initial_metal = $this->planetService->metal()->get();
        $initial_crystal = $this->planetService->crystal()->get();
        $initial_deuterium = $this->planetService->deuterium()->get();

        // ---
        // Step 1: Issue a request to downgrade metal mine
        // ---
        $object = ObjectService::getObjectByMachineName('metal_mine');
        $response = $this->post('/resources/downgrade', [
            '_token' => csrf_token(),
            'technologyId' => $object->id,
        ]);
        $response->assertStatus(200);

        // ---
        // Step 2: Verify resources were deducted immediately after request
        // ---
        $this->reloadApplication();
        // Resources should be deducted when the downgrade starts
        $this->planetService->reloadPlanet();
        $actual_metal = $this->planetService->metal()->get();
        $actual_crystal = $this->planetService->crystal()->get();
        $actual_deuterium = $this->planetService->deuterium()->get();

        // Calculate expected resources (accounting for any resource production that may have occurred)
        $expected_metal = $initial_metal - $downgrade_price->metal->get();
        $expected_crystal = $initial_crystal - $downgrade_price->crystal->get();
        $expected_deuterium = $initial_deuterium - $downgrade_price->deuterium->get();

        // Allow small tolerance for resource production (should be minimal since we just made the request)
        $this->assertLessThanOrEqual($expected_metal + 100, $actual_metal, 'Metal should be deducted (allowing for minimal production)');
        $this->assertGreaterThanOrEqual($expected_metal, $actual_metal, 'Metal should not be higher than expected');
        $this->assertLessThanOrEqual($expected_crystal + 100, $actual_crystal, 'Crystal should be deducted (allowing for minimal production)');
        $this->assertGreaterThanOrEqual($expected_crystal, $actual_crystal, 'Crystal should not be higher than expected');
        $this->assertLessThanOrEqual($expected_deuterium + 100, $actual_deuterium, 'Deuterium should be deducted (allowing for minimal production)');
        $this->assertGreaterThanOrEqual($expected_deuterium, $actual_deuterium, 'Deuterium should not be higher than expected');

        // ---
        // Step 3: Verify the building is still at level 5 (downgrade in progress)
        // ---
        $response = $this->get('/resources');
        $this->assertObjectLevelOnPage($response, 'metal_mine', 5, 'Metal mine should still be at level 5 while downgrade is in progress.');

        // ---
        // Step 3: Travel forward in time to complete downgrade
        // ---
        $downgrade_time = $this->planetService->getBuildingDowngradeTime('metal_mine');
        $this->travel($downgrade_time + 1)->seconds();

        // ---
        // Step 4: Verify the building is now at level 4
        // ---
        $response = $this->get('/resources');
        $this->assertObjectLevelOnPage($response, 'metal_mine', 4, 'Metal mine should be at level 4 after downgrade completes.');
    }

    /**
     * Verify that downgrading fails when building is at level 0.
     * @throws Exception
     */
    public function testDowngradeBuildingLevelZero(): void
    {
        // Building is at level 0 by default
        $object = ObjectService::getObjectByMachineName('metal_mine');
        $response = $this->post('/resources/downgrade', [
            '_token' => csrf_token(),
            'technologyId' => $object->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
        ]);
    }

    /**
     * Verify that downgrading with Ion technology bonus reduces cost.
     * @throws Exception
     */
    public function testDowngradeWithIonTechnologyBonus(): void
    {
        // Set the universe speed to 8x for this test.
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('economy_speed', 8);

        // Set building to level 5
        $this->planetSetObjectLevel('metal_mine', 5);

        // Set Ion technology to level 6 (6 * 4% = 24% reduction)
        $this->playerSetResearchLevel('ion_technology', 6);

        // Get downgrade price with bonus
        $downgrade_price = ObjectService::getObjectDowngradePrice('metal_mine', $this->planetService);

        // Get construction cost to calculate expected downgrade cost
        $construction_cost = ObjectService::getObjectRawPrice('metal_mine', 5);
        // Ion technology level 6 = 6 * 4% = 24% reduction
        $ion_bonus = 6 * 0.04; // 0.24 = 24%
        $expected_metal = floor($construction_cost->metal->get() * (1 - $ion_bonus));

        // Verify the cost is reduced by Ion technology bonus
        $this->assertEquals($expected_metal, $downgrade_price->metal->get());
    }

    /**
     * Test resource production with multiple buildings in queue (issue #931).
     * Resources should be calculated in segments with correct production rates.
     *
     * @throws Exception
     */
    public function testResourceProductionWithMultipleBuildingsInQueue(): void
    {
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('economy_speed', 8);

        // Setup: metal mine level 5 with energy
        $this->planetSetObjectLevel('metal_mine', 5);
        $this->planetSetObjectLevel('solar_plant', 10);
        $this->planetAddResources(new Resources(10000, 10000, 0, 0));
        $this->planetService->updateResourceProductionStats();

        $production_level_5 = $this->planetService->getMetalProductionPerHour();
        $this->assertGreaterThan(0, $production_level_5);

        // Reset resources
        $this->planetService->deductResources($this->planetService->getResources());
        $this->planetAddResources(new Resources(10000, 10000, 0, 0));
        $starting_metal = $this->planetService->metal()->get();

        // Queue metal mine level 6 and 7
        $this->addResourceBuildRequest('metal_mine');
        $build_time_level_6 = $this->planetService->getBuildingConstructionTime('metal_mine');
        $this->addResourceBuildRequest('metal_mine');

        // Travel past level 6 completion
        $this->travel($build_time_level_6 + 1)->seconds();
        $this->reloadApplication();
        $response = $this->get('/resources');
        $this->assertObjectLevelOnPage($response, 'metal_mine', 6);

        $this->planetService->reloadPlanet();
        $production_level_6 = $this->planetService->getMetalProductionPerHour();
        $this->assertGreaterThanOrEqual($production_level_5, $production_level_6);

        // Travel past level 7 completion
        $build_time_level_7 = $this->planetService->getBuildingConstructionTime('metal_mine');
        $this->travel($build_time_level_7 + 1)->seconds();
        $this->reloadApplication();
        $response = $this->get('/resources');
        $this->assertObjectLevelOnPage($response, 'metal_mine', 7);

        $this->planetService->reloadPlanet();
        $production_level_7 = $this->planetService->getMetalProductionPerHour();
        $this->assertGreaterThanOrEqual($production_level_6, $production_level_7);

        // Verify resources were produced during build time
        $current_metal = $this->planetService->metal()->get();
        $total_build_time_hours = ($build_time_level_6 + $build_time_level_7 + 2) / 3600;
        $minimum_expected_production = $production_level_5 * $total_build_time_hours * 0.5;

        $level_6_cost = ObjectService::getObjectRawPrice('metal_mine', 6);
        $level_7_cost = ObjectService::getObjectRawPrice('metal_mine', 7);
        $total_cost = $level_6_cost->metal->get() + $level_7_cost->metal->get();
        $expected_minimum_metal = $starting_metal - $total_cost + $minimum_expected_production;

        $this->assertGreaterThan($expected_minimum_metal, $current_metal,
            "Starting: $starting_metal, Cost: $total_cost, Min production: $minimum_expected_production, Current: $current_metal"
        );
    }

    /**
     * Test bulk processing of multiple buildings when player returns after long offline (issue #931).
     *
     * @throws Exception
     */
    public function testResourceProductionBulkProcessingMultipleBuildings(): void
    {
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('economy_speed', 8);

        // Setup: metal mine level 3 with energy and storage
        $this->planetSetObjectLevel('metal_mine', 3);
        $this->planetSetObjectLevel('solar_plant', 10);
        $this->planetSetObjectLevel('metal_store', 5);
        $this->planetService->updateResourceStorageStats();
        $this->planetService->save();

        $this->planetAddResources(new Resources(5000, 5000, 0, 0));
        $this->planetService->updateResourceProductionStats();
        $this->planetService->save();

        $initial_production = $this->planetService->getMetalProductionPerHour();
        $this->assertGreaterThan(0, $initial_production);

        $starting_metal = $this->planetService->metal()->get();
        $planet_id = $this->planetService->getPlanetId();

        // Queue metal mine level 4 and 5
        $this->addResourceBuildRequest('metal_mine');
        $this->addResourceBuildRequest('metal_mine');

        // Simulate long offline period - both buildings should complete
        $this->travel(2)->hours();
        $this->reloadApplication();
        $response = $this->get('/resources');
        $this->assertObjectLevelOnPage($response, 'metal_mine', 5);

        // Verify resources were produced
        $planet_data = \DB::table('planets')->where('id', $planet_id)->first();
        $current_metal_from_db = $planet_data->metal;

        $level_4_cost = ObjectService::getObjectRawPrice('metal_mine', 4);
        $level_5_cost = ObjectService::getObjectRawPrice('metal_mine', 5);
        $total_cost = $level_4_cost->metal->get() + $level_5_cost->metal->get();
        $minimum_expected = $starting_metal - $total_cost;

        $this->assertGreaterThan($minimum_expected, $current_metal_from_db,
            "Starting: $starting_metal, Cost: $total_cost, Min: $minimum_expected, Current: $current_metal_from_db"
        );
    }

    /**
     * Test exact scenario: solar plant completion enables production.
     * When solar plant completes, energy increases, enabling mine production.
     *
     * @throws Exception
     */
    public function testSolarPlantCompletionEnablesProduction(): void
    {
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('economy_speed', 8);

        // Setup: metal mine level 10, but solar plant only level 1 (not enough energy)
        $this->planetSetObjectLevel('metal_mine', 10);
        $this->planetSetObjectLevel('solar_plant', 1);
        $this->planetSetObjectLevel('metal_store', 5);
        $this->planetService->updateResourceStorageStats();
        $this->planetService->updateResourceProductionStats();
        $this->planetService->save();

        // With insufficient energy, production should be reduced or zero
        $production_before = $this->planetService->getMetalProductionPerHour();

        // Add resources and queue solar plant upgrade to level 10 (enough energy)
        $this->planetAddResources(new Resources(50000, 50000, 0, 0));
        $starting_metal = $this->planetService->metal()->get();

        // Queue solar plant upgrade
        $this->addResourceBuildRequest('solar_plant');
        $build_time = $this->planetService->getBuildingConstructionTime('solar_plant');

        // Travel past solar plant completion + extra time for production
        $extra_production_time = 3600; // 1 hour extra
        $this->travel($build_time + $extra_production_time)->seconds();

        $this->reloadApplication();
        $response = $this->get('/resources');
        $this->assertObjectLevelOnPage($response, 'solar_plant', 2);

        // After solar plant upgrade, production should be higher
        $this->planetService->reloadPlanet();
        $production_after = $this->planetService->getMetalProductionPerHour();

        // Key assertion: resources should have been produced during the extra hour
        // with the NEW production rate (after solar plant completed)
        $current_metal = $this->planetService->metal()->get();
        $solar_plant_cost = ObjectService::getObjectRawPrice('solar_plant', 2);

        // We should have: starting - cost + (production_after * 1 hour)
        // At minimum, we should have more than starting - cost
        $minimum_expected = $starting_metal - $solar_plant_cost->metal->get();

        $this->assertGreaterThan($minimum_expected, $current_metal,
            "Production should occur after solar plant completes. " .
            "Prod before: $production_before, Prod after: $production_after, " .
            "Starting: $starting_metal, Current: $current_metal"
        );
    }
}

