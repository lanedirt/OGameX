<?php

namespace Feature;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Carbon;
use OGame\Models\Resources;
use Tests\AccountTestCase;

/**
 * Test that the unit queue works as expected.
 */
class UnitQueueTest extends AccountTestCase
{
    /**
     * Prepare the planet for the test so it has the required buildings and research.
     *
     * @return void
     * @throws BindingResolutionException
     */
    private function basicSetup(): void
    {
        // Set the robotics factory to level 2
        $this->planetSetObjectLevel('robot_factory', 2);
        // Set shipyard to level 1.
        $this->planetSetObjectLevel('shipyard', 1);
        // Set the research lab to level 1.
        $this->planetSetObjectLevel('research_lab', 1);
        // Set energy technology to level 1.
        $this->playerSetResearchLevel('energy_technology', 1);
        // Set combustion drive to level 1.
        $this->playerSetResearchLevel('combustion_drive', 1);
    }

    /**
     * Verify that building more than one of a ship works as expected.
     * @throws BindingResolutionException
     */
    public function testUnitQueueShips(): void
    {
        $this->basicSetup();
        // Add resources to planet that test requires.
        $this->planetAddResources(new Resources(30000,10000, 0,0));

        // Set the current time to a specific moment for testing
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 0);
        Carbon::setTestNow($testTime);

        // ---
        // Step 1: Issue a request to build 10 light fighters
        // ---
        $response = $this->post('/shipyard/add-buildrequest', [
            '_token' => csrf_token(),
            'type' => '204', // Light fighter
            'amount' => 10,
            'planet_id' => $this->currentPlanetId,
        ]);

        // Assert the response status is successful (302 redirect).
        $response->assertStatus(302);

        // ---
        // Step 2: Verify the ships are in the build queue
        // ---
        $response = $this->get('/shipyard');
        $response->assertStatus(200);
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Light\s*Fighter\s*<\/span>\s*0\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Light Fighter is not at 0 units directly after build request issued.');

        // ---
        // Step 3: Verify the ships are still in the build queue 1 minute later.
        // ---
        $testTime = Carbon::create(2024, 1, 1, 12, 1, 0);
        Carbon::setTestNow($testTime);

        $response = $this->get('/shipyard');
        $response->assertStatus(200);
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Light\s*Fighter\s*<\/span>\s*0\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Light Fighter is not at 0 units 1m after build request issued.');

        // ---
        // Step 4: Verify that some ships are finished 30 minute later.
        // ---
        $testTime = Carbon::create(2024, 1, 1, 12, 20, 0);
        Carbon::setTestNow($testTime);

        $response = $this->get('/shipyard');
        $response->assertStatus(200);
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Light\sFighter\s*<\/span>\s*3\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Light Fighter build job has not completed exactly 3 units 15m after build request issued.');

        // ---
        // Step 5: Verify that ALL ships are finished 15 minute later.
        // ---
        $testTime = Carbon::create(2024, 1, 1, 14, 0, 0);
        Carbon::setTestNow($testTime);

        $response = $this->get('/shipyard');
        $response->assertStatus(200);
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Light\sFighter\s*<\/span>\s*10\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Light Fighter build job is not finished yet 2h after build request issued.');
    }

    /**
     * Verify that adding three different build jobs and waiting for them all to complete works as expected.
     * @throws BindingResolutionException
     */
    public function testUnitQueueShipsMultiQueues(): void
    {
        $this->basicSetup();

        // Add more specific resources to planet that test requires.
        // For 5 light fighters
        $this->planetAddResources(new Resources(15000,5000,0,0));
        // For 10 solar satellites
        $this->planetAddResources(new Resources(0,20000,50000,0));

        // Set the current time to a specific moment for testing
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 0);
        Carbon::setTestNow($testTime);

        // ---
        // Step 1: Issue a request to build 3 light fighters, 10 solar sats, and then 2 light fighters
        // ---
        $response = $this->post('/shipyard/add-buildrequest', [
            '_token' => csrf_token(),
            'type' => '204', // Light fighter
            'amount' => 3,
            'planet_id' => $this->currentPlanetId,
        ]);
        // Assert the response status is successful (302 redirect).
        $response->assertStatus(302);

        $response = $this->post('/shipyard/add-buildrequest', [
            '_token' => csrf_token(),
            'type' => '212', // Solar satellites
            'amount' => 10,
            'planet_id' => $this->currentPlanetId,
        ]);
        // Assert the response status is successful (302 redirect).
        $response->assertStatus(302);

        $response = $this->post('/shipyard/add-buildrequest', [
            '_token' => csrf_token(),
            'type' => '204', // Light fighter
            'amount' => 2,
            'planet_id' => $this->currentPlanetId,
        ]);
        // Assert the response status is successful (302 redirect).
        $response->assertStatus(302);

        // ---
        // Step 2: Verify the ships are in the build queue
        // ---
        $response = $this->get('/shipyard');
        $response->assertStatus(200);
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Light\s*Fighter\s*<\/span>\s*0\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Light Fighter is not at 0 units directly after build request issued.');
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Solar\s*Satellite\s*<\/span>\s*0\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Solar Satellite is not at 0 units directly after build request issued.');

        // ---
        // Step 3: Verify that the light fighters and partial solar satellites are finished 30 minute later.
        // ---
        $testTime = Carbon::create(2024, 1, 1, 12, 25, 0);
        Carbon::setTestNow($testTime);

        $response = $this->get('/shipyard');
        $response->assertStatus(200);
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Light\s*Fighter\s*<\/span>\s*3\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Light Fighter is not at 3 units 25m after build request issued.');
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Solar\s*Satellite\s*<\/span>\s*2\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Solar Satellite is not at 2 units 25m after build request issued.');

        // ---
        // Step 5: Verify that ALL ships are finished 30 minute later.
        // ---
        $testTime = Carbon::create(2024, 1, 1, 14, 0, 0);
        Carbon::setTestNow($testTime);

        $response = $this->get('/shipyard');
        $response->assertStatus(200);
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Light\sFighter\s*<\/span>\s*5\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Light Fighter build job is not finished 2h after build request issued.');
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Solar\s*Satellite\s*<\/span>\s*10\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Solar Satellite build job is not finished 2h after build request issued.');
    }

    /**
     * Verify that building more than one of a defense unit works as expected.
     * @throws BindingResolutionException
     */
    public function testUnitQueueDefense(): void
    {
        $this->basicSetup();

        // Add resources to planet that test requires.
        $this->planetAddResources(new Resources(20000,0,0,0));
        // Set the robotics factory to level 2
        $this->planetSetObjectLevel('robot_factory', 2);
        // Set shipyard to level 1.
        $this->planetSetObjectLevel('shipyard', 1);

        // Set the current time to a specific moment for testing
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 0);
        Carbon::setTestNow($testTime);

        // ---
        // Step 1: Issue a request to build 10 light fighters
        // ---
        $response = $this->post('/defense/add-buildrequest', [
            '_token' => csrf_token(),
            'type' => '401', // Rocket launcher
            'amount' => 10,
            'planet_id' => $this->currentPlanetId,
        ]);
        $response->assertStatus(302);

        // ---
        // Step 2: Verify the defense units are in the build queue
        // ---
        $response = $this->get('/defense');
        $response->assertStatus(200);
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Rocket\s*Launcher\s*<\/span>\s*0\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Rocket Launcher is not at 0 units directly after build request issued.');

        // ---
        // Step 3: Verify the defense units are still in the build queue 1 minute later.
        // ---
        $testTime = Carbon::create(2024, 1, 1, 12, 1, 0);
        Carbon::setTestNow($testTime);

        $response = $this->get('/defense');
        $response->assertStatus(200);
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Rocket\s*Launcher\s*<\/span>\s*0\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Rocket Launcher is not at 0 units 30 seconds after build request issued.');

        // ---
        // Step 4: Verify that some defense units are finished 10 minute later.
        // ---
        $testTime = Carbon::create(2024, 1, 1, 12, 10, 0);
        Carbon::setTestNow($testTime);

        $response = $this->get('/defense');
        $response->assertStatus(200);
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Rocket\sLauncher\s*<\/span>\s*3\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Rocket Launcher build job has not completed exactly 3 units 10 minutes after build request issued.');

        // ---
        // Step 5: Verify that ALL defense units are finished 1h later.
        // ---
        $testTime = Carbon::create(2024, 1, 1, 13, 0, 0);
        Carbon::setTestNow($testTime);

        $response = $this->get('/defense');
        $response->assertStatus(200);
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Rocket\sLauncher\s*<\/span>\s*10\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Rocket Launcher build job is not finished yet 1h after build request issued.');
    }

    /**
     * Verify that building ships on a planet not owned by the player fails.
     * @throws BindingResolutionException
     */
    public function testUnitQueueNonExistentPlanet(): void
    {
        $this->basicSetup();
        // Add resources to planet that test requires.
        $this->planetAddResources(new Resources(30000,10000,0,0));

        // Set the current time to a specific moment for testing
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 0);
        Carbon::setTestNow($testTime);

        // ---
        // Step 1: Issue a request to build 10 light fighters
        // ---
        $response = $this->post('/shipyard/add-buildrequest', [
            '_token' => csrf_token(),
            'type' => '204', // Light fighter
            'amount' => 10,
            'planet_id' => $this->currentPlanetId - 1,
        ]);

        // Assert the response status is successful (302 redirect).
        $response->assertStatus(500);
    }

    /**
     * Verify that building ships deducts correct amount of resources from planet.
     * @throws BindingResolutionException
     */
    public function testUnitQueueDeductResources(): void
    {
        $this->basicSetup();

        // Set the current time to a specific moment for testing
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 0);
        Carbon::setTestNow($testTime);

        // ---
        // Step 1: Issue a request to build 10 light fighters
        // ---
        $response = $this->post('/shipyard/add-buildrequest', [
            '_token' => csrf_token(),
            'type' => '204', // Light fighter
            'amount' => 10,
            'planet_id' => $this->currentPlanetId,
        ]);

        // Assert the response status is successful (302 redirect).
        $response->assertStatus(302);

        // ---
        // Step 2: Verify that nothing has been built as there were not enough resources.
        // ---
        $testTime = Carbon::create(2024, 1, 1, 13, 0, 0);
        Carbon::setTestNow($testTime);

        $response = $this->get('/shipyard');
        $response->assertStatus(200);
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Light\sFighter\s*<\/span>\s*0\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Light Fighter units have been built while there were no resources.');
    }

    /**
     * Verify that building ships without resources fails.
     * @throws BindingResolutionException
     */
    public function testUnitQueueInsufficientResources(): void
    {
        $this->basicSetup();

        // Set the current time to a specific moment for testing
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 0);
        Carbon::setTestNow($testTime);

        $this->planetAddResources(new Resources(30000,10000, 0,0));

        // Assert that we begin with 30500 metal and 10500 crystal.
        $response = $this->get('/shipyard');
        $response->assertStatus(200);

        $pattern = '/<span\s+id="resources_metal"\s+class="[^"]*">\s*30,500\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Not starting test at 30500 metal. Verify starting resources and update tests accordingly.');

        $pattern = '/<span\s+id="resources_crystal"\s+class="[^"]*">\s*10,500\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Not starting test at 10500 crystal. Verify starting resources and update tests accordingly.');

        // ---
        // Step 1: Issue a request to build 5 light fighters
        // ---
        $response = $this->post('/shipyard/add-buildrequest', [
            '_token' => csrf_token(),
            'type' => '204', // Light fighter
            'amount' => 5,
            'planet_id' => $this->currentPlanetId,
        ]);

        // Assert the response status is successful (302 redirect).
        $response->assertStatus(302);

        // Assert that after building 5 light fighters (=15k metal, 5k crystal) now have with 15500 metal and 5500 crystal left.
        $response = $this->get('/shipyard');
        $response->assertStatus(200);

        $pattern = '/<span\s+id="resources_metal"\s+class="[^"]*">\s*15,500\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Multiple unit build order incorrect amount of resources deducted.');

        $pattern = '/<span\s+id="resources_crystal"\s+class="[^"]*">\s*5,500\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Multiple unit build order incorrect amount of resources deducted.');
    }
}
