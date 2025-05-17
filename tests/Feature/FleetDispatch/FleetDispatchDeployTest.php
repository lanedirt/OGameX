<?php

namespace Tests\Feature\FleetDispatch;

use Illuminate\Support\Carbon;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Resources;
use OGame\Services\FleetMissionService;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;
use OGame\Services\SettingsService;
use Tests\FleetDispatchTestCase;

/**
 * Test that fleet dispatch works as expected for deployment missions.
 */
class FleetDispatchDeployTest extends FleetDispatchTestCase
{
    /**
     * @var int The mission type for the test.
     */
    protected int $missionType = 4;

    /**
     * @var string The mission name for the test, displayed in UI.
     */
    protected string $missionName = 'Deployment';

    /**
     * Prepare the planet for the test so it has the required buildings and research.
     *
     * @return void
     */
    protected function basicSetup(): void
    {
        $this->planetSetObjectLevel('robot_factory', 2);
        $this->planetSetObjectLevel('shipyard', 1);
        $this->planetSetObjectLevel('research_lab', 1);
        $this->playerSetResearchLevel('computer_technology', 2);
        $this->playerSetResearchLevel('energy_technology', 1);
        $this->playerSetResearchLevel('combustion_drive', 1);
        $this->planetAddUnit('small_cargo', 5);
        $this->planetAddResources(new Resources(0, 0, 100000, 0));
        // Set the fleet speed to 5x for this test.
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('fleet_speed', 5);
    }

    protected function messageCheckMissionArrival(PlanetService $destinationPlanet): void
    {
        // Assert that message has been sent to player and contains the correct information.
        $this->assertMessageReceivedAndContains('fleets', 'other', [
            'One of your fleets from',
            'has reached',
            'Metal: 100',
            $this->planetService->getPlanetName(),
            $destinationPlanet->getPlanetName()
        ]);
    }

    protected function messageCheckMissionReturn(): void
    {
        // Assert that message has been sent to player and contains the correct information.
        $this->assertMessageReceivedAndContains('fleets', 'other', [
            'Your fleet is returning from',
            'Metal:',
            $this->planetService->getPlanetName(),
            $this->secondPlanetService->getPlanetName()
        ]);
    }

    /**
     * Assert that check request to dispatch fleet to second planet succeeds with small cargo ship.
     */
    public function testFleetCheckToOwnPlanetSuccess(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);
        $this->fleetCheckToSecondPlanet($unitCollection, true);
    }

    /**
     * Assert that check request to dispatch fleet to planet of other player fails.
     */
    public function testFleetCheckToForeignPlanetError(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);
        $this->fleetCheckToOtherPlayer($unitCollection, false);
    }

    /**
     * Assert that check request to dispatch fleet to empty position fails.
     */
    public function testFleetCheckToEmptyPlanetError(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);
        $this->fleetCheckToEmptyPosition($unitCollection, false);
    }

    public function testDispatchFleetWithoutResources(): void
    {
        $this->basicSetup();

        // Send fleet to the second planet of the test user WITHOUT resources.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);
        $this->sendMissionToSecondPlanet($unitCollection, new Resources(0, 0, 0, 0));

        // Set all messages as read to avoid unread messages count in the overview.
        $this->playerSetAllMessagesRead();

        // Increase time by 10 hours to ensure the mission is done.
        $this->travel(10)->hours();

        // Do a request to trigger the update logic.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that message has been sent to player and contains the correct information.
        $this->assertMessageReceivedAndContains('fleets', 'other', [
            'has reached',
            //'The fleet doesn`t deliver goods.', Deployment now leaves half of the consumed deuterium on the target, so this feature has been removed.
            $this->planetService->getPlanetName(),
            $this->secondPlanetService->getPlanetName()
        ]);

        // Assert that the units have been added to the second planet.
        $this->switchToSecondPlanet();
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'small_cargo', 1, 'Small Cargo ship has not been added to second planet after deploy mission.');
    }

    /**
     * Verify that dispatching a fleet deducts correct amount of units from planet.
     */
    public function testDispatchFleetDeductUnits(): void
    {
        $this->basicSetup();

        // Assert that we begin with 5 small cargo ships on planet.
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'small_cargo', 5, 'Small Cargo ships are not at 5 units at beginning of test.');

        // Send fleet to the second planet of the test user.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);
        $this->sendMissionToSecondPlanet($unitCollection, new Resources(100, 100, 0, 0));

        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'small_cargo', 4, 'Small Cargo ship not deducted from planet after fleet dispatch.');
    }

    /**
     * Verify that dispatching a fleet deducts correct amount of resources from planet.
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
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);
        $this->sendMissionToSecondPlanet($unitCollection, new Resources(100, 100, 0, 0));

        $response = $this->get('/shipyard');
        $response->assertStatus(200);

        // Assert that the resources were deducted from the planet.
        $this->assertResourcesOnPage($response, new Resources($beginningMetal - 100, $beginningCrystal - 100, 0, 0));
    }

    /**
     * Verify that dispatching a fleet with more resources than is on planet fails.
     */
    public function testDispatchFleetDeductTooMuchResources(): void
    {
        $this->basicSetup();
        $this->get('/shipyard');

        // Send fleet to the second planet of the test user.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);
        $this->sendMissionToSecondPlanet($unitCollection, new Resources(4500, 100, 0, 0), false);
    }

    /**
     * Verify that dispatching a fleet with more units than is on planet fails.
     */
    public function testDispatchFleetDeductTooMuchUnits(): void
    {
        $this->basicSetup();
        $this->get('/shipyard');

        // Send fleet to the second planet of the test user.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 10);
        $this->sendMissionToSecondPlanet($unitCollection, new Resources(100, 100, 0, 0), false);
    }

    /**
     * Verify that dispatching a fleet does NOT launch a return trip as deploy is only one way.
     */
    public function testDispatchFleetNoReturnTrip(): void
    {
        $this->basicSetup();

        // Assert that we begin with 5 small cargo ships on planet.
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'small_cargo', 5, 'Small Cargo ships are not at 5 units at beginning of test.');

        // Send fleet to the second planet of the test user.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);
        $this->sendMissionToSecondPlanet($unitCollection, new Resources(100, 100, 0, 0));

        // Get just dispatched fleet mission ID from database.
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionId = $fleetMission->id;

        // Get time it takes for the fleet to travel to the second planet.
        $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration($this->planetService, $this->secondPlanetService->getPlanetCoordinates(), $unitCollection);

        // Set time to fleet mission duration + 30 seconds (we do 30 instead of 1 second to test later if the return trip start and endtime work as expected
        // and are calculated based on the arrival time instead of the time the job got processed).
        $this->travel($fleetMissionDuration + 30)->seconds();

        // Set all messages as read to avoid unread messages count in the overview.
        $this->playerSetAllMessagesRead();

        // Do a request to trigger the update logic.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that the fleet mission is processed.
        $fleetMission = $fleetMissionService->getFleetMissionById($fleetMissionId, false);
        $this->assertTrue($fleetMission->processed == 1, 'Fleet mission is not processed after fleet has arrived at destination.');

        // Check that message has been received by calling extended method
        $this->messageCheckMissionArrival($this->secondPlanetService);

        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();

        // Assert that NO return trip has been launched by checking the active missions for the current planet.
        $this->assertCount(0, $activeMissions, 'Return trip launched after fleet with deployment mission has arrived at destination.');
    }

    /**
     * Verify that dispatching a fleet towards a moon does NOT launch a return trip as deploy is only one way.
     */
    public function testDispatchFleetMoonNoReturnTrip(): void
    {
        $this->basicSetup();

        // Assert that we begin with 5 small cargo ships on planet.
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'small_cargo', 5, 'Small Cargo ships are not at 5 units at beginning of test.');

        // Send fleet to the second planet of the test user.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);
        $this->sendMissionToFirstPlanetMoon($unitCollection, new Resources(100, 100, 0, 0));

        // Get just dispatched fleet mission ID from database.
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionId = $fleetMission->id;

        // Get time it takes for the fleet to travel to the second planet.
        $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration($this->planetService, $this->moonService->getPlanetCoordinates(), $unitCollection);

        // Set time to fleet mission duration + 30 seconds (we do 30 instead of 1 second to test later if the return trip start and endtime work as expected
        // and are calculated based on the arrival time instead of the time the job got processed).
        $this->travel($fleetMissionDuration + 30)->seconds();

        // Set all messages as read to avoid unread messages count in the overview.
        $this->playerSetAllMessagesRead();

        // Do a request to trigger the update logic.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that the fleet mission is processed.
        $fleetMission = $fleetMissionService->getFleetMissionById($fleetMissionId, false);
        $this->assertTrue($fleetMission->processed == 1, 'Fleet mission is not processed after fleet has arrived at destination.');

        // Check that message has been received by calling extended method
        $this->messageCheckMissionArrival($this->moonService);

        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();

        // Assert that NO return trip has been launched by checking the active missions for the current planet.
        $this->assertCount(0, $activeMissions, 'Return trip launched after fleet with deployment mission has arrived at destination.');
    }

    /**
     * Verify that an active mission also does NOT show a return trip in the fleet event list.
     */
    public function testDispatchFleetReturnNotShown(): void
    {
        $this->basicSetup();

        // Send fleet to the second planet of the test user.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);
        $this->sendMissionToSecondPlanet($unitCollection, new Resources(100, 100, 0, 0));

        // The eventbox should only show 1 mission (the parent).
        $response = $this->get('/ajax/fleet/eventbox/fetch');
        $response->assertStatus(200);
        $response->assertJsonFragment(['friendly' => 1]);

        // The event list should show either 1 or 2 missions (the parent and the to-be-created return trip).
        $response = $this->get('/ajax/fleet/eventlist/fetch');
        $response->assertStatus(200);

        // If the mission does not have a return mission, we should only see the parent mission.
        $response->assertSee($this->missionName);
        $response->assertDontSee($this->missionName .  ' (R)');
        // Assert that we see only parent row in the event list.
        $response->assertSee('data-return-flight="false"', false);
        $response->assertDontSee('data-return-flight="true"', false);
    }

    /**
     * Verify that canceling/recalling an active mission works.
     */
    public function testDispatchFleetRecallMission(): void
    {
        $this->basicSetup();

        // Add resources for test.
        $this->planetAddResources(new Resources(5000, 5000, 0, 0));

        // Assert that we begin with 5 small cargo ships on planet.
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'small_cargo', 5, 'Small Cargo ships are not at 5 units at beginning of test.');

        // Send fleet to the second planet of the test user.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 5);
        $this->sendMissionToSecondPlanet($unitCollection, new Resources(5000, 5000, 0, 0));

        // Get just dispatched fleet mission ID from database.
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionId = $fleetMission->id;

        // Advance time by 1 minute.
        $fleetParentTime = Carbon::getTestNow()->addSeconds(60);
        $this->travelTo($fleetParentTime);

        // Cancel the mission
        $response = $this->post('/ajax/fleet/dispatch/recall-fleet', [
            'fleet_mission_id' => $fleetMissionId,
            '_token' => csrf_token(),
        ]);
        $response->assertStatus(200);

        // Assert that the original mission is now canceled.
        $fleetMission = $fleetMissionService->getFleetMissionById($fleetMissionId, false);
        $this->assertTrue($fleetMission->canceled == 1, 'Fleet mission is not canceled after fleet recall is requested.');

        // Assert that only the return trip is now visible.
        // The eventbox should only show 1 mission (the parent).
        $response = $this->get('/ajax/fleet/eventbox/fetch');
        $response->assertStatus(200);
        $response->assertJsonFragment(['friendly' => 1]);
        $response->assertJsonFragment(['eventText' => $this->missionName . ' (R)']);

        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionId = $fleetMission->id;
        $fleetMission = $fleetMissionService->getFleetMissionById($fleetMissionId, false);

        // Assert that the return trip arrival time is exactly 1 minute after the cancellation time.
        // Because the return trip should take exactly as long as the original trip has traveled until it was canceled.
        $this->assertTrue($fleetMission->time_arrival == $fleetParentTime->addSeconds(60)->timestamp, 'Return trip duration is not the same as the original mission has been active.');

        // Set all messages as read in order to check if we receive the correct messages during return trip process.
        $this->playerSetAllMessagesRead();

        // Advance time by amount of minutes it takes for the return trip to arrive.
        $this->travelTo(Carbon::createFromTimestamp($fleetMission->time_arrival));

        // Do a request to trigger the update logic.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that the return trip is processed.
        $fleetMission = $fleetMissionService->getFleetMissionById($fleetMissionId, false);
        $this->assertTrue($fleetMission->processed == 1, 'Return trip is not processed after fleet has arrived back at origin planet.');

        // Assert that the units have been returned to the origin planet.
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'small_cargo', 5, 'Small Cargo ships are not at original 5 units after recalled trip has been processed.');
        // Assert that the resources have been returned to the origin planet.
        $this->planetService->reloadPlanet();
        $this->assertTrue($this->planetService->hasResources(new Resources(5000, 5000, 0, 0)), 'Resources are not returned to origin planet after recalling mission.');

        $this->messageCheckMissionReturn();
    }

    /**
     * Verify that canceling/recalling an active mission twice results in an error.
     */
    public function testDispatchFleetRecallMissionTwiceError(): void
    {
        $this->basicSetup();

        // Assert that we begin with 5 small cargo ships on planet.
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'small_cargo', 5, 'Small Cargo ships are not at 5 units at beginning of test.');

        // Send fleet to the second planet of the test user.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);
        $this->sendMissionToSecondPlanet($unitCollection, new Resources(100, 100, 0, 0));

        // Get just dispatched fleet mission ID from database.
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionId = $fleetMission->id;

        // Advance time by 1 minute
        $this->travel(1)->minutes();

        // Cancel the mission
        $response = $this->post('/ajax/fleet/dispatch/recall-fleet', [
            'fleet_mission_id' => $fleetMissionId,
            '_token' => csrf_token(),
        ]);
        $response->assertStatus(200);

        // Cancel it again
        $response = $this->post('/ajax/fleet/dispatch/recall-fleet', [
            'fleet_mission_id' => $fleetMissionId,
            '_token' => csrf_token(),
        ]);
        // Expecting a 500 error because the mission is already canceled.
        $response->assertStatus(500);

        // The eventbox should only show 1 mission (the first recalled mission).
        $response = $this->get('/ajax/fleet/eventbox/fetch');
        $response->assertStatus(200);
        $response->assertJsonFragment(['friendly' => 1]);
        $response->assertJsonFragment(['eventText' => $this->missionName . ' (R)']);
    }
}
