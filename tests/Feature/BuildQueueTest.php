<?php

namespace Feature;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Carbon;
use OGame\Models\Resources;
use Tests\AccountTestCase;

/**
 * Test AJAX calls to make sure they work as expected.
 */
class BuildQueueTest extends AccountTestCase
{
    /**
     * Verify that building a metal mine works as expected.
     */
    public function testBuildQueueResourcesMetalMine(): void
    {
        // Set the current time to a specific moment for testing
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 0);
        Carbon::setTestNow($testTime);

        // ---
        // Step 1: Issue a request to build a metal mine
        // ---
        $response = $this->post('/resources/add-buildrequest', [
            '_token' => csrf_token(),
            'type' => '1', // Metal mine
            'planet_id' => $this->currentPlanetId,
        ]);

        // Assert the response status is successful (302 redirect).
        $response->assertStatus(302);

        // ---
        // Step 2: Verify the building is in the build queue
        // ---
        // Check if the building is in the queue and is still level 0.
        $response = $this->get('/resources');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'metal_mine', 0, 'Metal mine is not still at level 0 directly after build request issued.');

        // ---
        // Step 3: Verify the building is still in the build queue 2 seconds later.
        // ---
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 2);
        Carbon::setTestNow($testTime);

        // Check if the building is still in the queue and is still level 0.
        $response = $this->get('/resources');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'metal_mine', 0, 'Metal mine is not still at level 0 directly after build request issued.');

        // ---
        // Step 4: Verify the building is finished 1 minute later.
        // ---
        $testTime = Carbon::create(2024, 1, 1, 12, 1, 0);
        Carbon::setTestNow($testTime);

        // Check if the building is finished and is now level 1.
        $response = $this->get('/resources');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'metal_mine', 1, 'Metal mine is not at level 1 one minute after build request issued.');
    }

    /**
     * Verify that building a robotics factory on the facilities page works as expected.
     * @throws BindingResolutionException
     */
    public function testBuildQueueFacilitiesRoboticsFactory(): void
    {
        // Add resources to planet that test requires.
        $this->planetAddResources(new Resources(400,120,200,0));

        // Set the current time to a specific moment for testing
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 0);
        Carbon::setTestNow($testTime);

        // ---
        // Step 1: Issue a request to build a robotics factory.
        // ---
        $response = $this->post('/facilities/add-buildrequest', [
            '_token' => csrf_token(),
            'type' => '14', // Robotics factory
            'planet_id' => $this->currentPlanetId,
        ]);

        // Assert the response status is successful (302 redirect).
        $response->assertStatus(302);

        // ---
        // Step 2: Verify the building is in the build queue
        // ---
        // Check if the building is in the queue and is still level 0.
        $response = $this->get('/facilities');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'robot_factory', 0, 'Robotics factory is not still at level 0 directly after build request issued.');

        // ---
        // Step 3: Verify the building is still in the build queue 2 seconds later.
        // ---
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 2);
        Carbon::setTestNow($testTime);

        // Check if the building is still in the queue and is still level 0.
        $response = $this->get('/facilities');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'robot_factory', 0, 'Robotics factory is not still at level 0 two seconds after build request issued.');

        // ---
        // Step 4: Verify the building is finished 10 minutes later.
        // ---
        $testTime = Carbon::create(2024, 1, 1, 12, 10, 0);
        Carbon::setTestNow($testTime);

        // Check if the building is finished and is now level 1.
        $response = $this->get('/facilities');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'robot_factory', 1, 'Robotics factory is not at level 1 ten minutes after build request issued.');
    }

    /**
     * Verify that building a robotics factory on the facilities page works as expected.
     * @throws BindingResolutionException
     */
    public function testBuildQueueFacilitiesRoboticsFactoryMultiQueue(): void
    {
        // Add resources to planet that test requires.
        $this->planetAddResources(new Resources(5000,5000,5000,0));

        // Set the current time to a specific moment for testing
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 0);
        Carbon::setTestNow($testTime);

        // ---
        // Step 1: Issue a request to build two robotics factory upgrades.
        // ---
        $response = $this->post('/facilities/add-buildrequest', [
            '_token' => csrf_token(),
            'type' => '14', // Robotics factory
            'planet_id' => $this->currentPlanetId,
        ]);
        $response->assertStatus(302);
        $response = $this->post('/facilities/add-buildrequest', [
            '_token' => csrf_token(),
            'type' => '14', // Robotics factory
            'planet_id' => $this->currentPlanetId,
        ]);
        $response->assertStatus(302);

        // ---
        // Step 2: Verify the building is in the build queue
        // ---
        // Check if the building is in the queue and is still level 0.
        $response = $this->get('/facilities');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'robot_factory', 0, 'Robotics factory is not still at level 0 directly after build request issued.');

        // ---
        // Step 3: Verify that one building is finished 30s later.
        // ---
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 30);
        Carbon::setTestNow($testTime);

        // Check if the building is finished and is now level 1.
        $response = $this->get('/facilities');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'robot_factory', 1, 'Robotics factory is not at level 1 30s after build request issued.');

        // ---
        // Step 3: Verify that both building upgrades are finished 5 minutes later.
        // ---
        $testTime = Carbon::create(2024, 1, 1, 12, 5, 0);
        Carbon::setTestNow($testTime);

        // Check if the building is finished and is now level 2.
        $response = $this->get('/facilities');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'robot_factory', 2, 'Robotics factory is not at level 2 5m after build request issued.');
    }

    /**
     * Verify that building a metal mine with fastbuild (get request) as expected.
     */
    public function testBuildQueueResourcesMetalMineFastBuild(): void
    {
        // Set the current time to a specific moment for testing
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 0);
        Carbon::setTestNow($testTime);

        // ---
        // Step 1: Issue a request to build a metal mine
        // ---
        $response = $this->get('/resources/add-buildrequest?_token=' . csrf_token() . '&type=1&planet_id=' . $this->currentPlanetId);

        // Assert the response status is successful (302 redirect).
        $response->assertStatus(302);

        // ---
        // Step 2: Verify the building is in the build queue
        // ---
        // Check if the building is in the queue and is still level 0.
        $response = $this->get('/resources');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'metal_mine', 0, 'Metal mine is not still at level 0 directly after build request issued.');

        // ---
        // Step 3: Verify the building is still in the build queue 2 seconds later.
        // ---
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 2);
        Carbon::setTestNow($testTime);

        // Check if the building is still in the queue and is still level 0.
        $response = $this->get('/resources');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'metal_mine', 0, 'Metal mine is not still at level 0 two seconds after build request issued.');

        // ---
        // Step 4: Verify the building is finished 1 minute later.
        // ---
        $testTime = Carbon::create(2024, 1, 1, 12, 1, 0);
        Carbon::setTestNow($testTime);

        // Check if the building is finished and is now level 1.
        $response = $this->get('/resources');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'metal_mine', 1, 'Metal mine is not at level 1 one minute after build request issued.');
    }

    /**
     * Verify that building on a non-existent planet fails.
     */
    public function testBuildQueueNonExistentPlanet(): void
    {
        // Set the current time to a specific moment for testing
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 0);
        Carbon::setTestNow($testTime);

        // ---
        // Step 1: Issue a request to build a metal mine on planet not owned by player.
        // ---
        $response = $this->get('/resources/add-buildrequest', [
            '_token' => csrf_token(),
            'type' => '1', // Metal mine
            'planet_id' => $this->currentPlanetId - 1,
        ]);

        // Assert the response status returns an error (500).
        $response->assertStatus(500);
    }

    /**
     * Verify that building ships without resources fails.
     * @throws BindingResolutionException
     */
    public function testBuildQueueFailInsufficientResources(): void
    {
        $this->planetDeductResources(new Resources(500, 500, 0, 0));

        // Set the current time to a specific moment for testing
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 0);
        Carbon::setTestNow($testTime);

        // ---
        // Step 1: Issue a request to build a metal mine.
        // ---
        $response = $this->post('/resources/add-buildrequest', [
            '_token' => csrf_token(),
            'type' => '1', // Metal mine
            'planet_id' => $this->currentPlanetId,
        ]);

        // Assert the response status is successful (302 redirect).
        $response->assertStatus(302);

        // ---
        // Step 2: Verify that nothing has been built as there were not enough resources.
        // ---
        $testTime = Carbon::create(2024, 1, 1, 13, 0, 0);
        Carbon::setTestNow($testTime);

        $response = $this->get('/resources');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'metal_mine', 0, 'Metal Mine has been built while there were no resources.');
    }

    /**
     * Verify that building a fusion reactor without required technology fails.
     * @throws BindingResolutionException
     */
    public function testBuildQueueFailUnfulfilledRequirements(): void
    {
        $this->planetAddResources(new Resources(1000,1000,1000, 0));

        // Set the current time to a specific moment for testing
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 0);
        Carbon::setTestNow($testTime);

        // ---
        // Step 1: Issue a request to build a metal mine.
        // ---
        $this->post('/resources/add-buildrequest', [
            '_token' => csrf_token(),
            'type' => '12', // Fusion reactor
            'planet_id' => $this->currentPlanetId,
        ]);

        // ---
        // Step 2: Verify that nothing has been built as the user does not have the required technology.
        // ---
        $testTime = Carbon::create(2024, 1, 1, 13, 0, 0);
        Carbon::setTestNow($testTime);

        $response = $this->get('/resources');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'fusion_plant', 0, 'Fusion Reactor has been built while player has not satisfied building requirements.');
    }

    /**
     * Verify that building construction time is calculated correctly (higher than 0)
     * @throws Exception
     */
    public function testBuildingProductionTime(): void
    {
        // Add resources to planet to initialize planet.
        $this->planetAddResources(new Resources(400,120,200,0));

        $building_construction_time = $this->planetService->getBuildingConstructionTime('metal_mine');
        $this->assertGreaterThan(0, $building_construction_time);
    }
}
