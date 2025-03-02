<?php

namespace Tests\Feature\FleetDispatch;

use Illuminate\Support\Carbon;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Resources;
use OGame\Services\DebrisFieldService;
use OGame\Services\FleetMissionService;
use OGame\Services\ObjectService;
use Tests\FleetDispatchTestCase;

/**
 * Test that fleet dispatch works as expected for recycle (harvest) missions.
 */
class FleetDispatchRecycleTest extends FleetDispatchTestCase
{
    /**
     * @var int The mission type for the test.
     */
    protected int $missionType = 8;

    /**
     * @var string The mission name for the test, displayed in UI.
     */
    protected string $missionName = 'Harvest';

    /**
     * Prepare the planet for the test, so it has the required buildings and research.
     *
     * @return void
     */
    protected function basicSetup(): void
    {
        $this->planetAddUnit('recycler', 5);
        $this->planetAddResources(new Resources(0, 0, 100000, 0));

        // Add debris field to the second planet of the test user.
        $debrisFieldService = resolve(DebrisFieldService::class);
        $debrisFieldService->loadOrCreateForCoordinates($this->secondPlanetService->getPlanetCoordinates());
        $debrisFieldService->appendResources(new Resources(5000, 4000, 3000, 0));
        $debrisFieldService->save();
    }

    protected function messageCheckMissionArrival(): void
    {
        // Assert that message has been sent to player and contains the correct information.
        $this->assertMessageReceivedAndContains('fleets', 'other', [
            'have a total storage capacity of 20,000',
            'are floating in space',
            'You have harvested',
            '5,000 Metal',
            '4,000 Crystal',
            '3,000 Deuterium'
        ]);
    }

    protected function messageCheckMissionReturn(): void
    {
        // Assert that message has been sent to player and contains the correct information.
        $this->assertMessageReceivedAndContains('fleets', 'other', [
            'Your fleet is returning from',
            'Metal:',
            $this->planetService->getPlanetName(),
        ]);
    }

    /**
     * Assert that check request to dispatch fleet to second planet with recycler.
     */
    public function testFleetCheckToSecondPlanetSuccess(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('recycler'), 1);
        $this->fleetCheckToSecondPlanetDebrisField($unitCollection, true);
    }

    /**
     * Assert that check request to dispatch fleet to second planet fails without recycler.
     */
    public function testFleetCheckToSecondPlanetWithoutRecyclerError(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);
        $this->fleetCheckToSecondPlanetDebrisField($unitCollection, false);
    }

    /**
     * Assert that check request to dispatch fleet to first planet position fails with recycler as it has no debris field.
     */
    public function testFleetCheckToFirstPlanetError(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('recycler'), 1);
        $this->fleetCheckToFirstPlanetDebrisField($unitCollection, false);
    }

    /**
     * Verify that dispatching a fleet deducts correct amount of units from planet.
     */
    public function testDispatchFleetDeductUnits(): void
    {
        $this->basicSetup();

        // Assert that we begin with 5 recyclers on the planet.
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'recycler', 5, 'Recycler ships are not at 5 units at beginning of test.');

        // Send fleet to the second planet of the test user.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('recycler'), 1);
        $this->sendMissionToSecondPlanetDebrisField($unitCollection, new Resources(0, 0, 0, 0));

        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'recycler', 4, 'Recycler ship not deducted from planet after fleet dispatch.');
    }

    /**
     * Verify that dispatching a recycle mission launches a return trip and brings back units to origin planet.
     */
    public function testDispatchFleetReturnTrip(): void
    {
        $this->basicSetup();

        // Assert that we begin with 5 recyclers.
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'recycler', 5, 'Recycler ships are not at 5 units at beginning of test.');

        // Send fleet to the debris field located at second planet of the test user.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('recycler'), 1);
        $this->sendMissionToSecondPlanetDebrisField($unitCollection, new Resources(0, 0, 0, 0));

        // Get just dispatched fleet mission ID from database.
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionId = $fleetMission->id;

        // Get time it takes for the fleet to travel to the second planet.
        $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration($this->planetService, $this->secondPlanetService->getPlanetCoordinates(), $unitCollection);

        // Set time to fleet mission duration + 30 seconds (we do 30 instead of 1 second to test later if the return trip start and endtime work as expected
        // and are calculated based on the arrival time instead of the time the job got processed).
        $this->travel($fleetMissionDuration)->seconds();

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

        // Assert that the debris field is now empty after harvesting.
        $debrisFieldService = resolve(DebrisFieldService::class);
        $debrisFieldService->loadOrCreateForCoordinates($this->secondPlanetService->getPlanetCoordinates());
        $this->assertFalse($debrisFieldService->getResources()->any(), 'Debris field still has resources after recyclers have harvested it.');

        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();

        // Assert that a return trip has been launched by checking the active missions for the current planet.
        $this->assertCount(1, $activeMissions, 'No return trip launched after fleet has arrived at destination.');
        $returnMission = $activeMissions->first();
        // Assert that the return mission contains the correct resources.
        $this->assertTrue($returnMission->metal === 5000.0, 'Metal resources are not correct in return trip.');
        $this->assertTrue($returnMission->crystal === 4000.0, 'Crystal resources are not correct in return trip.');
        $this->assertTrue($returnMission->deuterium === 3000.0, 'Deuterium resources are not correct in return trip.');

        // Advance time to the return trip arrival.
        $returnTripDuration = $returnMission->time_arrival - $returnMission->time_departure;
        $this->travel($returnTripDuration + 1)->seconds();

        // Do a request to trigger the update logic.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that the return trip has been processed.
        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();
        $this->assertCount(0, $activeMissions, 'Return trip is not processed after fleet has arrived back at origin planet.');

        // Assert that the units have been returned to the origin planet.
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'recycler', 5, 'Recycler ships are not at 5 units after return trip.');

        $this->messageCheckMissionReturn();
    }

    /**
     * Verify that an active mission also shows the (not yet existing) return trip in the fleet event list.
     */
    public function testDispatchFleetReturnShown(): void
    {
        $this->basicSetup();

        // Send fleet to the second planet of the test user.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('recycler'), 1);
        $this->sendMissionToSecondPlanetDebrisField($unitCollection, new Resources(100, 100, 0, 0));

        // The eventbox should only show 1 mission (the parent).
        $response = $this->get('/ajax/fleet/eventbox/fetch');
        $response->assertStatus(200);
        $response->assertJsonFragment(['friendly' => 1]);

        // The event list should show either 1 or 2 missions (the parent and the to-be-created return trip).
        $response = $this->get('/ajax/fleet/eventlist/fetch');
        $response->assertStatus(200);

        // If the mission has a return mission, we should see both in the event list.
        $response->assertSee($this->missionName);
        $response->assertSee($this->missionName .  ' (R)');
        // Assert that we see both rows in the event list.
        $response->assertSee('data-return-flight="false"', false);
        $response->assertSee('data-return-flight="true"', false);
    }

    /**
     * Verify that dispatching a recycle mission towards a debris field with large resources works as expected
     * and only takes the amount of resources the recyclers can carry.
     */
    public function testDispatchFleetLargeDebrisField(): void
    {
        $this->basicSetup();

        // Make sure the debris field has a lot of resources.
        $debrisFieldService = resolve(DebrisFieldService::class);
        $debrisFieldService->loadForCoordinates($this->secondPlanetService->getPlanetCoordinates());
        $debrisFieldService->appendResources(new Resources(500000, 500000, 500000, 0));
        $debrisFieldService->save();

        $beforeDebrisFieldResources = $debrisFieldService->getResources();

        // Send fleet to the debris field located at second planet of the test user.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('recycler'), 5);
        $this->sendMissionToSecondPlanetDebrisField($unitCollection, new Resources(0, 0, 0, 0));

        // Get just dispatched fleet mission ID from database.
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionId = $fleetMission->id;

        // Get time it takes for the fleet to travel to the second planet.
        $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration($this->planetService, $this->secondPlanetService->getPlanetCoordinates(), $unitCollection);

        // Set time to fleet mission duration + 30 seconds (we do 30 instead of 1 second to test later if the return trip start and endtime work as expected
        // and are calculated based on the arrival time instead of the time the job got processed).
        $this->travel($fleetMissionDuration)->seconds();

        // Do a request to trigger the update logic.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that the fleet mission is processed.
        $fleetMission = $fleetMissionService->getFleetMissionById($fleetMissionId, false);
        $this->assertTrue($fleetMission->processed == 1, 'Fleet mission is not processed after fleet has arrived at destination.');

        // Assert that the debris field contents have been reduced by the amount the recyclers can carry.
        $debrisFieldService = resolve(DebrisFieldService::class);
        $debrisFieldService->loadForCoordinates($this->secondPlanetService->getPlanetCoordinates());

        $this->assertEquals($beforeDebrisFieldResources->metal->get() - 33333.33333333, $debrisFieldService->getResources()->metal->get(), 'Metal resources are not correct in debris field after recyclers have harvested it.');
        $this->assertEquals($beforeDebrisFieldResources->crystal->get() - 33333.33333333, $debrisFieldService->getResources()->crystal->get(), 'Crystal resources are not correct in debris field after recyclers have harvested it.');
        $this->assertEquals($beforeDebrisFieldResources->deuterium->get() - 33333.33333333, $debrisFieldService->getResources()->deuterium->get(), 'Deuterium resources are not correct in debris field after recyclers have harvested it.');

        // Expecting a return trip that will contain the extracted resources.
        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();
        $returnMission = $activeMissions->first();

        // Assert that the return mission contains the correct resources
        // which should be the same as the total capacity of the recyclers.
        $this->assertEquals(33333.0, $returnMission->metal, 'Metal resources are not correct in return trip.');
        $this->assertEquals(33333.0, $returnMission->crystal, 'Crystal resources are not correct in return trip.');
        $this->assertEquals(33333.0, $returnMission->deuterium, 'Deuterium resources are not correct in return trip.');
    }

    /**
     * Verify that dispatching a recycle mission towards a debris field that existed at
     * time of sending fleet but has been emptied by another mission before arriving.
     * This should still work as expected but return 0 resources.
     */
    public function testDispatchFleetEmptiedDebrisField(): void
    {
        $this->basicSetup();

        // Make sure the debris field contains resources when sending the fleet.
        $debrisFieldService = resolve(DebrisFieldService::class);
        $debrisFieldService->loadOrCreateForCoordinates($this->secondPlanetService->getPlanetCoordinates());
        $debrisFieldService->appendResources(new Resources(5000, 4000, 3000, 0));
        $debrisFieldService->save();

        // Send fleet to the debris field located at second planet of the test user.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('recycler'), 5);
        $this->sendMissionToSecondPlanetDebrisField($unitCollection, new Resources(0, 0, 0, 0));

        // Now delete the resources from the debris field to simulate another mission that has harvested it.
        $debrisFieldService->delete();

        // Get just dispatched fleet mission ID from database.
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionId = $fleetMission->id;

        // Get time it takes for the fleet to travel to the second planet.
        $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration($this->planetService, $this->secondPlanetService->getPlanetCoordinates(), $unitCollection);

        // Set time to fleet mission duration + 30 seconds (we do 30 instead of 1 second to test later if the return trip start and endtime work as expected
        // and are calculated based on the arrival time instead of the time the job got processed).
        $this->travel($fleetMissionDuration + 30)->seconds();

        // Do a request to trigger the update logic.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that the fleet mission is processed.
        $fleetMission = $fleetMissionService->getFleetMissionById($fleetMissionId, false);
        $this->assertTrue($fleetMission->processed == 1, 'Fleet mission is not processed after fleet has arrived at destination.');

        // Expecting a return trip that will contain 0 resources.
        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();
        $returnMission = $activeMissions->first();

        // Assert that the return mission contains the correct resources
        // which should be the same as the total capacity of the recyclers.
        $this->assertEquals(0, $returnMission->metal, 'Metal resources are not correct in return trip.');
        $this->assertEquals(0, $returnMission->crystal, 'Crystal resources are not correct in return trip.');
        $this->assertEquals(0, $returnMission->deuterium, 'Deuterium resources are not correct in return trip.');
    }

    /**
     * Verify that canceling/recalling an active mission works.
     */
    public function testDispatchFleetRecallMission(): void
    {
        $this->basicSetup();

        // Add resources for test.
        $this->planetAddResources(new Resources(5000, 5000, 0, 0));

        // Send fleet to the second planet debris field of the test user.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('recycler'), 5);
        $this->sendMissionToSecondPlanetDebrisField($unitCollection, new Resources(0, 0, 0, 0));

        // Get just dispatched fleet mission ID from database.
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);

        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionId = $fleetMission->id;

        // Advance time by 1 minute
        $fleetParentTime = Carbon::getTestNow()->addMinute();
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

        // Assert that the return trip arrival time is exactly 1 minute after the cancelation time.
        // Because the return trip should take exactly as long as the original trip has traveled until it was canceled.
        $this->assertTrue($fleetMission->time_arrival == $fleetParentTime->addSeconds(60)->timestamp, 'Return trip duration is not the same as the original mission has been active.');

        // Advance time by amount of minutes it takes for the return trip to arrive.
        $this->travelTo(Carbon::createFromTimestamp($fleetMission->time_arrival));

        // Do a request to trigger the update logic.
        $this->get('/overview');

        // Assert that the return trip is processed.
        $fleetMission = $fleetMissionService->getFleetMissionById($fleetMissionId, false);
        $this->assertTrue($fleetMission->processed == 1, 'Return trip is not processed after fleet has arrived back at origin planet.');

        // Assert that the units have been returned to the origin planet.
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'recycler', 5, 'Recycler ships are not at original 5 units after recalled trip has been processed.');
    }

    /**
     * Verify that canceling/recalling an active mission twice results in an error.
     */
    public function testDispatchFleetRecallMissionTwiceError(): void
    {
        $this->basicSetup();

        // Send fleet to the second planet of the test user.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('recycler'), 1);
        $this->sendMissionToSecondPlanetDebrisField($unitCollection, new Resources(100, 100, 0, 0));

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

    /**
     * Verify that recycling debris field at current planet coordinates works to make sure
     * that mission from and to with same coordinates is allowed.
     */
    public function testDispatchFleetToCurrentPlanetDebrisField(): void
    {
        $this->basicSetup();

        // Add debris field to the current planet coordinates
        $debrisFieldService = resolve(DebrisFieldService::class);
        $debrisFieldService->loadOrCreateForCoordinates($this->planetService->getPlanetCoordinates());
        $debrisFieldService->appendResources(new Resources(5000, 4000, 3000, 0));
        $debrisFieldService->save();

        // Send fleet to the debris field at current planet coordinates
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('recycler'), 1);
        $this->sendMissionToFirstPlanetDebrisField($unitCollection, new Resources(0, 0, 0, 0));

        // Get mission duration and advance time
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration(
            $this->planetService,
            $this->planetService->getPlanetCoordinates(),
            $unitCollection
        );

        // Advance time and trigger update
        $this->travel($fleetMissionDuration + 1)->seconds();
        $this->get('/overview');

        // Assert debris field is empty after harvesting
        $debrisFieldService->loadForCoordinates($this->planetService->getPlanetCoordinates());
        $this->assertFalse($debrisFieldService->getResources()->any(), 'Debris field still has resources after recyclers have harvested it.');
    }
}
