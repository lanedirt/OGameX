<?php

namespace Tests\Feature;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Carbon;
use OGame\GameObjects\Models\UnitCollection;
use OGame\Models\Resources;
use OGame\Services\FleetMissionService;
use Tests\AccountTestCase;

/**
 * Test that fleet dispatch works as expected.
 */
class FleetDispatchTest extends AccountTestCase
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
        // Add light cargo ship to the planet.
        $this->planetAddUnit('small_cargo', 5);
    }

    private function sendTransportMissionToSecondPlanet(UnitCollection $units, Resources $resources, int $assertStatus = 200) : void {
        // Convert units to array.
        $unitsArray = [];
        foreach ($units->units as $unit) {
            $unitsArray['am' . $unit->unitObject->id] = $unit->amount;
        }

        // Send fleet to the second planet of the test user.
        $post = $this->post('/ajax/fleet/dispatch/send-fleet', array_merge([
            'galaxy' => $this->secondPlanetService->getPlanetCoordinates()->galaxy,
            'system' => $this->secondPlanetService->getPlanetCoordinates()->system,
            'position' => $this->secondPlanetService->getPlanetCoordinates()->position,
            'type' => 1,
            'mission' => 3, // Transport mission
            'metal' => $resources->metal->get(),
            'crystal' => $resources->crystal->get(),
            'deuterium' => $resources->deuterium->get(),
            '_token' => csrf_token(),
        ], $unitsArray));

        // Assert that the fleet was dispatched successfully.
        $post->assertStatus($assertStatus);

        // Assert that eventbox fetch works when a fleet mission is active.
        $this->get('/ajax/fleet/eventbox/fetch')->assertStatus(200);
        $this->get('/ajax/fleet/eventlist/fetch')->assertStatus(200);
    }

    /**
     * Verify that dispatching a fleet deducts correct amount of units from planet.
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testDispatchFleetDeductUnits(): void
    {
        $this->basicSetup();

        // Assert that we begin with 5 small cargo ships on planet.
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'small_cargo', 5, 'Small Cargo ships are not at 5 units at beginning of test.');

        // Send fleet to the second planet of the test user.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('small_cargo'), 1);
        $this->sendTransportMissionToSecondPlanet($unitCollection, new Resources(100, 100, 0, 0));

        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'small_cargo', 4, 'Small Cargo ship not deducted from planet after fleet dispatch.');
    }

    /**
     * Verify that dispatching a fleet deducts correct amount of resources from planet.
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testDispatchFleetDeductResources(): void
    {
        $this->basicSetup();
        $this->get('/shipyard');

        // Get beginning resources of the planet.
        $beginningMetal = $this->planetService->metal()->get();
        $beginningCrystal = $this->planetService->crystal()->get();

        // Send fleet to the second planet of the test user.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('small_cargo'), 1);
        $this->sendTransportMissionToSecondPlanet($unitCollection, new Resources(100, 100, 0, 0));

        $response = $this->get('/shipyard');
        $response->assertStatus(200);

        // Assert that the resources were deducted from the planet.
        $this->assertResourcesOnPage($response, new Resources($beginningMetal - 100, $beginningCrystal - 100, 0, 0));
    }

    /**
     * Verify that dispatching a fleet with more resources than is on planet fails.
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testDispatchFleetDeductTooMuchResources(): void
    {
        $this->basicSetup();
        $this->get('/shipyard');

        // Send fleet to the second planet of the test user.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('small_cargo'), 1);
        $this->sendTransportMissionToSecondPlanet($unitCollection, new Resources(4500, 100, 0, 0), 500);
    }

    /**
     * Verify that dispatching a fleet with more units than is on planet fails.
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testDispatchFleetDeductTooMuchUnits(): void
    {
        $this->basicSetup();
        $this->get('/shipyard');

        // Send fleet to the second planet of the test user.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('small_cargo'), 10);
        $this->sendTransportMissionToSecondPlanet($unitCollection, new Resources(100, 100, 0, 0), 500);
    }

    /**
     * Verify that dispatching a transport fleet launches a return trip and brings back units to origin planet.
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testDispatchFleetReturnTrip(): void
    {
        $this->basicSetup();

        // Set time to static time 2024-01-01
        $startTime = Carbon::create(2024, 1, 1, 0, 0, 0);
        Carbon::setTestNow($startTime);

        // Assert that we begin with 5 small cargo ships on planet.
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'small_cargo', 5, 'Small Cargo ships are not at 5 units at beginning of test.');

        // Send fleet to the second planet of the test user.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('small_cargo'), 1);
        $this->sendTransportMissionToSecondPlanet($unitCollection, new Resources(100, 100, 0, 0));

        // Get just dispatched fleet mission ID from database.
        $fleetMissionService = app()->make(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionId = $fleetMission->id;

        // Get time it takes for the fleet to travel to the second planet.
        $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration($fleetMission);

        // Set time to fleet mission duration + 30 seconds (we do 30 instead of 1 second to test later if the return trip start and endtime work as expected
        // and are calculated based on the arrival time instead of the time the job got processed).
        $fleetParentTime = $startTime->copy()->addSeconds($fleetMissionDuration + 30);
        Carbon::setTestNow($fleetParentTime);

        // Do a request to trigger the update logic.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that the fleet mission is processed.
        $fleetMission = $fleetMissionService->getFleetMissionById($fleetMissionId);
        $this->assertTrue($fleetMission->processed == 1, 'Fleet mission is not processed after fleet has arrived at destination.');

        // Assert that a return trip has been launched by checking the active missions for the current planet.
        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();
        $this->assertCount(1, $activeMissions, 'No return trip launched after fleet with transport mission has arrived at destination.');

        // Advance time to the return trip arrival.
        $returnTripDuration = $activeMissions->first()->time_arrival - $activeMissions->first()->time_departure;

        $fleetReturnTime = $fleetParentTime->copy()->addSeconds($returnTripDuration + 1);
        Carbon::setTestNow($fleetReturnTime);

        // Do a request to trigger the update logic.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that the return trip has been processed.
        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();
        $this->assertCount(0, $activeMissions, 'Return trip is not processed after fleet has arrived back at origin planet.');

        // Assert that the units have been returned to the origin planet.
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'small_cargo', 5, 'Small Cargo ships are not at 5 units after return trip.');
    }

    /**
     * Verify that an active transport mission also shows the (not yet existing) return trip in the fleet event list.
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testDispatchFleetTransportReturnShown(): void
    {
        $this->basicSetup();

        // Set time to static time 2024-01-01
        $startTime = Carbon::create(2024, 1, 1, 0, 0, 0);
        Carbon::setTestNow($startTime);

        // Send fleet to the second planet of the test user.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('small_cargo'), 1);
        $this->sendTransportMissionToSecondPlanet($unitCollection, new Resources(100, 100, 0, 0));

        // The eventbox should only show 1 mission (the parent).
        $response = $this->get('/ajax/fleet/eventbox/fetch');
        $response->assertStatus(200);
        $response->assertJsonFragment(['friendly' => 1]);

        // The event list should show 2 missions (the parent and the to-be-created return trip).
        $response = $this->get('/ajax/fleet/eventlist/fetch');
        $response->assertStatus(200);
        $response->assertSee('Transport');
        $response->assertSee('Transport (R)');
        // Assert that we see both rows in the event list.
        $response->assertSee('data-return-flight="false"', false);
        $response->assertSee('data-return-flight="true"', false);
    }
}
