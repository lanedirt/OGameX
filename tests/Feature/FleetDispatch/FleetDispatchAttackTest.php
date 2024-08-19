<?php

namespace Feature\FleetDispatch;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Carbon;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\BattleReport;
use OGame\Models\Resources;
use OGame\Services\FleetMissionService;
use OGame\Services\SettingsService;
use Tests\FleetDispatchTestCase;

/**
 * Test that fleet dispatch works as expected.
 */
class FleetDispatchAttackTest extends FleetDispatchTestCase
{
    /**
     * @var int The mission type for the test.
     */
    protected int $missionType = 1;

    /**
     * @var string The mission name for the test, displayed in UI.
     */
    protected string $missionName = 'Attack';

    /**
     * Prepare the planet for the test, so it has the required buildings and research.
     *
     * @return void
     */
    protected function basicSetup(): void
    {
        $this->planetAddUnit('light_fighter', 5);

        // Set the fleet speed to 5x for this test.
        $settingsService = app()->make(SettingsService::class);
        $settingsService->set('fleet_speed', 1);
    }

    protected function messageCheckMissionArrival(): void
    {
        // Assert that message has been sent to player and contains the correct information.
        $this->assertMessageReceivedAndContains('fleets', 'combat_reports', [
            'Combat report',
        ]);
    }

    protected function messageCheckMissionReturn(): void
    {
        // Assert that message has been sent to player and contains the correct information.
        $this->assertMessageReceivedAndContains('fleets', 'other', [
            'Your fleet is returning from',
            $this->planetService->getPlanetName(),
        ]);
    }

    /**
     * Assert that check request to dispatch fleet to own planet fails with attack mission.
     */
    public function testFleetCheckToOwnPlanetError(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('light_fighter'), 1);
        $this->fleetCheckToSecondPlanet($unitCollection, false);
    }

    /**
     * Assert that check request to dispatch fleet to planet of other player succeeds.
     */
    public function testFleetCheckToForeignPlanetSuccess(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('light_fighter'), 1);
        $this->fleetCheckToOtherPlayer($unitCollection, true);
    }

    /**
     * Assert that check request to dispatch fleet to empty position fails.
     */
    public function testFleetCheckToEmptyPlanetError(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('light_fighter'), 1);
        $this->fleetCheckToEmptyPosition($unitCollection, false);
    }

    /**
     * Assert that attacking a foreign planet works and results in a battle report.
     */
    public function testDispatchFleetCombatReport(): void
    {
        $this->basicSetup();

        // Set time to static time 2024-01-01
        $startTime = Carbon::create(2024, 1, 1, 0, 0, 0);
        Carbon::setTestNow($startTime);

        // Send fleet to a nearby foreign planet.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('light_fighter'), 1);
        $foreignPlanet = $this->sendMissionToOtherPlayer($unitCollection, new Resources(0, 0, 0, 0));

        // Set all messages as read to avoid unread messages count in the overview.
        $this->playerSetAllMessagesRead();

        // Increase time by 10 hours to ensure the mission is done.
        Carbon::setTestNow($startTime->copy()->addHours(10));

        // Do a request to trigger the update logic.
        $response = $this->get('/overview');
        $response->assertStatus(200);
        
        // Assert that battle report has been sent to player and contains the correct information.
        $this->assertMessageReceivedAndContains('fleets', 'combat_reports', [
            'Combat report',
            $foreignPlanet->getPlanetName()
        ]);
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

        // Assert that we begin with 5 light fighter ships on planet.
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'light_fighter', 5, 'Light fighter are not at 5 units at beginning of test.');

        // Send fleet to a nearby foreign planet.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('light_fighter'), 5);
        $foreignPlanet = $this->sendMissionToOtherPlayer($unitCollection, new Resources(0, 0, 0, 0));

        // Clear any units from foreign planet to ensure we win the battle.
        $foreignPlanet->removeUnits($foreignPlanet->getDefenseUnits(), true);
        $foreignPlanet->removeUnits($foreignPlanet->getShipUnits(), true);

        // Get just dispatched fleet mission ID from database.
        $fleetMissionService = app()->make(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionId = $fleetMission->id;

        // Get time it takes for the fleet to travel to the second planet.
        $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration($this->planetService, $foreignPlanet->getPlanetCoordinates(), $unitCollection);

        // Set time to fleet mission duration + 1 second.
        $fleetParentTime = $startTime->copy()->addSeconds($fleetMissionDuration + 1);
        Carbon::setTestNow($fleetParentTime);

        // Reload application to make sure the defender planet is not cached as we modified it above during test.
        $this->reloadApplication();

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

        // Advance time by amount of minutes it takes for the return trip to arrive.
        Carbon::setTestNow(Carbon::createFromTimestamp($activeMissions->first()->time_arrival));

        // Do a request to trigger the update logic.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that the return trip is processed and that the resources mentioned in battle report
        // match the resources of the return trip.
        $fleetMission = $fleetMissionService->getFleetMissionById($activeMissions->first()->id, false);
        $this->assertTrue($fleetMission->processed == 1, 'Return trip is not processed after fleet has arrived back at origin planet.');

        // Get most recent battle report ID from the database.
        $battleReport = BattleReport::orderBy('id', 'desc')->first();
        $battleReportResources = new Resources($battleReport->loot['metal'], $battleReport->loot['crystal'], $battleReport->loot['deuterium'], 0);

        // Assert that the resources of the return trip match the resources of the battle report.
        $this->assertEquals($battleReportResources, new Resources($fleetMission->metal, $fleetMission->crystal, $fleetMission->deuterium, 0), 'Resources of return trip do not match resources of battle report.');
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
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('light_fighter'), 1);
        $foreignPlanet = $this->sendMissionToOtherPlayer($unitCollection, new Resources(0, 0, 0, 0));

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
        $this->assertObjectLevelOnPage($response, 'light_fighter', 5, 'Light fighter not at 5 units at beginning of test.');

        // Send fleet to a nearby foreign planet.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('light_fighter'), 1);
        $foreignPlanet = $this->sendMissionToOtherPlayer($unitCollection, new Resources(0, 0, 0, 0));

        // Get just dispatched fleet mission ID from database.
        $fleetMissionService = app()->make(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
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

        $fleetMissionService = app()->make(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
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
        $this->assertObjectLevelOnPage($response, 'light_fighter', 5, 'Light Fighter ships are not at original 5 units after recalled trip has been processed.');

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
        $this->assertObjectLevelOnPage($response, 'light_fighter', 5, 'Light fighter not at 5 units at beginning of test.');

        // Send fleet to a nearby foreign planet.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('light_fighter'), 1);
        $foreignPlanet = $this->sendMissionToOtherPlayer($unitCollection, new Resources(0, 0, 0, 0));

        // Get just dispatched fleet mission ID from database.
        $fleetMissionService = app()->make(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
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
     * Assert that units lost during battle are correctly removed in the database for both attacker and defender.
     */
    public function testDispatchFleetCombatUnitsLostRemoved(): void
    {
        // Set time to static time 2024-01-01
        $startTime = Carbon::create(2024, 1, 1, 0, 0, 0);
        Carbon::setTestNow($startTime);

        // Send fleet to a nearby foreign planet.
        // Attack with 150 light fighters, defend with 100 rocket launchers.
        // We expect attacker to win in +/- 4 rounds, while losing 10-50 light fighters.
        $this->planetAddUnit('light_fighter', 200);

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('light_fighter'), 150);
        $foreignPlanet = $this->sendMissionToOtherPlayer($unitCollection, new Resources(0, 0, 0, 0));

        // Clear existing units from foreign planet.
        $foreignPlanet->removeUnits($foreignPlanet->getShipUnits(), true);
        $foreignPlanet->removeUnits($foreignPlanet->getDefenseUnits(), true);
        // Give the foreign planet some units to defend itself.
        $foreignPlanet->addUnit('rocket_launcher', 100);

        // Reload application to make sure the planet is not cached.
        $this->reloadApplication();

        // Increase time by 10 hours to ensure the mission is done.
        Carbon::setTestNow($startTime->copy()->addHours(10));

        // Do a request to trigger the update logic.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that attacker has less than 200 light fighters after battle.
        $this->planetService->reloadPlanet();
        $this->assertLessThan(200, $this->planetService->getObjectAmount('light_fighter'), 'Attacker still has 150 light fighters after battle while it was expected they lost some.');

        // Assert that the defender has lost all units.
        $foreignPlanet->reloadPlanet();
        $this->assertEquals(0, $foreignPlanet->getObjectAmount('rocket_launcher'), 'Defender still has rocket launcher after battle while it was expected they lost all.');
    }

    /**
     * Assert that if attacker loses the battle, no return trip is launched.
     */
    public function testDispatchFleetAttackerLossNoReturnMission(): void
    {
        // Set time to static time 2024-01-01
        $startTime = Carbon::create(2024, 1, 1, 0, 0, 0);
        Carbon::setTestNow($startTime);

        // Send fleet to a nearby foreign planet.
        // Attack with 50 light fighters, defend with 100 rocket launchers.
        // We expect defender to win in +/- 3 rounds. Attacker will lose all units.
        $this->planetAddUnit('light_fighter', 50);

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('light_fighter'), 50);
        $foreignPlanet = $this->sendMissionToOtherPlayer($unitCollection, new Resources(0, 0, 0, 0));

        // Give the foreign planet some units to defend itself.
        $foreignPlanet->addUnit('rocket_launcher', 100);

        // Get just dispatched fleet mission ID from database.
        $fleetMissionService = app()->make(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionId = $fleetMission->id;

        // Get time it takes for the fleet to travel to the second planet.
        $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration($this->planetService, $foreignPlanet->getPlanetCoordinates(), $unitCollection);

        // Set time to fleet mission duration + 1 second.
        $fleetParentTime = $startTime->copy()->addSeconds($fleetMissionDuration + 1);
        Carbon::setTestNow($fleetParentTime);

        // Reload application to make sure the planet is not cached.
        $this->reloadApplication();

        // Do a request to trigger the update logic.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that the fleet mission is processed.
        $fleetMission = $fleetMissionService->getFleetMissionById($fleetMissionId, false);
        $this->assertTrue($fleetMission->processed == 1, 'Fleet mission is not processed after fleet has arrived at destination.');

        // Check that combat message has been received to ensure battle has taken place.
        $this->messageCheckMissionArrival();

        // Assert that NO return trip has been launched by checking the active missions for the current planet.
        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();
        $this->assertCount(0, $activeMissions, 'A return trip is launched while attacker has lost the battle and should not have any units left.');
    }

}
