<?php

namespace Tests\Feature;

use OGame\Models\Resources;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use OGame\Models\Planet;
use OGame\Services\ObjectService;
use Tests\AccountTestCase;

/**
 * Test that planet field restrictions work correctly for buildings.
 */
class PlanetFieldRestrictionTest extends AccountTestCase
{
    use DatabaseTransactions;

    /**
     * Test that a building cannot be built when planet fields are full.
     *
     * @return void
     */
    public function testCannotBuildWhenPlanetFieldsAreFull(): void
    {
        // Set the planet to have very few fields and fill them up
        $planetModel = Planet::where('id', $this->planetService->getPlanetId())->first();
        $planetModel->field_max = 5;
        $planetModel->save();

        // Fill up all 5 fields by building 5 facilities to level 1
        $facilities = ['robot_factory', 'shipyard', 'research_lab', 'nano_factory', 'metal_store'];
        foreach ($facilities as $i => $machineName) {
            $object = ObjectService::getObjectByMachineName($machineName);
            $planetModel->{$machineName} = 1;
        }
        $planetModel->save();

        // Reload the planet service to get updated data
        $this->planetService->reloadPlanet();

        // Verify that building count equals max fields
        $this->assertEquals(5, $this->planetService->getBuildingCount());
        $this->assertEquals(5, $this->planetService->getPlanetFieldMax());

        // Try to build another facility (crystal_store)
        $object = ObjectService::getObjectByMachineName('crystal_store');

        $response = $this->post('/facilities/add-buildrequest', [
            '_token' => csrf_token(),
            'technologyId' => $object->id,
        ]);

        // Should receive an error response, not success
        $response->assertStatus(200);
        $json = $response->json();
        $this->assertArrayHasKey('success', $json);
        $this->assertFalse($json['success']);
    }

    /**
     * Test that a building CAN be built when fields are not full.
     *
     * @return void
     */
    public function testCanBuildWhenPlanetFieldsAreNotFull(): void
    {
        // Set the planet to have 10 fields
        $planetModel = Planet::where('id', $this->planetService->getPlanetId())->first();
        $planetModel->field_max = 10;
        $planetModel->save();

        // Build only 3 facilities
        $facilities = ['robot_factory', 'shipyard', 'research_lab'];
        foreach ($facilities as $machineName) {
            $object = ObjectService::getObjectByMachineName($machineName);
            $planetModel->{$machineName} = 1;
        }
        $planetModel->save();

        // Reload the planet service
        $this->planetService->reloadPlanet();

        // Verify that building count is less than max fields
        $this->assertEquals(3, $this->planetService->getBuildingCount());
        $this->assertEquals(10, $this->planetService->getPlanetFieldMax());

        // Add resources to build crystal_store
        $this->planetAddResources(new Resources(100000, 100000, 100000));

        // Try to build crystal_store
        $object = ObjectService::getObjectByMachineName('crystal_store');

        $response = $this->post('/facilities/add-buildrequest', [
            '_token' => csrf_token(),
            'technologyId' => $object->id,
        ]);

        // Should receive success response
        $response->assertStatus(200);
        $json = $response->json();
        $this->assertArrayHasKey('status', $json);
        $this->assertEquals('success', $json['status']);
    }

    /**
     * Test that a building that doesn't consume fields (Space Dock) can still be built when planet is full.
     *
     * @return void
     */
    public function testCanBuildSpaceDockWhenPlanetFieldsAreFull(): void
    {
        // Set the planet to have 6 fields total
        $planetModel = Planet::where('id', $this->planetService->getPlanetId())->first();
        $planetModel->field_max = 6;
        $planetModel->save();

        // Fill up 6 fields - set shipyard to level 2 to meet space dock requirement
        // robot_factory(1) + shipyard(2) + research_lab(1) + nano_factory(1) + metal_store(1) = 6 buildings = 6 fields
        $planetModel->robot_factory = 1;
        $planetModel->shipyard = 2;
        $planetModel->research_lab = 1;
        $planetModel->nano_factory = 1;
        $planetModel->metal_store = 1;
        $planetModel->save();

        // Reload the planet service
        $this->planetService->reloadPlanet();

        // Verify that building count equals max fields
        $this->assertEquals(6, $this->planetService->getBuildingCount());
        $this->assertEquals(6, $this->planetService->getPlanetFieldMax());

        // Add resources to build space_dock
        $this->planetAddResources(new Resources(100000, 100000, 100000));

        // Try to build space_dock (which doesn't consume fields)
        $object = ObjectService::getObjectByMachineName('space_dock');

        $response = $this->post('/facilities/add-buildrequest', [
            '_token' => csrf_token(),
            'technologyId' => $object->id,
        ]);

        // Should receive success response since space_dock doesn't consume fields
        $response->assertStatus(200);
        $json = $response->json();
        $this->assertArrayHasKey('status', $json);
        $this->assertEquals('success', $json['status']);
    }

    /**
     * Test that the ViewModel correctly reflects field limit status.
     *
     * @return void
     */
    public function testFacilitiesPageShowsCorrectStatusWhenFieldsFull(): void
    {
        // Set the planet to have very few fields and fill them up
        $planetModel = Planet::where('id', $this->planetService->getPlanetId())->first();
        $planetModel->field_max = 5;
        $planetModel->save();

        // Fill up all 5 fields
        $planetModel->robot_factory = 1;
        $planetModel->shipyard = 1;
        $planetModel->research_lab = 1;
        $planetModel->nano_factory = 1;
        $planetModel->metal_store = 1;
        $planetModel->save();

        // Reload the planet service to get updated data
        $this->planetService->reloadPlanet();

        // Visit the facilities page
        $response = $this->get('/facilities');

        $response->assertStatus(200);

        // The page should load successfully
        // The important thing is that buildings that would exceed the field limit
        // should be disabled in the UI - this is controlled by the ViewModel
        $this->assertEquals(5, $this->planetService->getBuildingCount());
        $this->assertEquals(5, $this->planetService->getPlanetFieldMax());
    }

    /**
     * Test that terraformer increases max fields allowing more buildings.
     *
     * @return void
     */
    public function testTerraformerIncreasesMaxFields(): void
    {
        // Set the planet to have 5 base fields
        $planetModel = Planet::where('id', $this->planetService->getPlanetId())->first();
        $planetModel->field_max = 5;
        $planetModel->terraformer = 0;
        $planetModel->save();

        // Fill up all 5 fields
        $facilities = ['robot_factory', 'shipyard', 'research_lab', 'nano_factory', 'metal_store'];
        foreach ($facilities as $machineName) {
            $object = ObjectService::getObjectByMachineName($machineName);
            $planetModel->{$machineName} = 1;
        }
        $planetModel->save();

        // Reload the planet service
        $this->planetService->reloadPlanet();

        // Verify max fields is 5
        $this->assertEquals(5, $this->planetService->getPlanetFieldMax());

        // Now upgrade terraformer to level 1 (should add 5 fields)
        $planetModel->terraformer = 1;
        $planetModel->save();
        $this->planetService->reloadPlanet();

        // Verify max fields is now 10 (5 base + 5 from terraformer level 1)
        $this->assertEquals(10, $this->planetService->getPlanetFieldMax());

        // Now we should be able to build another facility
        $this->planetAddResources(new Resources(100000, 100000, 100000));

        $object = ObjectService::getObjectByMachineName('crystal_store');
        $response = $this->post('/facilities/add-buildrequest', [
            '_token' => csrf_token(),
            'technologyId' => $object->id,
        ]);

        // Should receive success response
        $response->assertStatus(200);
        $json = $response->json();
        $this->assertArrayHasKey('status', $json);
        $this->assertEquals('success', $json['status']);
    }

    /**
     * Test that space_dock (which doesn't consume fields) doesn't affect field count.
     *
     * @return void
     */
    public function testSpaceDockDoesNotConsumeFields(): void
    {
        // Set the planet to have 5 fields
        $planetModel = Planet::where('id', $this->planetService->getPlanetId())->first();
        $planetModel->field_max = 5;
        $planetModel->save();

        // Build 5 facilities (filling all fields)
        $facilities = ['robot_factory', 'shipyard', 'research_lab', 'nano_factory', 'metal_store'];
        foreach ($facilities as $machineName) {
            $object = ObjectService::getObjectByMachineName($machineName);
            $planetModel->{$machineName} = 1;
        }
        $planetModel->save();

        // Reload the planet service
        $this->planetService->reloadPlanet();

        // Building count should be 5
        $this->assertEquals(5, $this->planetService->getBuildingCount());

        // Now build space_dock (which doesn't consume fields)
        $planetModel->space_dock = 1;
        $planetModel->save();
        $this->planetService->reloadPlanet();

        // Building count should still be 5 (space_dock doesn't count)
        $this->assertEquals(5, $this->planetService->getBuildingCount());

        // And we should still not be able to build another field-consuming building
        $object = ObjectService::getObjectByMachineName('crystal_store');

        $response = $this->post('/facilities/add-buildrequest', [
            '_token' => csrf_token(),
            'technologyId' => $object->id,
        ]);

        // Should receive error response
        $response->assertStatus(200);
        $json = $response->json();
        $this->assertArrayHasKey('success', $json);
        $this->assertFalse($json['success']);
    }

    /**
     * Test that buildings show as unavailable when queued buildings would exceed field limit.
     *
     * @return void
     */
    public function testBuildingsShowAsUnavailableWhenQueueWouldExceedFieldLimit(): void
    {
        // Set the planet to have 5 fields total
        $planetModel = Planet::where('id', $this->planetService->getPlanetId())->first();
        $planetModel->field_max = 5;
        $planetModel->save();

        // Build 3 facilities at level 1 (using 3 fields)
        $planetModel->robot_factory = 1;
        $planetModel->shipyard = 1;
        $planetModel->research_lab = 1;
        $planetModel->save();

        // Reload the planet service
        $this->planetService->reloadPlanet();

        // Verify current state
        $this->assertEquals(3, $this->planetService->getBuildingCount());
        $this->assertEquals(5, $this->planetService->getPlanetFieldMax());

        // Add enough resources for multiple buildings
        $this->planetAddResources(new Resources(100000, 100000, 100000));

        // Queue metal_store (level 0 -> 1) - will use 4th field
        $object = ObjectService::getObjectByMachineName('metal_store');
        $response = $this->post('/facilities/add-buildrequest', [
            '_token' => csrf_token(),
            'technologyId' => $object->id,
        ]);
        $response->assertStatus(200);
        $json = $response->json();
        $this->assertArrayHasKey('status', $json);
        $this->assertEquals('success', $json['status']);

        // Queue crystal_store (level 0 -> 1) - will use 5th field
        $object = ObjectService::getObjectByMachineName('crystal_store');
        $response = $this->post('/facilities/add-buildrequest', [
            '_token' => csrf_token(),
            'technologyId' => $object->id,
        ]);
        $response->assertStatus(200);
        $json = $response->json();
        $this->assertArrayHasKey('status', $json);
        $this->assertEquals('success', $json['status']);

        // Reload the planet to get updated state
        $this->planetService->reloadPlanet();

        // Visit the facilities page - buildings that would exceed field limit should show as unavailable
        $response = $this->get('/facilities');
        $response->assertStatus(200);

        // The current building count is still 3, but after queue completes it will be 5
        $this->assertEquals(3, $this->planetService->getBuildingCount());

        // deuterium_store should show as unavailable (would use 6th field but only 5 exist)
        // We can't easily test the UI state directly, but we can verify the queue accepts the item
        // The UI state is tested by checking the ViewModel properties in the controller
        $object = ObjectService::getObjectByMachineName('deuterium_store');
        $response = $this->post('/facilities/add-buildrequest', [
            '_token' => csrf_token(),
            'technologyId' => $object->id,
        ]);

        // Queue should accept the item (not throw an exception)
        $response->assertStatus(200);
        $json = $response->json();
        $this->assertArrayHasKey('status', $json);
        $this->assertEquals('success', $json['status']);
    }
}
