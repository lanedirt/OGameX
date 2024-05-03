<?php

namespace Tests;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Carbon;
use OGame\GameObjects\Models\UnitCollection;
use OGame\Models\Resources;
use OGame\Services\FleetMissionService;

/**
 * Base class to test that fleet missions work as expected.
 * Extending this class will include basic tests for dispatching fleets for that mission type.
 */
abstract class FleetDispatchSelfTestCase extends FleetDispatchTestCase
{
    /**
     * @var int The mission type for the test.
     */
    protected int $missionType;

    /**
     * @var string The mission name for the test, displayed in UI.
     */
    protected string $missionName;

    /**
     * @var bool Whether the mission has a return mission by default.
     * Note: a mission that can be canceled still requires return mission logic.
     */
    protected bool $hasReturnMission = true;

    /**
     * @var bool Whether the mission is cancelable.
     */
    protected bool $isCancelable = true;

    /**
     * Prepare the planet for the test so it has the required buildings and research.
     *
     * @return void
     * @throws BindingResolutionException
     */
    protected function basicSetup(): void
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

    abstract protected function messageCheckMissionArrival(): void;
    abstract protected function messageCheckMissionReturn(): void;

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
        $this->sendMissionToSecondPlanet($unitCollection, new Resources(100, 100, 0, 0));

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
        $this->sendMissionToSecondPlanet($unitCollection, new Resources(100, 100, 0, 0));

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
        $this->sendMissionToSecondPlanet($unitCollection, new Resources(4500, 100, 0, 0), 500);
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
        $this->sendMissionToSecondPlanet($unitCollection, new Resources(100, 100, 0, 0), 500);
    }

    /**
     * Verify that dispatching a fleet launches a return trip and brings back units to origin planet.
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
        $this->sendMissionToSecondPlanet($unitCollection, new Resources(100, 100, 0, 0));

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
        $fleetMission = $fleetMissionService->getFleetMissionById($fleetMissionId, false);
        $this->assertTrue($fleetMission->processed == 1, 'Fleet mission is not processed after fleet has arrived at destination.');

        // Check that message has been received by calling extended method
        $this->messageCheckMissionArrival();

        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();
        if ($this->hasReturnMission) {
            // Assert that a return trip has been launched by checking the active missions for the current planet.
            $this->assertCount(1, $activeMissions, 'No return trip launched after fleet has arrived at destination.');

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

            $this->messageCheckMissionReturn();
        } else {
            // Assert that NO return trip has been launched by checking the active missions for the current planet.
            $this->assertCount(0, $activeMissions, 'Return trip launched after fleet with deployment mission has arrived at destination.');
        }
    }

    /**
     * Verify that an active mission also shows the (not yet existing) return trip in the fleet event list.
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testDispatchFleetReturnShown(): void
    {
        $this->basicSetup();

        // Set time to static time 2024-01-01
        $startTime = Carbon::create(2024, 1, 1, 0, 0, 0);
        Carbon::setTestNow($startTime);

        // Send fleet to the second planet of the test user.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('small_cargo'), 1);
        $this->sendMissionToSecondPlanet($unitCollection, new Resources(100, 100, 0, 0));

        // The eventbox should only show 1 mission (the parent).
        $response = $this->get('/ajax/fleet/eventbox/fetch');
        $response->assertStatus(200);
        $response->assertJsonFragment(['friendly' => 1]);

        // The event list should show either 1 or 2 missions (the parent and the to-be-created return trip).
        $response = $this->get('/ajax/fleet/eventlist/fetch');
        $response->assertStatus(200);
        if ($this->hasReturnMission) {
            // If the mission has a return mission, we should see both in the event list.
            $response->assertSee($this->missionName);
            $response->assertSee($this->missionName .  ' (R)');
            // Assert that we see both rows in the event list.
            $response->assertSee('data-return-flight="false"', false);
            $response->assertSee('data-return-flight="true"', false);
        } else {
            // If the mission does not have a return mission, we should only see the parent mission.
            $response->assertSee($this->missionName);
            $response->assertDontSee($this->missionName .  ' (R)');
            // Assert that we see only parent row in the event list.
            $response->assertSee('data-return-flight="false"', false);
            $response->assertDontSee('data-return-flight="true"', false);
        }
    }

    /**
     * Verify that canceling/recalling an active mission works.
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testDispatchFleetRecallMission(): void
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
        $this->sendMissionToSecondPlanet($unitCollection, new Resources(100, 100, 0, 0));

        // Get just dispatched fleet mission ID from database.
        $fleetMissionService = app()->make(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionId = $fleetMission->id;

        // Advance time by 1 minute
        $fleetParentTime = $startTime->copy()->addMinutes(1);
        Carbon::setTestNow($fleetParentTime);

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
    }

    /**
     * Verify that canceling/recalling an active mission twice results in an error.
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testDispatchFleetRecallMissionTwiceError(): void
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
        $this->sendMissionToSecondPlanet($unitCollection, new Resources(100, 100, 0, 0));

        // Get just dispatched fleet mission ID from database.
        $fleetMissionService = app()->make(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionId = $fleetMission->id;

        // Advance time by 1 minute
        $fleetParentTime = $startTime->copy()->addMinutes(1);
        Carbon::setTestNow($fleetParentTime);

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
