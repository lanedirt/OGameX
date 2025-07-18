<?php

namespace Tests\Feature\FleetDispatch;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Carbon;
use OGame\Factories\PlanetServiceFactory;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\BattleReport;
use OGame\Models\Enums\PlanetType;
use OGame\Models\Message;
use OGame\Models\Resources;
use OGame\Services\FleetMissionService;
use OGame\Services\ObjectService;
use OGame\Services\SettingsService;
use Tests\FleetDispatchTestCase;
use OGame\Services\DebrisFieldService;

/**
 * Test that fleet dispatch works as expected for attack missions.
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
        $this->playerSetResearchLevel('computer_technology', object_level: 1);

        // Set the fleet speed to 1x for this test.
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('economy_speed', 8);
        $settingsService->set('fleet_speed', 1);
        $this->planetAddResources(new Resources(0, 0, 100000, 0));
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
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1);
        $this->fleetCheckToSecondPlanet($unitCollection, false);
    }

    /**
     * Assert that check request to dispatch fleet to planet of other player succeeds.
     */
    public function testFleetCheckToForeignPlanetSuccess(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1);
        $this->fleetCheckToOtherPlayer($unitCollection, true);
    }

    /**
     * Assert that check request to dispatch fleet to empty position fails.
     */
    public function testFleetCheckToEmptyPlanetError(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1);
        $this->fleetCheckToEmptyPosition($unitCollection, false);
    }

    /**
     * Assert that attacking a foreign planet works and results in a battle report for both the attacker and defender
     * players.
     */
    public function testDispatchFleetCombatReport(): void
    {
        $this->basicSetup();

        // Send fleet to a nearby foreign planet.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1);
        $foreignPlanet = $this->sendMissionToOtherPlayerPlanet($unitCollection, new Resources(0, 0, 0, 0));

        // Set all messages as read to avoid unread messages count in the overview.
        $this->playerSetAllMessagesRead();

        // Increase time by 10 hours to ensure the mission is done.
        $this->travel(10)->hours();

        // Do a request to trigger the update logic.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that battle report has been sent to attacker and contains the correct information.
        $this->assertMessageReceivedAndContains('fleets', 'combat_reports', [
            'Combat report',
            $foreignPlanet->getPlanetName()
        ]);

        // Get battle report message of attacker from database.
        $messageAttacker = Message::where('user_id', $this->planetService->getPlayer()->getId())->where('key', 'battle_report')->orderByDesc('id')->first();
        $this->assertNotNull($messageAttacker, 'Attacker has not received a battle report after combat.');

        // Assert that defender also received a message with the same battle report ID.
        $messageDefender = Message::where('user_id', $foreignPlanet->getPlayer()->getId())->orderByDesc('id')->first();
        if ($messageDefender) {
            $messageDefender = $messageDefender instanceof Message ? $messageDefender : new Message($messageDefender->getAttributes());
            $this->assertEquals($messageAttacker->battle_report_id, $messageDefender->battle_report_id, 'Defender has not received the same battle report as attacker.');
        } else {
            $this->fail('Defender has not received a battle report after combat.');
        }
    }

    /**
     * Assert that attacking a foreign moon works and results in a battle report for both the attacker and defender
     * players.
     */
    public function testDispatchFleetMoonCombatReport(): void
    {
        $this->basicSetup();

        // Send fleet to a nearby foreign moon.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1);
        $foreignMoon = $this->sendMissionToOtherPlayerMoon($unitCollection, new Resources(0, 0, 0, 0));

        // Set all messages as read to avoid unread messages count in the overview.
        $this->playerSetAllMessagesRead();

        // Increase time by 10 hours to ensure the mission is done.
        $this->travel(10)->hours();

        // Do a request to trigger the update logic.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that battle report has been sent to attacker and contains the correct information.
        $this->assertMessageReceivedAndContains('fleets', 'combat_reports', [
            'Combat report',
            $foreignMoon->getPlanetName()
        ]);

        // Get battle report message of attacker from database.
        $messageAttacker = Message::where('user_id', $this->planetService->getPlayer()->getId())->where('key', 'battle_report')->orderByDesc('id')->first();
        $this->assertNotNull($messageAttacker, 'Attacker has not received a battle report after combat.');

        // Assert that defender also received a message with the same battle report ID.
        $messageDefender = Message::where('user_id', $foreignMoon->getPlayer()->getId())->orderByDesc('id')->first();
        if ($messageDefender) {
            $messageDefender = $messageDefender instanceof Message ? $messageDefender : new Message($messageDefender->getAttributes());
            $this->assertEquals($messageAttacker->battle_report_id, $messageDefender->battle_report_id, 'Defender has not received the same battle report as attacker.');
        } else {
            $this->fail('Defender has not received a battle report after combat.');
        }
    }

    /**
     * Verify that dispatching a fleet launches a return trip.
     * @throws Exception
     */
    public function testDispatchFleetReturnTrip(): void
    {
        $this->basicSetup();

        // Assert that we begin with 5 light fighter ships on planet.
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'light_fighter', 5, 'Light fighter are not at 5 units at beginning of test.');

        // Send fleet to a nearby foreign planet.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 5);
        $foreignPlanet = $this->sendMissionToOtherPlayerPlanet($unitCollection, new Resources(0, 0, 0, 0));

        // Clear any units from foreign planet to ensure we win the battle.
        $foreignPlanet->removeUnits($foreignPlanet->getDefenseUnits(), true);
        $foreignPlanet->removeUnits($foreignPlanet->getShipUnits(), true);

        // Get just dispatched fleet mission ID from database.
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionId = $fleetMission->id;

        // Get time it takes for the fleet to travel to the second planet.
        $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration($this->planetService, $foreignPlanet->getPlanetCoordinates(), $unitCollection);

        // Set time to fleet mission duration + 1 second.
        $this->travel($fleetMissionDuration + 1)->seconds();

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
        $this->travelTo(Carbon::createFromTimestamp($activeMissions->first()->time_arrival));

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

        // Send fleet to a nearby foreign planet.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1);
        $this->sendMissionToOtherPlayerPlanet($unitCollection, new Resources(0, 0, 0, 0));

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
     * @throws Exception
     */
    public function testDispatchFleetRecallMission(): void
    {
        $this->basicSetup();

        // Add resources for test.
        $this->planetAddResources(new Resources(5000, 5000, 100000, 0));

        // Assert that we begin with 5 small cargo ships on planet.
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'light_fighter', 5, 'Light fighter not at 5 units at beginning of test.');

        // Send fleet to a nearby foreign planet.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1);
        $this->sendMissionToOtherPlayerPlanet($unitCollection, new Resources(0, 0, 0, 0));

        // Get just dispatched fleet mission ID from database.
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionId = $fleetMission->id;

        // Advance time by 5 seconds.
        $this->travel(5)->seconds();
        $fleetParentTime = Carbon::getTestNow();

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
        // The eventbox should only show 1 mission (the first recalled mission).
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
        $this->travelTo(Carbon::createFromTimestamp($fleetMission->time_arrival));

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

        // Assert that we begin with 5 small cargo ships on planet.
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'light_fighter', 5, 'Light fighter not at 5 units at beginning of test.');

        // Send fleet to a nearby foreign planet.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1);
        $this->sendMissionToOtherPlayerPlanet($unitCollection, new Resources(0, 0, 0, 0));

        // Get just dispatched fleet mission ID from database.
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionId = $fleetMission->id;

        // Advance time by 10 seconds
        $this->travel(10)->seconds();

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
     * Assert that units lost during battle are correctly removed in the database for both attacker and defender and
     * that resources are correctly deducted from the defender planet and added to the attacker planet.
     */
    public function testDispatchFleetCombatUnitsLostAndResourceGained(): void
    {
        // Disable all resource generation in server settings to ensure we're not affected by it
        // when comparing resources before and after battle.
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('economy_speed', 0);

        // Send fleet to a nearby foreign planet.
        // Attack with 200 light fighters, defend with 100 rocket launchers.
        // We expect attacker to win in +/- 4 rounds, while losing 10-50 light fighters.
        $this->planetAddUnit('light_fighter', 200);
        $this->planetAddResources(new Resources(5000, 5000, 1000000, 0));

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 200);
        $foreignPlanet = $this->sendMissionToOtherPlayerCleanPlanet($unitCollection, new Resources(0, 0, 0, 0));

        // Give the foreign planet some units to defend itself.
        $foreignPlanet->addUnit('rocket_launcher', 100);

        // Increase time by 24 hours to ensure the mission is done and fleets have returned.
        $this->travel(24)->hours();

        // Reload application to make sure the planet is not cached.
        $this->reloadApplication();

        // Get amount of resources of the foreign planet before the battle.
        $attackerResourcesBefore = $this->planetService->getResources();
        $foreignPlanetResourcesBefore = $foreignPlanet->getResources();

        // Do a request to trigger the update logic.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that attacker has more than 0 but less than 200 light fighters after battle.
        $this->planetService->reloadPlanet();
        $this->assertGreaterThan(0, $this->planetService->getObjectAmount('light_fighter'), 'Attacker has no light fighters after battle while it was expected some should have survived and returned.');
        $this->assertLessThan(200, $this->planetService->getObjectAmount('light_fighter'), 'Attacker still has 200 light fighters after battle while it was expected they lost some.');

        // Assert that the defender has lost all units.
        $foreignPlanet->reloadPlanet();
        $this->assertEquals(0, $foreignPlanet->getObjectAmount('rocket_launcher'), 'Defender still has rocket launcher after battle while it was expected they lost all.');

        // Assert that the resources of the foreign planet have decreased after the battle, and resources of attacker
        // planet have increased.
        $this->reloadApplication();
        $this->planetService->reloadPlanet();
        $foreignPlanet->reloadPlanet();
        $this->assertLessThan($foreignPlanetResourcesBefore->metal, $foreignPlanet->getResources()->metal, 'Defender still has same amount of metal after battle while it was expected they lost some.');
        $this->assertLessThan($foreignPlanetResourcesBefore->crystal, $foreignPlanet->getResources()->crystal, 'Defender still has same amount of crystal after battle while it was expected they lost some.');

        $this->assertGreaterThan($attackerResourcesBefore->metal, $this->planetService->getResources()->metal, 'Attacker still has same amount of metal after battle while it was expected they gained some.');
        $this->assertGreaterThan($attackerResourcesBefore->crystal, $this->planetService->getResources()->crystal, 'Attacker still has same amount of crystal after battle while it was expected they gained some.');
    }

    /**
     * Assert that if attacker loses the battle, no return trip is launched.
     */
    public function testDispatchFleetAttackerLossNoReturnMission(): void
    {
        // Send fleet to a nearby foreign planet.
        // Attack with 50 light fighters, defend with 100 rocket launchers.
        // We expect defender to win in +/- 3 rounds. Attacker will lose all units.
        $this->planetAddUnit('light_fighter', 50);
        $this->planetAddResources(new Resources(5000, 5000, 100000, 0));

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 50);
        $foreignPlanet = $this->sendMissionToOtherPlayerPlanet($unitCollection, new Resources(0, 0, 0, 0));

        // Give the foreign planet some units to defend itself.
        $foreignPlanet->addUnit('rocket_launcher', 100);

        // Get just dispatched fleet mission ID from database.
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionId = $fleetMission->id;

        // Get time it takes for the fleet to travel to the second planet.
        $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration($this->planetService, $foreignPlanet->getPlanetCoordinates(), $unitCollection);

        // Set time to fleet mission duration + 1 second.
        $this->travel($fleetMissionDuration + 1)->seconds();

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

    /**
     * Assert that when someone is attacking us a warning is shown.
     */
    public function testDispatchFleetUnderAttackWarning(): void
    {
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Check that the class "noAttack" is present in the response which indicates we're not under attack.
        $this->assertStringContainsString('noAttack', (string)$response->getContent(), 'We are under attack while we should not be.');

        // Check that no title warning is shown.
        $this->assertStringNotContainsString('You are under attack!', (string)$response->getContent(), 'You are under attack warning title is shown while we should not be under attack.');

        // Get foreign planet.
        $foreignPlanet = $this->getNearbyForeignPlanet();

        // Add units to foreign planet.
        $foreignPlanet->addUnit('light_fighter', 1);
        $unitsToSend = new UnitCollection();
        $unitsToSend->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1);
        $foreignPlanet->addResources(new Resources(0, 0, 100000, 0));

        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $foreignPlanet->getPlayer()]);
        $fleetMissionService->createNewFromPlanet($foreignPlanet, $this->planetService->getPlanetCoordinates(), PlanetType::Planet, $this->missionType, $unitsToSend, new Resources(0, 0, 0, 0), 10);

        // Check that now we're under attack.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        $this->assertStringNotContainsString('noAttack', (string)$response->getContent(), 'We are not under attack while we should be. Check if the under attack warning works correctly.');
        $this->assertStringContainsString('soon', (string)$response->getContent(), 'We are under attack but no warning is shown. Check if the under attack warning works correctly.');
        $this->assertStringContainsString('You are under attack!', (string)$response->getContent(), 'You are under attack warning title is not shown while we should be under attack. Check if the under attack warning works correctly.');
    }

    /**
     * Assert that a fleet attack mission targeted not towards current planet still gets processed on page load.
     * @throws Exception
     */
    public function testDispatchFleetMissionProcessedNotActivePlanet(): void
    {
        $response = $this->get('/overview');
        $response->assertStatus(200);
        $this->planetAddResources(new Resources(5000, 5000, 100000, 0));

        // Get foreign planet.
        $foreignPlanet = $this->getNearbyForeignPlanet();

        // Add units to foreign planet.
        $foreignPlanet->addUnit('light_fighter', 1);
        $unitsToSend = new UnitCollection();
        $unitsToSend->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1);
        $foreignPlanet->addResources(new Resources(0, 0, 100000, 0));

        // Launch attack from foreign planet to the current players second planet.
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $foreignPlanet->getPlayer()]);

        $fleetMission = $fleetMissionService->createNewFromPlanet($foreignPlanet, $this->planetService->getPlanetCoordinates(), PlanetType::Planet, $this->missionType, $unitsToSend, new Resources(0, 0, 0, 0), 10);

        // Advance time by 24 hours to ensure the mission is done.
        $this->travel(24)->hours();

        // Load overview page to trigger the update logic which should process all fleet missions associated with user,
        // not just the current planet.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that the fleet mission is processed.
        $fleetMission = $fleetMissionService->getFleetMissionById($fleetMission->id, false);
        $this->assertTrue($fleetMission->processed === 1, 'Fleet mission is not processed associated with players second (not-selected) planet.');
    }

    /**
     * Assert that a debris field is created because of the battle where attacker lost (all) ships.
     */
    public function testDispatchFleetAttackerLossDebrisFieldCreated(): void
    {
        // Send fleet to a nearby foreign planet.
        // Attack with 50 light fighters, defend with 200 rocket launchers.
        // We expect defender to win in +/- 3 rounds. Attacker will lose all units.
        $this->planetAddUnit('light_fighter', 50);
        $this->planetAddResources(new Resources(5000, 5000, 100000000, 0));

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 50);
        $foreignPlanet = $this->sendMissionToOtherPlayerPlanet($unitCollection, new Resources(0, 0, 0, 0));

        // Ensure that there is no debris field on the foreign planet.
        $debrisFieldService = resolve(DebrisFieldService::class);
        if ($debrisFieldService->loadForCoordinates($foreignPlanet->getPlanetCoordinates())) {
            $debrisFieldService->delete();
        }

        // Give the foreign planet some units to defend itself.
        $foreignPlanet->addUnit('rocket_launcher', 200);

        // Get just dispatched fleet mission ID from database.
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionId = $fleetMission->id;

        // Get time it takes for the fleet to travel to the second planet.
        $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration($this->planetService, $foreignPlanet->getPlanetCoordinates(), $unitCollection);

        // Set time to fleet mission duration + 1 second.
        $this->travel($fleetMissionDuration + 1)->seconds();

        // Reload application to make sure the planet is not cached.
        $this->reloadApplication();

        // Do a request to trigger the update logic.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that the fleet mission is processed.
        $fleetMission = $fleetMissionService->getFleetMissionById($fleetMissionId, false);
        $this->assertTrue($fleetMission->processed == 1, 'Fleet mission is not processed after fleet has arrived at destination.');

        // Get the battle report
        $battleReport = BattleReport::orderBy('id', 'desc')->first();
        $this->assertNotNull($battleReport, 'Battle report was not created.');

        // Assert that the battle report contains debris field information
        $this->assertNotEmpty($battleReport->debris, 'Battle report does not contain debris field information.');
        $this->assertGreaterThan(0, $battleReport->debris['metal'] + $battleReport->debris['crystal'] + $battleReport->debris['deuterium'], 'Debris field in battle report is empty.');

        // Assert that a debris field was actually created in the database
        $debrisFieldService = resolve(DebrisFieldService::class);
        $debrisFieldExists = $debrisFieldService->loadForCoordinates($foreignPlanet->getPlanetCoordinates());
        $this->assertTrue($debrisFieldExists, 'Debris field was not created in the database.');

        $debrisResources = $debrisFieldService->getResources();
        $this->assertEquals($battleReport->debris['metal'], $debrisResources->metal->get(), 'Debris field metal in database does not match battle report.');
        $this->assertEquals($battleReport->debris['crystal'], $debrisResources->crystal->get(), 'Debris field crystal in database does not match battle report.');
        $this->assertEquals($battleReport->debris['deuterium'], $debrisResources->deuterium->get(), 'Debris field deuterium in database does not match battle report.');
    }

    /**
     * Assert that a battle with a large amount of units works correctly and creates a debris field.
     */
    public function testLargeScaleAttackWithDebrisField(): void
    {
        // Prepare attacker fleet
        $this->planetAddUnit('cruiser', 700000);
        $this->planetAddUnit('battle_ship', 100000);

        $this->planetAddResources(new Resources(5000, 5000, 200000000, 0));

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('cruiser'), 700000);
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('battle_ship'), 100000);

        // Send fleet to a nearby foreign planet
        $foreignPlanet = $this->sendMissionToOtherPlayerPlanet($unitCollection, new Resources(0, 0, 0, 0));

        // Prepare defender units
        $foreignPlanet->addUnit('plasma_turret', 20000);
        $foreignPlanet->addUnit('rocket_launcher', 100000);

        // Ensure that there is no debris field on the foreign planet
        $debrisFieldService = resolve(DebrisFieldService::class);
        if ($debrisFieldService->loadForCoordinates($foreignPlanet->getPlanetCoordinates())) {
            $debrisFieldService->delete();
        }

        // Get fleet mission
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionId = $fleetMission->id;

        // Calculate fleet mission duration
        $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration($this->planetService, $foreignPlanet->getPlanetCoordinates(), $unitCollection);

        // Set time to fleet mission duration + 1 second
        $this->travel($fleetMissionDuration + 1)->seconds();

        // Reload application to make sure the planet is not cached
        $this->reloadApplication();

        // Trigger the update logic
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that the fleet mission is processed without errors
        $fleetMission = $fleetMissionService->getFleetMissionById($fleetMissionId, false);
        $this->assertTrue($fleetMission->processed == 1, 'Large-scale fleet mission was not processed successfully.');

        // Get the battle report
        $battleReport = BattleReport::orderBy('id', 'desc')->first();
        $this->assertNotNull($battleReport, 'Battle report was not created for large-scale attack.');

        // Assert that the battle report contains debris field information
        $this->assertNotEmpty($battleReport->debris, 'Battle report does not contain debris field information for large-scale attack.');
        $this->assertGreaterThan(0, $battleReport->debris['metal'] + $battleReport->debris['crystal'] + $battleReport->debris['deuterium'], 'Debris field in battle report is empty for large-scale attack.');

        // Assert that a debris field was actually created in the database
        $debrisFieldService = resolve(DebrisFieldService::class);
        $debrisFieldExists = $debrisFieldService->loadForCoordinates($foreignPlanet->getPlanetCoordinates());
        $this->assertTrue($debrisFieldExists, 'Debris field was not created in the database for large-scale attack.');

        $debrisResources = $debrisFieldService->getResources();
        $this->assertGreaterThan(0, $debrisResources->metal->get() + $debrisResources->crystal->get() + $debrisResources->deuterium->get(), 'Debris field resources are empty for large-scale attack.');
    }

    /**
     * Assert that a battle with a large amount of debris results in a newly created moon.
     */
    public function testLargeScaleAttackMoonChance(): void
    {
        // Adjust maximum moon chance to 100% to ensure a moon is created.
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('maximum_moon_chance', 100);
        $this->planetAddResources(new Resources(5000, 5000, 1000000, 0));

        // Prepare attacker fleet
        $this->planetAddUnit('cruiser', 2000);

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('cruiser'), 2000);

        // Send fleet to a nearby foreign planet
        $foreignPlanet = $this->sendMissionToOtherPlayerPlanet($unitCollection, new Resources(0, 0, 0, 0));

        // Ensure that foreign planet has no moon. If it already has one, delete it.
        if ($foreignPlanet->hasMoon()) {
            $foreignPlanet->moon()->abandonPlanet();
        }

        // Prepare defender units
        $foreignPlanet->addUnit('rocket_launcher', 100000);

        // Get fleet mission
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionId = $fleetMission->id;

        // Calculate fleet mission duration
        $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration($this->planetService, $foreignPlanet->getPlanetCoordinates(), $unitCollection);

        // Set time to fleet mission duration + 1 second
        $this->travel($fleetMissionDuration + 1)->seconds();

        // Reload application to make sure the planet is not cached
        $this->reloadApplication();

        // Trigger the update logic
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that the fleet mission is processed without errors
        $fleetMission = $fleetMissionService->getFleetMissionById($fleetMissionId, false);
        $this->assertTrue($fleetMission->processed == 1, 'Battle with expected moon creation fleet mission was not processed successfully.');

        // Get the battle report
        $battleReport = BattleReport::orderBy('id', 'desc')->first();
        $this->assertNotNull($battleReport, 'Battle report was not created for battle that should result in moon creation.');
        $this->assertSame(100, $battleReport->general['moon_chance'], 'Battle report does not contain 100% moon chance.');

        $this->assertNotEmpty($battleReport->debris, 'Battle report does not contain debris field information for large-scale attack.');
        $this->assertFalse($battleReport->general['moon_existed'], 'Battle report does not contain correct boolean that moon already existed before battle.');
        $this->assertTrue($battleReport->general['moon_created'], 'Battle report does not contain moon creation information.');

        // Assert that debris field is at least 10M resources combined as every 100k results in 1%
        // So 10M results in 100% moon chance which is required for this test.
        $this->assertGreaterThan(10000000, $battleReport->debris['metal'] + $battleReport->debris['crystal'] + $battleReport->debris['deuterium'], 'Debris field in battle report does not contain at least 10M resources required for 100% moon chance.');

        // Assert that a moon was actually created by attempting to load it.
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $newPlanet = $planetServiceFactory->makeMoonForCoordinate($foreignPlanet->getPlanetCoordinates());

        $this->assertNotNull($newPlanet, 'Battle with 100% moon chance did not result in moon creation.');
    }

    /**
     * Assert that a battle with 100% moon chance but a moon already existing does not create a new moon.
     */
    public function testLargeScaleAttackMoonAlreadyExists(): void
    {
        $planetServiceFactory = resolve(PlanetServiceFactory::class);

        // Adjust maximum moon chance to 100% to ensure a moon is created.
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('maximum_moon_chance', 100);

        // Prepare attacker fleet
        $this->planetAddUnit('cruiser', 2000);
        $this->planetAddResources(new Resources(5000, 5000, 1000000, 0));

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('cruiser'), 2000);

        // Send fleet to a nearby foreign planet
        $foreignPlanet = $this->sendMissionToOtherPlayerPlanet($unitCollection, new Resources(0, 0, 0, 0));

        // Ensure that foreign planet has a moon already. If it doesn't have one yet, we create it.
        if (!$foreignPlanet->hasMoon()) {
            $planetServiceFactory->createMoonForPlanet($foreignPlanet);
        }

        // Prepare defender units
        $foreignPlanet->addUnit('rocket_launcher', 100000);

        // Get fleet mission
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionId = $fleetMission->id;

        // Calculate fleet mission duration
        $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration($this->planetService, $foreignPlanet->getPlanetCoordinates(), $unitCollection);

        // Set time to fleet mission duration + 1 second
        $this->travel($fleetMissionDuration + 1)->seconds();

        // Reload application to make sure the planet is not cached
        $this->reloadApplication();

        // Trigger the update logic
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that the fleet mission is processed without errors
        $fleetMission = $fleetMissionService->getFleetMissionById($fleetMissionId, false);
        $this->assertTrue($fleetMission->processed == 1, 'Battle with expected moon creation fleet mission was not processed successfully.');

        // Get the battle report
        $battleReport = BattleReport::orderBy('id', 'desc')->first();
        $this->assertNotNull($battleReport, 'Battle report was not created for battle that should result in moon creation.');
        $this->assertSame(0, $battleReport->general['moon_chance'], 'Battle report should contain 0 percent moon chance because moon already existed before battle.');
        $this->assertTrue($battleReport->general['moon_existed'], 'Battle report does not contain correct boolean that moon already existed before battle.');
        $this->assertFalse($battleReport->general['moon_created'], 'Battle report does not contain moon creation information.');
    }

    /**
     * Assert that a battle with specific units eventually creates a moon within 100 attempts.
     */
    public function testLargeScaleAttackMoonCreationWithinAttempts(): void
    {
        // Set moon chance to 20%
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('maximum_moon_chance', 20);

        // Track if moon was created in any attempt
        $moonCreated = false;
        $attempts = 0;
        $maxAttempts = 100;

        while (!$moonCreated && $attempts < $maxAttempts) {
            $attempts++;

            // Prepare attacker fleet
            $this->planetAddUnit('light_fighter', 1667);

            $this->planetAddResources(new Resources(5000, 5000, 100000, 0));

            $unitCollection = new UnitCollection();
            $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1667);

            // Send fleet to a nearby foreign planet
            $foreignPlanet = $this->sendMissionToOtherPlayerPlanet($unitCollection, new Resources(0, 0, 0, 0));

            // Ensure that foreign planet has no moon
            if ($foreignPlanet->hasMoon()) {
                $foreignPlanet->moon()->abandonPlanet();
            }

            // Add defender units
            $foreignPlanet->addUnit('rocket_launcher', 20000);

            // Get fleet mission
            $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
            $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
            $fleetMissionId = $fleetMission->id;

            // Calculate fleet mission duration
            $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration(
                $this->planetService,
                $foreignPlanet->getPlanetCoordinates(),
                $unitCollection
            );

            // Set time to fleet mission duration + 1 second
            $this->travel($fleetMissionDuration + 1)->seconds();

            // Reload application to make sure the planet is not cached
            $this->reloadApplication();

            // Trigger the update logic
            $response = $this->get('/overview');
            $response->assertStatus(200);

            // Assert that the fleet mission is processed
            $fleetMission = $fleetMissionService->getFleetMissionById($fleetMissionId, false);
            $this->assertTrue($fleetMission->processed == 1, 'Fleet mission was not processed successfully.');

            // Get the battle report
            $battleReport = BattleReport::orderBy('id', 'desc')->first();
            $this->assertNotNull($battleReport, 'Battle report was not created.');

            // Check if moon was created in this attempt
            if ($battleReport->general['moon_created']) {
                $moonCreated = true;
            }

            // Clean up for next attempt if moon wasn't created
            if (!$moonCreated) {
                // Reset the test state
                $this->reloadApplication();
                // Do a request to get a new CSRF token
                $this->get('/overview');
            }
        }

        // Assert that a moon was created within the maximum attempts
        $this->assertTrue($moonCreated, sprintf('Moon was not created within %d attempts with 20%% maximum moon chance.', $maxAttempts));
        $this->assertLessThanOrEqual($maxAttempts, $attempts, 'Test exceeded maximum allowed attempts.');

        // Output the number of attempts it took to create the moon
        dump("Moon was created after amount of attempts: " . $attempts);
    }

    /**
     * Assert that a debris field can contain large numbers (billions) of resources.
     */
    public function testLargeDebrisFieldCreation(): void
    {
        // Create coordinates for debris field.
        $coordinates = $this->planetService->getPlanetCoordinates();

        // Create large resource amounts (10 billion each).
        $metal = 10000000000;
        $crystal = 10000000000;
        $deuterium = 10000000000;

        // Create debris field with large numbers.
        $debrisFieldService = resolve(DebrisFieldService::class);

        // Load or create debris field.
        $debrisFieldService->loadOrCreateForCoordinates($coordinates);

        // Add resources to debris field.
        $debrisFieldService->appendResources(new Resources($metal, $crystal, $deuterium, 0));
        $debrisFieldService->save();

        // Try to load the debris field again.
        $exists = $debrisFieldService->loadForCoordinates($coordinates);
        $this->assertTrue($exists, 'Large debris field was not created successfully.');

        // Verify that debris field contains at least the amount of resources we added.
        $resources = $debrisFieldService->getResources();
        $this->assertGreaterThanOrEqual($metal, $resources->metal->get(), 'Debris field metal amount does not match expected large value.');
        $this->assertGreaterThanOrEqual($crystal, $resources->crystal->get(), 'Debris field crystal amount does not match expected large value.');
        $this->assertGreaterThanOrEqual($deuterium, $resources->deuterium->get(), 'Debris field deuterium amount does not match expected large value.');
    }

    /**
     * Assert that attacking a planet with negative deuterium still processes correctly.
     */
    public function testDispatchFleetNegativeDeuteriumPlanet(): void
    {
        $this->basicSetup();
        $this->planetAddUnit('light_fighter', 500);

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 500);
        $foreignPlanet = $this->sendMissionToOtherPlayerPlanet($unitCollection, new Resources(0, 0, 0, 0));

        $foreignPlanet->removeUnits($foreignPlanet->getDefenseUnits(), true);
        $foreignPlanet->removeUnits($foreignPlanet->getShipUnits(), true);

        // Add negative deuterium to the planet.
        $foreignPlanet->addResources(new Resources(0, 0, -1000000, 0));

        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration($this->planetService, $foreignPlanet->getPlanetCoordinates(), $unitCollection);

        // Set time to fleet mission duration + 1 second.
        $this->travel($fleetMissionDuration + 1)->seconds();
        $this->reloadApplication();

        // Trigger the update logic.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that the fleet mission is processed.
        $fleetMission = $fleetMissionService->getFleetMissionById($fleetMission->id, false);
        $this->assertTrue($fleetMission->processed == 1);
    }
}
