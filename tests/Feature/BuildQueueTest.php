<?php

namespace Feature;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Carbon;
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
            'token' => csrf_token(),
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
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Metal\s+Mine\s*<\/span>\s*0\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Metal mine is not still at level 0 directly after build request issued.');

        // ---
        // Step 3: Verify the building is still in the build queue 2 seconds later.
        // ---
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 2);
        Carbon::setTestNow($testTime);

        // Check if the building is still in the queue and is still level 0.
        $response = $this->get('/resources');
        $response->assertStatus(200);
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Metal\s+Mine\s*<\/span>\s*0\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Metal mine is not still at level 0 two seconds after build request issued.');

        // ---
        // Step 4: Verify the building is finished 1 minute later.
        // ---
        $testTime = Carbon::create(2024, 1, 1, 12, 1, 0);
        Carbon::setTestNow($testTime);

        // Check if the building is finished and is now level 1.
        $response = $this->get('/resources');
        $response->assertStatus(200);
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Metal\s+Mine\s*<\/span>\s*1\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Metal mine is not at level 1 one minute after build request issued.');
    }

    /**
     * Verify that building a robotics factory on the facilities page works as expected.
     * @throws BindingResolutionException
     */
    public function testBuildQueueFacilitiesRoboticsFactory(): void
    {
        // Add resources to planet that test requires.
        $this->planetAddResources(['metal' => 400, 'crystal' => 120, 'deuterium' => 200]);

        // Set the current time to a specific moment for testing
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 0);
        Carbon::setTestNow($testTime);

        // ---
        // Step 1: Issue a request to build a robotics factory.
        // ---
        $response = $this->post('/facilities/add-buildrequest', [
            'token' => csrf_token(),
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
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Robotics\s+Factory\s*<\/span>\s*0\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Robotics factory is not still at level 0 directly after build request issued.');

        // ---
        // Step 3: Verify the building is still in the build queue 2 seconds later.
        // ---
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 2);
        Carbon::setTestNow($testTime);

        // Check if the building is still in the queue and is still level 0.
        $response = $this->get('/facilities');
        $response->assertStatus(200);
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Robotics\s+Factory\s*<\/span>\s*0\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Robotics factory is not still at level 0 two seconds after build request issued.');

        // ---
        // Step 4: Verify the building is finished 10 minutes later.
        // ---
        $testTime = Carbon::create(2024, 1, 1, 12, 10, 0);
        Carbon::setTestNow($testTime);

        // Check if the building is finished and is now level 1.
        $response = $this->get('/facilities');
        $response->assertStatus(200);
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Robotics\s+Factory\s*<\/span>\s*1\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Robotics factory is not at level 1 ten minutes after build request issued.');
    }

    /**
     * Verify that building a robotics factory on the facilities page works as expected.
     * @throws BindingResolutionException
     */
    public function testBuildQueueFacilitiesRoboticsFactoryMultiQueue(): void
    {
        // Add resources to planet that test requires.
        $this->planetAddResources(['metal' => 5000, 'crystal' => 5000, 'deuterium' => 5000]);

        // Set the current time to a specific moment for testing
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 0);
        Carbon::setTestNow($testTime);

        // ---
        // Step 1: Issue a request to build two robotics factory upgrades.
        // ---
        $response = $this->post('/facilities/add-buildrequest', [
            'token' => csrf_token(),
            'type' => '14', // Robotics factory
            'planet_id' => $this->currentPlanetId,
        ]);
        $response->assertStatus(302);
        $response = $this->post('/facilities/add-buildrequest', [
            'token' => csrf_token(),
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
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Robotics\s+Factory\s*<\/span>\s*0\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Robotics factory is not still at level 0 directly after build request issued.');

        // ---
        // Step 3: Verify that one building is finished 4 minutes later.
        // ---
        $testTime = Carbon::create(2024, 1, 1, 12, 4, 0);
        Carbon::setTestNow($testTime);

        // Check if the building is finished and is now level 1.
        $response = $this->get('/facilities');
        $response->assertStatus(200);
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Robotics\s+Factory\s*<\/span>\s*1\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Robotics factory is not at level 1 ten minutes after build request issued.');

        // ---
        // Step 3: Verify that both building upgrades are finished 15 minutes later.
        // ---
        $testTime = Carbon::create(2024, 1, 1, 12, 15, 0);
        Carbon::setTestNow($testTime);

        // Check if the building is finished and is now level 2.
        $response = $this->get('/facilities');
        $response->assertStatus(200);
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Robotics\s+Factory\s*<\/span>\s*2\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Robotics factory is not at level 2 fifteen minutes after build request issued.');
    }
}
