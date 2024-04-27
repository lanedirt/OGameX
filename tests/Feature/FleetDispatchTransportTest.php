<?php

namespace Feature;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\FleetDispatchTestCase;

/**
 * Test that fleet dispatch works as expected.
 */
class FleetDispatchTransportTest extends FleetDispatchTestCase
{
    /**
     * @var int The mission type for the test.
     */
    protected int $missionType = 3;

    /**
     * @var string The mission name for the test, displayed in UI.
     */
    protected string $missionName = 'Transport';

    protected bool $hasReturnMission = true;

    /**
     * Verify that dispatching a transport fleet launches a return trip and brings back units to origin planet.
     * @throws BindingResolutionException
     * @throws Exception
     */
   /* public function testDispatchFleetReturnTrip(): void
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

        // Set all messages as read to avoid unread messages count in the overview.
        $this->playerSetAllMessagesRead();

        // Do a request to trigger the update logic.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that the fleet mission is processed.
        $fleetMission = $fleetMissionService->getFleetMissionById($fleetMissionId);
        $this->assertTrue($fleetMission->processed == 1, 'Fleet mission is not processed after fleet has arrived at destination.');

        // Assert that message has been sent to player and contains the correct information.
        $this->assertMessageReceivedAndContains('fleets', 'transport', [
            'reaches the planet',
            'Metal: 100',
            'Crystal: 100',
            $this->planetService->getPlanetName(),
            $this->secondPlanetService->getPlanetName()
        ]);

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

        // Assert that message has been sent to player and contains the correct information.
        $this->assertMessageReceivedAndContains('fleets', 'other', [
            'Your fleet is returning from',
            'The fleet doesn\'t deliver goods',
            $this->planetService->getPlanetName(),
            $this->secondPlanetService->getPlanetName()
        ]);
    }*/
}
