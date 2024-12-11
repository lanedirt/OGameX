<?php

namespace Tests\Feature;

use Exception;
use OGame\Models\Resources;
use OGame\Services\SettingsService;
use Tests\AccountTestCase;

/**
 * Test that the unit queue works as expected.
 */
class UnitQueueTest extends AccountTestCase
{
    /**
     * Prepare the planet for the test, so it has the required buildings and research.
     *
     * @return void
     */
    private function basicSetup(): void
    {
        // Set the universe speed to 8x for this test.
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('economy_speed', 8);
        $settingsService->set('research_speed', 1);

        $this->planetSetObjectLevel('robot_factory', 2);
        $this->planetSetObjectLevel('shipyard', 1);
        $this->planetSetObjectLevel('research_lab', 1);
        $this->playerSetResearchLevel('energy_technology', 1);
        $this->playerSetResearchLevel('combustion_drive', 1);
    }

    /**
     * Verify that building more than one of a ship works as expected.
     * @throws Exception
     */
    public function testUnitQueueShips(): void
    {
        $this->basicSetup();
        // Add resources to planet that test requires.
        $this->planetAddResources(new Resources(30000, 10000, 0, 0));

        // ---
        // Step 1: Issue a request to build 10 light fighters
        // ---
        $this->addShipyardBuildRequest('light_fighter', 10);

        // ---
        // Step 2: Verify the ships are in the build queue
        // ---
        $response = $this->get('/shipyard');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'light_fighter', 0, 'Light Fighter is not at 0 units directly after build request issued.');

        // ---
        // Step 3: Verify the ships are still in the build queue 1 minute later.
        // ---
        $this->travel(1)->minutes();

        $response = $this->get('/shipyard');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'light_fighter', 0, 'Light Fighter is not at 0 units 1m after build request issued.');

        // ---
        // Step 4: Verify that some ships are finished 20 minute later.
        // ---
        $this->travel(20)->minutes();

        $response = $this->get('/shipyard');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'light_fighter', 3, 'Light Fighter build job has not completed exactly 3 units 15m after build request issued.');

        // ---
        // Step 5: Verify that ALL ships are finished 14 hours later.
        // ---
        $this->travel(14)->hours();

        $response = $this->get('/shipyard');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'light_fighter', 10, 'Light Fighter build job is not finished yet 2h after build request issued.');
    }

    /**
     * Verify that building large amount of ships with default shipyard level works as expected.
     * @throws Exception
     */
    public function testUnitQueueLargeAmountLowShipyardLevel(): void
    {
        $this->basicSetup();

        // Add resources to planet that test requires.
        $this->planetAddResources(new Resources(30000000, 10000000, 0, 0));

        // ---
        // Step 1: Issue a request to build 1k light fighters
        // ---
        $this->addShipyardBuildRequest('light_fighter', 1000);

        // ---
        // Step 2: Verify the ships are in the build queue
        // ---
        $response = $this->get('/shipyard');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'light_fighter', 0, 'Light Fighter is not at 0 units directly after build request issued.');

        // Increase time by random 1-15 minute intervals 20 times in total to simulate partial updates.
        for ($i = 0; $i < 50; $i++) {
            $this->travel(rand(1, 15))->minutes();

            $response = $this->get('/shipyard');
            $response->assertStatus(200);
        }

        // ---
        // Step 4: Verify that all ships are finished 2 weeks later.
        // ---
        $this->travel(2)->weeks();

        $response = $this->get('/shipyard');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'light_fighter', 1000, 'Light Fighter build job is not finished yet 2h after build request issued.');
    }

    /**
     * Verify that building large amount of ships with high shipyard level works as expected.
     * @throws Exception
     */
    public function testUnitQueueLargeAmountHighShipyardLevel(): void
    {
        $this->basicSetup();

        // Set shipyard and nano factory to level 10 to speed up the build time, expecting 1 second per unit.
        $this->planetSetObjectLevel('shipyard', 12);
        $this->planetSetObjectLevel('nano_factory', 10);

        // Add resources to planet that test requires.
        $this->planetAddResources(new Resources(30000000, 10000000, 0, 0));

        // ---
        // Step 1: Issue a request to build 10k light fighters
        // ---
        $this->addShipyardBuildRequest('light_fighter', 10000);

        // ---
        // Step 2: Verify the ships are in the build queue
        // ---
        $response = $this->get('/shipyard');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'light_fighter', 0, 'Light Fighter is not at 0 units directly after build request issued.');

        // Increase time by random 1-15 second intervals 20 times in total to simulate partial updates.
        for ($i = 0; $i < 20; $i++) {
            $this->travel(rand(1, 15))->seconds();

            $response = $this->get('/shipyard');
            $response->assertStatus(200);
        }

        // Do it again but now with just millisecond differences.
        for ($i = 0; $i < 20; $i++) {
            $this->travel(rand(400, 999))->milliseconds();

            $response = $this->get('/shipyard');
            $response->assertStatus(200);
        }

        // Increase time by 10 hours to simulate the final update.
        $this->travel(10)->hours();

        $response = $this->get('/shipyard');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'light_fighter', 10000, 'Light Fighter build job is not finished yet 2h after build request issued.');
    }

    /**
     * Verify that adding three different build jobs and waiting for them all to complete works as expected.
     * @throws Exception
     */
    public function testUnitQueueShipsMultiQueues(): void
    {
        $this->basicSetup();

        // Add more specific resources to planet that test requires.
        // For 5 light fighters
        $this->planetAddResources(new Resources(15000, 5000, 0, 0));
        // For 10 solar satellites
        $this->planetAddResources(new Resources(0, 20000, 50000, 0));

        // ---
        // Step 1: Issue a request to build 3 light fighters, 10 solar sats, and then 2 light fighters
        // ---
        $this->addShipyardBuildRequest('light_fighter', 3);
        $this->addShipyardBuildRequest('solar_satellite', 10);
        $this->addShipyardBuildRequest('light_fighter', 2);

        // ---
        // Step 2: Verify the ships are in the build queue
        // ---
        $response = $this->get('/shipyard');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'light_fighter', 0, 'Light Fighter is not at 0 units directly after build request issued.');
        $this->assertObjectLevelOnPage($response, 'solar_satellite', 0, 'Solar Satellite is not at 0 units directly after build request issued.');

        // ---
        // Step 3: Verify that the light fighters and partial solar satellites are finished 25 minute later.
        // ---
        $this->travel(25)->minutes();

        $response = $this->get('/shipyard');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'light_fighter', 3, 'Light Fighter is not at 3 units 25m after build request issued.');
        $this->assertObjectLevelOnPage($response, 'solar_satellite', 2, 'Solar Satellite is not at 2 units 25m after build request issued.');

        // ---
        // Step 5: Verify that ALL ships are finished 35 minute later.
        // ---
        $this->travel(35)->minutes();

        $response = $this->get('/shipyard');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'light_fighter', 5, 'Light Fighter build job is not finished 2h after build request issued.');
        $this->assertObjectLevelOnPage($response, 'solar_satellite', 10, 'Solar Satellite build job is not finished 2h after build request issued.');
    }

    /**
     * Verify that building more than one of a defense unit works as expected.
     * @throws Exception
     */
    public function testUnitQueueDefense(): void
    {
        $this->basicSetup();

        $this->planetAddResources(new Resources(20000, 0, 0, 0));
        $this->planetSetObjectLevel('robot_factory', 2);
        $this->planetSetObjectLevel('shipyard', 1);

        // ---
        // Step 1: Issue a request to build 10 light fighters
        // ---
        $this->addDefenseBuildRequest('rocket_launcher', 10);

        // ---
        // Step 2: Verify the defense units are in the build queue
        // ---
        $response = $this->get('/defense');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'rocket_launcher', 0, 'Rocket Launcher is not at 0 units directly after build request issued.');

        // ---
        // Step 3: Verify the defense units are still in the build queue 1 minute later.
        // ---
        $this->travel(1)->minutes();

        $response = $this->get('/defense');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'rocket_launcher', 0, 'Rocket Launcher is not at 0 units 30 seconds after build request issued.');

        // ---
        // Step 4: Verify that some defense units are finished 10 minute later.
        // ---
        $this->travel(10)->minutes();

        $response = $this->get('/defense');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'rocket_launcher', 3, 'Rocket Launcher build job has not completed exactly 3 units 10 minutes after build request issued.');

        // ---
        // Step 5: Verify that ALL defense units are finished 1h later.
        // ---
        $this->travel(1)->hours();

        $response = $this->get('/defense');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'rocket_launcher', 10, 'Rocket Launcher build job is not finished yet 1h after build request issued.');
    }

    /**
     * Verify that building ships deducts correct amount of resources from planet.
     * @throws Exception
     */
    public function testUnitQueueDeductResources(): void
    {
        $this->basicSetup();

        // ---
        // Step 1: Issue a request to build 10 light fighters
        // ---
        $this->addShipyardBuildRequest('light_fighter', 10);

        // ---
        // Step 2: Verify that nothing has been built as there were not enough resources.
        // ---
        $this->travel(1)->hours();

        $response = $this->get('/shipyard');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'light_fighter', 0, 'Light Fighter units have been built while there were no resources.');
    }

    /**
     * Verify that building ships without resources fails.
     * @throws Exception
     */
    public function testUnitQueueInsufficientResources(): void
    {
        $this->basicSetup();
        $this->planetAddResources(new Resources(30000, 10000, 0, 0));

        // Assert that we begin with 30500 metal and 10500 crystal.
        $response = $this->get('/shipyard');
        $response->assertStatus(200);
        $this->assertResourcesOnPage($response, new Resources(30500, 10500, 0, 0));

        // ---
        // Step 1: Issue a request to build 5 light fighters
        // ---
        $this->addShipyardBuildRequest('light_fighter', 5);

        // Assert that after building 5 light fighters (=15k metal, 5k crystal) now have with 15500 metal and 5500 crystal left.
        $response = $this->get('/shipyard');
        $response->assertStatus(200);

        $this->assertResourcesOnPage($response, new Resources(15500, 5500, 0, 0));
    }

    /**
     * Verify that shipyard requirement is working for unit objects.
     * @throws Exception
     */
    public function testUnitQueueShipyardRequirement(): void
    {
        // Add required resources to planet
        $this->planetAddResources(new Resources(5000, 5000, 5000, 0));

        // Assert that build requirements for Solar Satellite are not met as Shipyard is missing
        $response = $this->get('/shipyard');
        $response->assertStatus(200);
        $this->assertRequirementsNotMet($response, 'solar_satellite', 'Solar Satellite build requirements are met.');

        // Add Shipyard level 1 with requisities to build queue
        $this->addFacilitiesBuildRequest('robot_factory');
        $this->addFacilitiesBuildRequest('robot_factory');
        $this->addFacilitiesBuildRequest('shipyard');

        // Assert that build requirements for Solar Satellite are not met as Shipyard is in build queue
        $response = $this->get('/shipyard');
        $response->assertStatus(200);
        $this->assertRequirementsNotMet($response, 'solar_satellite', 'Solar Satellite build requirements are met.');

        // Verify that Solar Satellite can be added to unit queue 10 minute later.
        $this->travel(10)->minutes();
        $this->addShipyardBuildRequest('solar_satellite', 1);

        // Verify the building is finished 10 minute later.
        $this->travel(10)->minutes();

        $response = $this->get('/shipyard');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'solar_satellite', 1, 'Solar Satellite build job is not finished yet 10 minute after build request issued.');
    }

    /**
     * Verify that research requirements are working for unit objects.
     * @throws Exception
     */
    public function testUnitQueueResearchRequirement(): void
    {
        // Add required resources to planet
        $this->planetAddResources(new Resources(5000, 5000, 5000, 0));

        // Assert that build requirements for Light Fighter are not met
        $response = $this->get('/shipyard');
        $response->assertStatus(200);
        $this->assertRequirementsNotMet($response, 'light_fighter', 'Light Fighter build requirements are met.');

        // Add Shipyard and Research Lab to build queue
        $this->addFacilitiesBuildRequest('robot_factory');
        $this->addFacilitiesBuildRequest('robot_factory');
        $this->addFacilitiesBuildRequest('shipyard');
        $this->addFacilitiesBuildRequest('research_lab');

        // Verify the buildings are finished 10 minutes later.
        $this->travel(10)->minutes();

        $response = $this->get('/facilities');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'research_lab', 1, 'Research Lab build job is not finished yet 10 minute after build request issued.');

        // Assert that build requirements for Light Fighter are not met as Combustion Drive technology is missing
        $response = $this->get('/shipyard');
        $response->assertStatus(200);
        $this->assertRequirementsNotMet($response, 'light_fighter', 'Light Fighter build requirements are met.');

        // Add required technology to research queue
        $this->addResearchBuildRequest('energy_technology');
        $this->addResearchBuildRequest('combustion_drive');

        // Assert that build requirements for Light Fighter are not met as Combustion Drive technology is still in
        // research queue and not finished yet.
        $response = $this->get('/shipyard');
        $response->assertStatus(200);
        $this->assertRequirementsNotMet($response, 'light_fighter', 'Light Fighter build requirements are met.');

        // Verify the research is finished 10 minute later.
        $this->travel(10)->minutes();

        // Reload the page to update the research queue
        $response = $this->get('/research');
        $response->assertStatus(200);

        $this->assertObjectLevelOnPage($response, 'combustion_drive', 1, 'Combustion Drive is not at level one 10 minutes after build request issued.');

        $this->addShipyardBuildRequest('light_fighter', 1);

        // Verify the ship is finished 10 minute later.
        $this->travel(10)->minutes();

        $response = $this->get('/shipyard');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'light_fighter', 1, 'Light Fighter build job is not finished yet 10 minute after build request issued.');
    }

    /**
     * Verify that unit construction time is calculated correctly (higher than 0)
     * @throws Exception
     */
    public function testUnitProductionTime(): void
    {
        $this->basicSetup();

        $unit_construction_time = $this->planetService->getUnitConstructionTime('light_fighter');
        $this->assertGreaterThan(0, $unit_construction_time);
    }

    /**
     * Verify that unit construction time is calculated correctly (higher than 0)
     * @throws Exception
     */
    public function testUnitProductionTimeHighShipyardLevel(): void
    {
        $this->basicSetup();

        $this->planetAddResources(new Resources(200000, 0, 0, 0));
        $this->planetSetObjectLevel('robot_factory', 2);
        $this->planetSetObjectLevel('shipyard', 10);
        $this->planetSetObjectLevel('nano_factory', 10);

        // ---
        // Step 1: Issue a request to build 100 rocket launchers.
        // ---
        $this->addDefenseBuildRequest('rocket_launcher', 100);

        // ---
        // Step 2: Verify the defense units are in the build queue
        // ---
        $response = $this->get('/defense');

        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'rocket_launcher', 0, 'Rocket Launcher is not at 0 units directly after build request issued.');

        // ---
        // Step 3: Verify that after 50 seconds, exactly 50 units are built. (Minimum time per unit is always 1 second)
        // ---
        $this->travel(50)->seconds();

        $response = $this->get('/defense');
        $response->assertStatus(200);
        $this->assertObjectLevelOnPage($response, 'rocket_launcher', 50, 'Rocket Launcher is not at 50 units 50 seconds after build request issued.');
    }
}
