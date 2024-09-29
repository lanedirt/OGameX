<?php

namespace Tests\Feature\FleetDispatch;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Carbon;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Resources;
use OGame\Services\FleetMissionService;
use Tests\FleetDispatchTestCase;

/**
 * Test that fleet dispatch works as expected for espionage missions.
 */
class FleetDispatchEspionageTest extends FleetDispatchTestCase
{
    /**
     * @var int The mission type for the test.
     */
    protected int $missionType = 6;

    /**
     * @var string The mission name for the test, displayed in UI.
     */
    protected string $missionName = 'Espionage';

    /**
     * Prepare the planet for the test, so it has the required buildings and research.
     *
     * @return void
     */
    protected function basicSetup(): void
    {
        $this->planetAddUnit('espionage_probe', 5);
    }

    protected function messageCheckMissionArrival(): void
    {
        // Assert that message has been sent to player and contains the correct information.
        $this->assertMessageReceivedAndContains('fleets', 'espionage', [
            'Espionage report from',
        ]);
    }

    protected function messageCheckMissionReturn(): void
    {
        // Assert that no message has been sent to player.
        $this->assertMessageNotReceived();
    }

    /**
     * Assert that check request to dispatch fleet to own planet fails with espionage mission.
     */
    public function testFleetCheckToOwnPlanetError(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('espionage_probe'), 1);
        $this->fleetCheckToSecondPlanet($unitCollection, false);
    }

    /**
     * Assert that check request to dispatch fleet to planet of other player succeeds.
     */
    public function testFleetCheckToForeignPlanetSuccess(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('espionage_probe'), 1);
        $this->fleetCheckToOtherPlayer($unitCollection, true);
    }

    /**
     * Assert that check request to dispatch fleet to empty position fails.
     */
    public function testFleetCheckToEmptyPlanetError(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('espionage_probe'), 1);
        $this->fleetCheckToEmptyPosition($unitCollection, false);
    }

    public function testDispatchFleetEspionageReport(): void
    {
        $this->basicSetup();

        // Set time to static time 2024-01-01
        $startTime = Carbon::create(2024, 1, 1, 0, 0, 0);
        Carbon::setTestNow($startTime);

        // Send fleet to a nearby foreign planet.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('espionage_probe'), 1);
        $foreignPlanet = $this->sendMissionToOtherPlayer($unitCollection, new Resources(0, 0, 0, 0));

        // Set all messages as read to avoid unread messages count in the overview.
        $this->playerSetAllMessagesRead();

        // Increase time by 10 hours to ensure the mission is done.
        Carbon::setTestNow($startTime->copy()->addHours(10));

        // Do a request to trigger the update logic.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that espionage report has been sent to player and contains the correct information.
        $this->assertMessageReceivedAndContains('fleets', 'espionage', [
            'Espionage report from',
            $foreignPlanet->getPlanetName()
        ]);
    }

    /**
     * Test that when espionage reaches destination planet, the planet is updated and the updates
     * resources are visible in the espionage report.
     */
    public function testDispatchFleetUpdatePlanet(): void
    {
        $this->basicSetup();

        // Send fleet to a nearby foreign planet.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('espionage_probe'), 1);
        $foreignPlanet = $this->sendMissionToOtherPlayer($unitCollection, new Resources(0, 0, 0, 0));

        // Get current updated timestamp of the target planet.
        $foreignPlanetUpdatedAt = $foreignPlanet->getUpdatedAt();

        // Increase time by 10 hours to ensure the mission is done.
        $currentTime = Carbon::now();
        $currentTime->addHours(10);
        Carbon::setTestNow($currentTime);

        // Do a request to trigger the update logic.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Reload the target planet to get the updated timestamp because
        // the target planet should be updated by the request above which processes the game mission.
        $foreignPlanet->reloadPlanet();

        // Assert that target planet has been updated.
        $this->assertLessThan($foreignPlanet->getUpdatedAt()->timestamp, $foreignPlanetUpdatedAt->timestamp, 'Target planet was not updated after espionage mission has arrived. Check target planet update logic on mission arrival.');
    }

    /**
     * Verify that dispatching a fleet launches a return trip.
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
        $this->assertObjectLevelOnPage($response, 'espionage_probe', 5, 'Espionage probe are not at 5 units at beginning of test.');

        // Send fleet to a nearby foreign planet.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('espionage_probe'), 1);
        $foreignPlanet = $this->sendMissionToOtherPlayer($unitCollection, new Resources(0, 0, 0, 0));

        // Get just dispatched fleet mission ID from database.
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionId = $fleetMission->id;

        // Get time it takes for the fleet to travel to the second planet.
        $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration($this->planetService, $foreignPlanet->getPlanetCoordinates(), $unitCollection);

        // Set time to fleet mission duration + 1 second.
        $fleetParentTime = $startTime->copy()->addSeconds($fleetMissionDuration + 1);
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

        // Assert that a return trip has been launched by checking the active missions for the current planet.
        $this->assertCount(1, $activeMissions, 'No return trip launched after fleet with deployment mission has arrived at destination.');
    }

    /**
     * Verify that an active mission also shows the (not yet existing) return trip in the fleet event list.
     * @throws Exception
     */
    public function testDispatchFleetReturnShown(): void
    {
        $this->basicSetup();

        // Set time to static time 2024-01-01
        $startTime = Carbon::create(2024, 1, 1, 0, 0, 0);
        Carbon::setTestNow($startTime);

        // Send fleet to a nearby foreign planet.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('espionage_probe'), 1);
        $this->sendMissionToOtherPlayer($unitCollection, new Resources(0, 0, 0, 0));

        // The eventbox should only show 1 mission (the parent).
        $response = $this->get('/ajax/fleet/eventbox/fetch');
        $response->assertStatus(200);
        $response->assertJsonFragment(['friendly' => 1]);

        // The event list should show 2 missions (the parent and the to-be-created return trip).
        $response = $this->get('/ajax/fleet/eventlist/fetch');
        $response->assertStatus(200);

        // We should see the mission name and the return mission name in the event list.
        $response->assertSee($this->missionName);
        $response->assertSee($this->missionName .  ' (R)');
        // Assert that we see both the parent and the return mission.
        $response->assertSee('data-return-flight="false"', false);
        $response->assertSee('data-return-flight="true"', false);
    }

    /**
     * Verify that canceling/recalling an active mission works.
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testDispatchFleetRecallMission(): void
    {
        $this->basicSetup();

        // Add resources for test.
        $this->planetAddResources(new Resources(5000, 5000, 0, 0));

        // Set time to static time 2024-01-01
        $startTime = Carbon::create(2024, 1, 1, 0, 0, 0);
        Carbon::setTestNow($startTime);

        // Assert that we begin with 5 small cargo ships on planet.
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'espionage_probe', 5, 'Espionage probes are not at 5 units at beginning of test.');

        // Send fleet to a nearby foreign planet.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('espionage_probe'), 1);
        $foreignPlanet = $this->sendMissionToOtherPlayer($unitCollection, new Resources(0, 0, 0, 0));

        // Get just dispatched fleet mission ID from database.
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionId = $fleetMission->id;

        // Advance time by 5 seconds.
        $fleetParentTime = $startTime->copy()->addSeconds(5);
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

        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionId = $fleetMission->id;
        $fleetMission = $fleetMissionService->getFleetMissionById($fleetMissionId, false);

        // Assert that the return trip arrival time is exactly 10 seconds  after the cancelation time.
        // Because the return trip should take exactly as long as the original trip has traveled until it was canceled.
        $this->assertTrue($fleetMission->time_arrival == $fleetParentTime->addSeconds(5)->timestamp, 'Return trip duration is not the same as the original mission has been active.');

        // Set all messages as read in order to check if we receive the correct messages during return trip process.
        $this->playerSetAllMessagesRead();

        // Advance time by amount of minutes it takes for the return trip to arrive.
        Carbon::setTestNow(Carbon::createFromTimestamp($fleetMission->time_arrival));

        // Do a request to trigger the update logic.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that the return trip is processed.
        $fleetMission = $fleetMissionService->getFleetMissionById($fleetMissionId, false);
        $this->assertTrue($fleetMission->processed == 1, 'Return trip is not processed after fleet has arrived back at origin planet.');

        // Assert that the units have been returned to the origin planet.
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'espionage_probe', 5, 'Small Cargo ships are not at original 5 units after recalled trip has been processed.');

        $this->messageCheckMissionReturn();
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
        $this->assertObjectLevelOnPage($response, 'espionage_probe', 5, 'Espionage probe ships are not at 5 units at beginning of test.');

        // Send fleet to a nearby foreign planet.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('espionage_probe'), 1);
        $foreignPlanet = $this->sendMissionToOtherPlayer($unitCollection, new Resources(0, 0, 0, 0));

        // Get just dispatched fleet mission ID from database.
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionId = $fleetMission->id;

        // Advance time by 10 seconds
        $fleetParentTime = $startTime->copy()->addSeconds(5);
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

    /**
     * Test that the minifleet dispatch method works correctly which is used for shortcut mission
     * buttons such as in the galaxy planet tooltip hover.
     */
    public function testMiniFleetDispatchMethod(): void
    {
        $this->basicSetup();

        $foreignPlanet = $this->getNearbyForeignPlanet();
        $foreignPlanetCoordinates = $foreignPlanet->getPlanetCoordinates();

        // Send a espionage mission through the minifleet endpoint to a nearby foreign planet.
        $post = $this->post('/ajax/fleet/dispatch/send-mini-fleet', [
            'galaxy' => $foreignPlanetCoordinates->galaxy,
            'system' => $foreignPlanetCoordinates->system,
            'position' => $foreignPlanetCoordinates->position,
            'type' => 1,
            'mission' => $this->missionType,
            '_token' => csrf_token(),
        ]);

        $post->assertStatus(200);

        $this->reloadApplication();

        // The eventbox should show the espionage mission.
        $response = $this->get('/ajax/fleet/eventbox/fetch');
        $response->assertStatus(200);
        $response->assertJsonFragment(['friendly' => 1]);

        $this->get('/ajax/fleet/eventlist/fetch')->assertStatus(200);
    }
}
