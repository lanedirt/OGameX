<?php

namespace Tests\Feature\FleetDispatch;

use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\GameMissions\Models\ExpeditionOutcomeType;
use OGame\Models\Resources;
use OGame\Models\Highscore;
use OGame\Services\FleetMissionService;
use OGame\Services\ObjectService;
use OGame\Services\SettingsService;
use Tests\FleetDispatchTestCase;
use Exception;

/**
 * Test that fleet dispatch works as expected for expedition missions.
 */
class FleetDispatchExpeditionTest extends FleetDispatchTestCase
{
    /**
     * @var int The mission type for the test.
     */
    protected int $missionType = 15;

    /**
     * @var string The mission name for the test, displayed in UI.
     */
    protected string $missionName = 'Expedition';

    /**
     * Prepare the planet for the test, so it has the required buildings and research.
     *
     * @return void
     */
    protected function basicSetup(): void
    {
        $this->planetAddUnit('large_cargo', 5000);
        $this->planetAddUnit('espionage_probe', 1);

        // Set astrophysics research level to 1 to allow expeditions.
        $this->playerSetResearchLevel('astrophysics', 1);
        // Set computer technology to a high enough level to allow enough concurrent fleets.
        $this->playerSetResearchLevel('computer_technology', 10);

        // Set the fleet and economy speed to 1x for this test.
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('economy_speed', 1);
        $settingsService->set('fleet_speed_war', 1);
        $settingsService->set('fleet_speed_holding', 1);
        $settingsService->set('fleet_speed_peaceful', 1);
        $this->planetAddResources(new Resources(0, 0, 100000, 0));
    }

    protected function messageCheckMissionArrival(): void
    {
        // Assert that message has been sent to player and contains the correct information.
        $this->assertMessageReceivedAndContains('fleets', 'expeditions', [
            'Expedition report',
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
     * Assert that check request to dispatch fleet to own planet fails with expedition mission.
     */
    public function testFleetCheckToOwnPlanetError(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('large_cargo'), 1);
        $this->fleetCheckToSecondPlanet($unitCollection, false);
    }

    /**
     * Assert that check request to dispatch fleet to planet of other player fails with expedition mission.
     */
    public function testFleetCheckToForeignPlanetError(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('large_cargo'), 1);
        $this->fleetCheckToOtherPlayer($unitCollection, false);
    }

    /**
     * Assert that check request to dispatch fleet to position 16 fails if user has expedition level 0.
     */
    public function testFleetCheckToPosition16Error(): void
    {
        $this->basicSetup();

        // Set astrophysics research level to 0 to disallow expeditions.
        $this->playerSetResearchLevel('astrophysics', 0);

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('large_cargo'), 1);
        $this->fleetCheckToPosition16($unitCollection, false);
    }

    /**
     * Assert that check request to dispatch fleet to position 16 succeeds with expedition mission.
     */
    public function testFleetCheckToPosition16Success(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('large_cargo'), 1);
        $this->fleetCheckToPosition16($unitCollection, true);
    }

    /**
     * Test that max expedition slots restriction works correctly based on astrophysics level.
     */
    public function testMaxExpeditionSlotsRestriction(): void
    {
        $this->basicSetup();

        // Set astrophysics to level 1 (default from basicSetup)
        $this->playerSetResearchLevel('astrophysics', 1);

        // First expedition should succeed
        $this->sendTestExpedition();
        $this->assertEquals(1, $this->planetService->getPlayer()->getExpeditionSlotsInUse(), 'Expedition slots in use should be 1 after first expedition');

        // Second expedition should fail due to max expedition slots restriction
        $this->sendTestExpedition(false);
        $this->assertEquals(1, $this->planetService->getPlayer()->getExpeditionSlotsInUse(), 'Expedition mission has been created but should have been rejected due to max expedition slots restriction (astrophysics level 1)');

        // Upgrade astrophysics to level 4 to allow 2 expedition slots
        $this->playerSetResearchLevel('astrophysics', 4);

        // Now second expedition should succeed
        $this->sendTestExpedition();
        $this->assertEquals(2, $this->planetService->getPlayer()->getExpeditionSlotsInUse(), 'Expedition slots in use should be 2 after second expedition');
    }

    /**
     * Test that current expedition slots in use is calculated correctly.
     */
    public function testCurrentExpeditionSlotsInUse(): void
    {
        $this->basicSetup();

        // Set astrophysics to level 9 to allow 3 expedition slots
        $this->playerSetResearchLevel('astrophysics', 9);

        // Send first expedition
        $this->sendTestExpedition();
        $this->assertEquals(1, $this->planetService->getPlayer()->getExpeditionSlotsInUse(), 'Expedition slots in use should be 1 after first expedition');

        // Send second expedition
        $this->sendTestExpedition();
        $this->assertEquals(2, $this->planetService->getPlayer()->getExpeditionSlotsInUse(), 'Expedition slots in use should be 2 after second expedition');

        // Send third expedition
        $this->sendTestExpedition();
        $this->assertEquals(3, $this->planetService->getPlayer()->getExpeditionSlotsInUse(), 'Expedition slots in use should be 3 after third expedition');

        // Increase time by 10 hours to ensure all expeditions are done
        $this->travel(10)->hours();

        // Do a request to trigger the update logic
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Ensure that the expedition slots in use are updated correctly
        $this->assertEquals(0, $this->planetService->getPlayer()->getExpeditionSlotsInUse(), 'Expedition slots in use should be 0 after all expeditions are done');
    }

    /**
     * Verify that an active mission also shows the (not yet existing) return trip in the fleet event list.
     * @throws Exception
     */
    public function testDispatchFleetReturnShown(): void
    {
        $this->basicSetup();

        // Send the expedition mission.
        $this->sendTestExpedition(true);

        // The eventbox should show 1 mission.
        $response = $this->get('/ajax/fleet/eventbox/fetch');
        $response->assertStatus(200);
        $response->assertJsonFragment(['friendly' => 1]);
        // The event list should show 3 missions (the parent, the parent + arrival time, and the to-be-created return trip)
        $response = $this->get('/ajax/fleet/eventlist/fetch');
        $response->assertStatus(200);

        // We should see the mission name and the return mission name in the event list.
        $response->assertSee($this->missionName);
        $response->assertSee($this->missionName .  ' (R)');

        // Assert that we see two arrival missions (initial arrival and time_holding) and one return mission
        $content = (string)$response->getContent();
        $this->assertNotEmpty($content, 'Fleet event list content is null');
        $this->assertEquals(2, substr_count($content, 'data-return-flight="false"'), 'Should see two parent mission rows');
        $this->assertEquals(1, substr_count($content, 'data-return-flight="true"'), 'Should see one return mission row');

        // Cancel the fleet mission, so it doesn't interfere with other tests.
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionService->cancelMission($fleetMission);
    }

    /**
     * Send an expedition mission to position 16 with only espionage units.
     *
     * @return void
     */
    public function testExpeditionWithOnlyEspionageUnits(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('espionage_probe'), 1);

        // The fleet check should succeed
        $this->fleetCheckToPosition16($unitCollection, true);

        // But the fleet dispatch should fail
        $this->sendMissionToPosition16($unitCollection, new Resources(1, 1, 0, 0), false);
    }

    /**
     * Send an expedition mission expecting failed mission result.
     *
     * @return void
     */
    public function testExpeditionWithFailedResult(): void
    {
        $this->basicSetup();

        // Get the number of ships before the expedition.
        $initialShipCount = $this->planetService->getShipUnits()->getAmount();

        // Enable only the "failed" expedition outcome.
        $this->settingsEnableExpeditionOutcomes([ExpeditionOutcomeType::Failed]);

        // Send the expedition mission.
        $this->sendTestExpedition(true);

        // Wait for the mission to complete.
        $this->travel(10)->hours();

        // Load the planet again to get the latest state.
        $this->get('/overview');
        $this->planetService->reloadPlanet();

        // Assert that we still have the same number of ships on the planet as before we started the expedition.
        $this->assertEquals($initialShipCount, $this->planetService->getShipUnits()->getAmount());

        // Assert that the mission failed.
        $this->assertMessageReceivedAndContains('fleets', 'expeditions', [
            'Expedition Result',
        ]);
    }

    /**
     * Test that expedition return mission timing correctly includes holding time.
     * This verifies the fix for the bug where return trips would complete instantly
     * because the start time was set to parent arrival instead of parent arrival + holding time.
     *
     * @return void
     */
    public function testExpeditionReturnMissionTimingIncludesHoldingTime(): void
    {
        $this->basicSetup();

        // Enable only the "failed" expedition outcome to avoid delay/speedup complications.
        $this->settingsEnableExpeditionOutcomes([ExpeditionOutcomeType::Failed]);

        // Send the expedition mission.
        $this->sendTestExpedition(true);

        // Get the parent mission.
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $parentMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        // Verify the parent mission has holding time set (expeditions should have at least 1 hour).
        $this->assertGreaterThan(0, $parentMission->time_holding, 'Expedition mission should have holding time set');
        $this->assertGreaterThanOrEqual(3600, $parentMission->time_holding, 'Expedition holding time should be at least 1 hour (3600 seconds)');

        // Calculate expected return mission start time (parent arrival + holding time).
        $expectedReturnStartTime = $parentMission->time_arrival + $parentMission->time_holding;
        $parentMissionId = $parentMission->id;
        $travelTime = $parentMission->time_arrival - $parentMission->time_departure;
        $holdingTime = $parentMission->time_holding;

        // Advance time to expedition arrival + holding time + 1 second to ensure arrival is fully processed.
        // This ensures the expedition has completed and return mission has been created.
        $this->travel($travelTime + $holdingTime + 1)->seconds();

        // Trigger update to process the arrival and create the return mission.
        $this->get('/overview');

        // Reload service to get updated missions.
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);

        // Get the return mission that should have been created.
        $returnMission = $fleetMissionService->getFleetMissionByParentId($parentMissionId, false);

        // Verify return mission was created.
        $this->assertNotNull($returnMission, 'Return mission should be created after expedition arrival');

        // Verify return mission start time equals parent arrival + holding time.
        // This is the key assertion - the return mission should start AFTER the holding time.
        $this->assertEquals(
            $expectedReturnStartTime,
            $returnMission->time_departure,
            'Return mission start time should equal parent arrival + holding time (not just arrival time)'
        );

        // Verify return mission arrival time is calculated correctly.
        $expectedReturnArrivalTime = $expectedReturnStartTime + ($parentMission->time_arrival - $parentMission->time_departure);
        $this->assertEquals(
            $expectedReturnArrivalTime,
            $returnMission->time_arrival,
            'Return mission arrival time should be calculated from the correct start time'
        );

        // Verify return mission is not processed (not auto-completed).
        $this->assertEquals(0, $returnMission->processed, 'Return mission should not be auto-completed immediately after creation');
    }

    /**
     * Send an expedition mission with high player points expecting large resource gain.
     *
     * @return void
     */
    public function testExpeditionWithGainResourcesResultHighPoints(): void
    {
        $this->basicSetup();

        // Enable only the "gain resources" expedition outcome.
        $this->settingsEnableExpeditionOutcomes([ExpeditionOutcomeType::GainResources]);

        // Set all existing highscores to 0 for this test.
        Highscore::query()->update(['general' => 0]);

        // Create a highscore record with 100.000.000 points to test max resource find.
        $highscore = Highscore::create([
            'player_id' => $this->planetService->getPlayer()->getId(),
            'general' => 100000000,
        ]);
        $highscore->save();

        // Send the expedition mission with 1000 large cargos.
        $this->sendTestExpedition(true);

        // Get the mission ID.
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $originalMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        // Wait for the mission to complete.
        $this->travel(10)->hours();

        // Load the planet again to get the latest state.
        $this->get('/overview');
        $this->planetService->reloadPlanet();

        // Delete the highscore record to not interfere with other tests.
        $highscore->delete();

        // Get the return trip mission for the original mission
        // and assert that at least one resource type is >= 500000 (adjusted for crystal and deut ratio)
        $returnTripMission = $fleetMissionService->getFleetMissionByParentId($originalMission->id, false);
        $this->assertTrue(
            $returnTripMission->metal >= 500000 || $returnTripMission->crystal >= 333000 || $returnTripMission->deuterium >= 166000,
            'At least one of metal, crystal, or deuterium should be >= 500000 with 100M player points'
        );

        // Assert that the expedition message contains the correct information.
        $this->assertMessageReceivedAndContains('fleets', 'expeditions', [
            'Expedition Result',
            'have been captured',
        ]);
    }

    /**
     * Send an expedition mission with 4 large cargos and high player points expecting exactly 100k resources.
     *
     * @return void
     */
    public function testExpeditionWithGainResourcesResultSmallFleet(): void
    {
        $this->basicSetup();

        // Enable only the "gain resources" expedition outcome.
        $this->settingsEnableExpeditionOutcomes([ExpeditionOutcomeType::GainResources]);

        // Set all existing highscores to 0 for this test.
        Highscore::query()->update(['general' => 0]);

        // Create a highscore record with 100.000.000 points.
        $highscore = Highscore::create([
            'player_id' => $this->planetService->getPlayer()->getId(),
            'general' => 100000000,
        ]);
        $highscore->save();

        // Send the expedition mission with only 4 large cargos.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('large_cargo'), 4);
        $this->sendMissionToPosition16($unitCollection, new Resources(1, 1, 0, 0), true);

        // Get the mission ID.
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $originalMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        // Wait for the mission to complete.
        $this->travel(10)->hours();

        // Load the planet again to get the latest state.
        $this->get('/overview');
        $this->planetService->reloadPlanet();

        // Delete the highscore record to not interfere with other tests.
        $highscore->delete();

        // Get the return trip mission for the original mission
        // and assert that total resources is exactly 100k (limited by cargo capacity)
        $returnTripMission = $fleetMissionService->getFleetMissionByParentId($originalMission->id, false);
        $totalResources = $returnTripMission->metal + $returnTripMission->crystal + $returnTripMission->deuterium;
        $this->assertEquals(
            100000,
            $totalResources,
            'Total resources should be exactly 100k when sending 4 large cargos (cargo capacity limit)'
        );
    }

    /**
     * Send an expedition mission with low player points expecting less than 100k resources.
     *
     * @return void
     */
    public function testExpeditionWithGainResourcesResultLowPoints(): void
    {
        $this->basicSetup();

        // Enable only the "gain resources" expedition outcome.
        $this->settingsEnableExpeditionOutcomes([ExpeditionOutcomeType::GainResources]);

        // Set all existing highscores to 0 for this test.
        Highscore::query()->update(['general' => 0]);

        // Create a highscore record with only 1 point.
        $highscore = Highscore::create([
            'player_id' => $this->planetService->getPlayer()->getId(),
            'general' => 1,
        ]);
        $highscore->save();

        // Send the expedition mission with 1000 large cargos.
        $this->sendTestExpedition(true);

        // Get the mission ID.
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $originalMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        // Wait for the mission to complete.
        $this->travel(10)->hours();

        // Load the planet again to get the latest state.
        $this->get('/overview');
        $this->planetService->reloadPlanet();

        // Delete the highscore record to not interfere with other tests.
        $highscore->delete();

        // Get the return trip mission for the original mission
        // and assert that total resources is less than 100k (limited by low player points)
        $returnTripMission = $fleetMissionService->getFleetMissionByParentId($originalMission->id, false);
        $totalResources = $returnTripMission->metal + $returnTripMission->crystal + $returnTripMission->deuterium;
        $this->assertLessThan(
            100000,
            $totalResources,
            'Total resources should be less than 100k with only 1 player point'
        );
    }

    /**
     * Send an expedition mission expecting failed mission result.
     *
     * @return void
     */
    public function testExpeditionWithGainShipsResult(): void
    {
        $this->basicSetup();

        // Get the number of ships before the expedition.
        $initialShipCount = $this->planetService->getShipUnits()->getAmount();

        // Enable only the "gain ships" expedition outcome.
        $this->settingsEnableExpeditionOutcomes([ExpeditionOutcomeType::GainShips]);

        // Send the expedition mission.
        $this->sendTestExpedition(true);

        // Wait for the mission to complete.
        $this->travel(10)->hours();

        // Load the planet again to get the latest state.
        $this->get('/overview');
        $this->planetService->reloadPlanet();

        // Assert that we now have more ships on the planet than before we started the expedition.
        $this->assertGreaterThan($initialShipCount, $this->planetService->getShipUnits()->getAmount());

        // Assert that the expedition message contains the correct information.
        $this->assertMessageReceivedAndContains('fleets', 'expeditions', [
            'Expedition Result',
            'The following ships are now part of the fleet',
        ]);
    }

    /**
     * Send an expedition mission expecting failed mission result.
     *
     * @return void
     */
    public function testExpeditionWithLossOfFleetResult(): void
    {
        $this->basicSetup();

        // Get the number of ships before the expedition.
        $initialShipCount = $this->planetService->getShipUnits()->getAmount();

        // Enable only the "loss of fleet" expedition outcome.
        $this->settingsEnableExpeditionOutcomes([ExpeditionOutcomeType::LossOfFleet]);

        // Send the expedition mission.
        $this->sendTestExpedition(true);

        // Wait for the mission to complete.
        $this->travel(10)->hours();

        // Load the planet again to get the latest state.
        $this->get('/overview');
        $this->planetService->reloadPlanet();

        // Assert that we now have less ships on the planet than before we started the expedition.
        $this->assertLessThan($initialShipCount, $this->planetService->getShipUnits()->getAmount());

        // Assert that the expedition message contains the correct information.
        $this->assertMessageReceivedAndContains('fleets', 'expeditions', [
            'Expedition Result',
        ]);
    }

    /**
     * Send an expedition mission expecting failed and delay result.
     *
     * @return void
     */
    public function testExpeditionWithFailedAndDelayResult(): void
    {
        $this->basicSetup();

        // Enable only the "failed and delay" expedition outcome.
        $this->settingsEnableExpeditionOutcomes([ExpeditionOutcomeType::FailedAndDelay]);

        // Send the expedition mission.
        $this->sendTestExpedition(true);

        // Get the mission ID.
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $originalMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        // Wait for the mission to complete.
        $this->travel(10)->hours();

        // Load the planet again to get the latest state.
        $this->get('/overview');
        $this->planetService->reloadPlanet();

        // Get the return trip mission for the original mission
        // and assert that the return trip took longer than the original mission.
        $returnTripMission = $fleetMissionService->getFleetMissionByParentId($originalMission->id, false);
        $this->assertGreaterThan(($originalMission->time_arrival - $originalMission->time_departure), $returnTripMission->time_arrival - $returnTripMission->time_departure);

        // Assert that the expedition message contains the correct information.
        $this->assertMessageReceivedAndContains('fleets', 'expeditions', [
            'Expedition Result',
        ]);
    }

    /**
     * Send an expedition mission expecting failed and speedup result.
     *
     * @return void
     */
    public function testExpeditionWithFailedAndSpeedupResult(): void
    {
        $this->basicSetup();

        // Enable only the "failed and speedup" expedition outcome.
        $this->settingsEnableExpeditionOutcomes([ExpeditionOutcomeType::FailedAndSpeedup]);

        // Send the expedition mission.
        $this->sendTestExpedition(true);

        // Get the mission ID.
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $originalMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        // Wait for the mission to complete.
        $this->travel(10)->hours();

        // Load the planet again to get the latest state.
        $this->get('/overview');
        $this->planetService->reloadPlanet();

        // Get the return trip mission for the original mission
        // and assert that the return trip took less time than the original mission.
        $returnTripMission = $fleetMissionService->getFleetMissionByParentId($originalMission->id, false);
        $this->assertLessThan(($originalMission->time_arrival - $originalMission->time_departure), $returnTripMission->time_arrival - $returnTripMission->time_departure);

        // Assert that the expedition message contains the correct information.
        $this->assertMessageReceivedAndContains('fleets', 'expeditions', [
            'Expedition Result',
        ]);
    }

    /**
     * Send an expedition mission to position 16.
     *
     * @param bool $assertStatus
     * @return void
     */
    protected function sendTestExpedition(bool $assertStatus = true): void
    {
        // Send fleet to position 16 for expedition
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('large_cargo'), 1000);
        $this->sendMissionToPosition16($unitCollection, new Resources(1, 1, 0, 0), $assertStatus);
    }

    /**
     * Test that resources sent with expedition are preserved when nothing is found.
     */
    public function testExpeditionPreservesOriginalResources(): void
    {
        $this->basicSetup();

        // Add resources to the planet
        $this->planetAddResources(new Resources(500000, 300000, 200000, 0));

        // Record initial resources
        $initialMetal = $this->planetService->metal()->get();
        $initialCrystal = $this->planetService->crystal()->get();
        $initialDeuterium = $this->planetService->deuterium()->get();

        // Resources to send with the expedition (fleetsave scenario)
        $resourcesToSend = new Resources(100000, 50000, 10000, 0);

        // Send expedition with resources
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('large_cargo'), 100);

        // Enable only the "nothing found" outcome to ensure no additional resources are gained
        $this->settingsEnableExpeditionOutcomes([ExpeditionOutcomeType::Failed]);

        $this->sendMissionToPosition16($unitCollection, $resourcesToSend, true);

        // Get the mission ID
        $fleetMissionService = resolve(FleetMissionService::class);
        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();
        $this->assertCount(1, $activeMissions, 'Expedition mission not created');
        $fleetMissionId = $activeMissions->first()->id;

        // Verify resources were deducted from planet (including fuel)
        $this->get('/overview');
        $this->planetService->reloadPlanet();
        $afterDispatchMetal = $this->planetService->metal()->get();
        $afterDispatchCrystal = $this->planetService->crystal()->get();
        $afterDispatchDeuterium = $this->planetService->deuterium()->get();

        $this->assertLessThan($initialMetal, $afterDispatchMetal, 'Metal should be deducted after dispatch');
        $this->assertLessThan($initialCrystal, $afterDispatchCrystal, 'Crystal should be deducted after dispatch');
        $this->assertLessThan($initialDeuterium, $afterDispatchDeuterium, 'Deuterium should be deducted after dispatch');

        // Advance time to expedition arrival (1 hour holding time + travel time)
        $fleetMission = $fleetMissionService->getFleetMissionById($fleetMissionId, false);
        $travelTime = $fleetMission->time_arrival - $fleetMission->time_departure;
        $holdingTime = $fleetMission->time_holding ?? 0;
        $this->travel($travelTime + $holdingTime + 1)->seconds();

        // Trigger update to process the expedition arrival
        $this->get('/overview');

        // Verify return mission was created
        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();
        $this->assertCount(1, $activeMissions, 'Return mission should be created');
        $returnMission = $activeMissions->first();

        // Verify return mission has original resources
        $this->assertEquals(100000, $returnMission->metal);
        $this->assertEquals(50000, $returnMission->crystal);
        $this->assertEquals(10000, $returnMission->deuterium);

        // Advance time to return mission arrival
        $this->travel($travelTime + 1)->seconds();

        // Trigger update to process the return
        $this->get('/overview');
        $this->planetService->reloadPlanet();

        // Verify resources were returned to the planet
        $finalMetal = $this->planetService->metal()->get();
        $finalCrystal = $this->planetService->crystal()->get();
        $finalDeuterium = $this->planetService->deuterium()->get();

        // The final resources should be approximately equal to initial resources minus fuel consumption
        // We allow some tolerance for fuel consumption
        $this->assertGreaterThan($afterDispatchMetal, $finalMetal, 'Metal should be returned to planet');
        $this->assertGreaterThan($afterDispatchCrystal, $finalCrystal, 'Crystal should be returned to planet');
        $this->assertGreaterThan($afterDispatchDeuterium, $finalDeuterium, 'Deuterium should be returned to planet (minus fuel)');

        // Verify the exact amounts returned match what was sent
        $metalReturned = $finalMetal - $afterDispatchMetal;
        $crystalReturned = $finalCrystal - $afterDispatchCrystal;

        $this->assertEquals(100000, $metalReturned, 'Exact metal amount should be returned');
        $this->assertEquals(50000, $crystalReturned, 'Exact crystal amount should be returned');
    }

    /**
     * Test that expedition preserves original resources and adds found resources.
     */
    public function testExpeditionPreservesOriginalResourcesAndAddsFound(): void
    {
        $this->basicSetup();

        // Add resources to the planet
        $this->planetAddResources(new Resources(500000, 300000, 200000, 0));

        // Resources to send with the expedition
        $resourcesToSend = new Resources(50000, 25000, 5000, 0);

        // Send expedition with resources
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('large_cargo'), 100);

        // Enable only the "gain resources" outcome
        $this->settingsEnableExpeditionOutcomes([ExpeditionOutcomeType::GainResources]);

        $this->sendMissionToPosition16($unitCollection, $resourcesToSend, true);

        // Get the mission ID
        $fleetMissionService = resolve(FleetMissionService::class);
        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();
        $fleetMissionId = $activeMissions->first()->id;

        // Advance time to expedition arrival
        $fleetMission = $fleetMissionService->getFleetMissionById($fleetMissionId, false);
        $travelTime = $fleetMission->time_arrival - $fleetMission->time_departure;
        $holdingTime = $fleetMission->time_holding ?? 0;
        $this->travel($travelTime + $holdingTime + 1)->seconds();

        // Trigger update to process the expedition arrival
        $this->get('/overview');

        // Verify return mission was created
        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();
        $this->assertCount(1, $activeMissions, 'Return mission should be created');
        $returnMission = $activeMissions->first();

        // Verify return mission has at least original resources (should have more from found resources)
        $this->assertGreaterThanOrEqual(50000, $returnMission->metal);
        $this->assertGreaterThanOrEqual(25000, $returnMission->crystal);
        $this->assertGreaterThanOrEqual(5000, $returnMission->deuterium);

        $hasFoundResources = ($returnMission->metal > 50000) ||
                            ($returnMission->crystal > 25000) ||
                            ($returnMission->deuterium > 5000);
        $this->assertTrue($hasFoundResources);
    }

    /**
     * Set the expedition outcomes in the settings service using the weight system.
     * This method ensures only the specified outcomes can occur by setting their weights high
     * and disabling all others.
     * Send an expedition mission expecting merchant found result.
     * Verifies that finding a merchant on expedition:
     * - Calls a random resource trader (metal/crystal/deuterium)
     * - Sends the correct expedition message
     * - Makes the merchant available for trading
     *
     * @return void
     */
    public function testExpeditionWithGainMerchantResult(): void
    {
        $this->basicSetup();

        // Enable only the "merchant trade" expedition outcome
        $this->settingsEnableExpeditionOutcomes([ExpeditionOutcomeType::GainMerchantTrade]);

        // Verify no merchant is active initially
        $player = $this->planetService->getPlayer();
        $this->assertNull(cache()->get('active_merchant_' . $player->getId()));

        // Send the expedition mission
        $this->sendTestExpedition(true);

        // Wait for the mission to complete
        $this->travel(10)->hours();

        // Load the planet again to trigger mission processing
        $this->get('/overview');
        $this->planetService->reloadPlanet();

        // Verify that a merchant was called and is now active (stored in cache for persistence)
        $activeMerchant = cache()->get('active_merchant_' . $player->getId());
        // @phpstan-ignore-next-line - PHPStan doesn't understand cache()->get() can return non-null values
        $this->assertNotNull($activeMerchant, 'Merchant should be active after expedition');
        $this->assertContains(
            $activeMerchant['type'],
            ['metal', 'crystal', 'deuterium'],
            'Expedition should call a resource trader (metal/crystal/deuterium)'
        );
        $this->assertArrayHasKey(
            'trade_rates',
            $activeMerchant,
            'Active merchant should have trade rates'
        );

        // Assert that the expedition message was sent
        // Check that we have at least one expedition merchant message
        $messages = \OGame\Models\Message::where('user_id', $player->getId())
            ->where('key', 'expedition_merchant_found')
            ->get();

        $this->assertGreaterThan(
            0,
            $messages->count(),
            'Expected at least one expedition merchant found message to be sent.'
        );

        // Verify the merchant actually appears in the UI on the resource market page
        $response = $this->get('/merchant/resource-market');
        $response->assertStatus(200);

        // Should show "Already paid" section (not "Call merchant")
        $response->assertSee('Already paid', false);
        $response->assertSee('trade', false);

        // Should have the active merchant highlighted with 'active' class
        $merchantType = $activeMerchant['type'];
        $response->assertSee('data-resource-type="' . $merchantType . '"', false);
    }

    /**
     * Set the expedition outcomes in the settings service.
     *
     * @param array<ExpeditionOutcomeType> $outcomes
     * @return void
     */
    private function settingsEnableExpeditionOutcomes(array $outcomes): void
    {
        $settingsService = resolve(SettingsService::class);

        // Ensure default expedition settings are initialized (bonus slots and multipliers)
        if (!$settingsService->get('bonus_expedition_slots')) {
            $settingsService->set('bonus_expedition_slots', 0);
        }
        if (!$settingsService->get('expedition_reward_multiplier_resources')) {
            $settingsService->set('expedition_reward_multiplier_resources', '1.0');
            $settingsService->set('expedition_reward_multiplier_ships', '1.0');
            $settingsService->set('expedition_reward_multiplier_dark_matter', '1.0');
            $settingsService->set('expedition_reward_multiplier_items', '1.0');
        }

        // Map outcome types to their weight setting keys using enum values as strings
        $weightMapping = [
            ExpeditionOutcomeType::GainDarkMatter->value => 'expedition_weight_dark_matter',
            ExpeditionOutcomeType::GainShips->value => 'expedition_weight_ships',
            ExpeditionOutcomeType::GainResources->value => 'expedition_weight_resources',
            ExpeditionOutcomeType::FailedAndDelay->value => 'expedition_weight_delay',
            ExpeditionOutcomeType::FailedAndSpeedup->value => 'expedition_weight_speedup',
            ExpeditionOutcomeType::Failed->value => 'expedition_weight_nothing',
            ExpeditionOutcomeType::LossOfFleet->value => 'expedition_weight_black_hole',
            ExpeditionOutcomeType::GainMerchantTrade->value => 'expedition_weight_merchant',
            ExpeditionOutcomeType::GainItems->value => 'expedition_weight_merchant', // Items use merchant weight for now
            ExpeditionOutcomeType::BattlePirates->value => 'expedition_weight_pirates',
            ExpeditionOutcomeType::BattleAliens->value => 'expedition_weight_aliens',
        ];

        // Ensure all weights are initialized with their defaults if not already set
        $defaultWeights = [
            'expedition_weight_ships' => '22',
            'expedition_weight_resources' => '32.5',
            'expedition_weight_delay' => '7',
            'expedition_weight_speedup' => '2',
            'expedition_weight_nothing' => '26.5',
            'expedition_weight_black_hole' => '0.3',
            'expedition_weight_dark_matter' => '9',
            'expedition_weight_merchant' => '0.7',
            'expedition_weight_pirates' => '0',
            'expedition_weight_aliens' => '0',
        ];

        foreach ($defaultWeights as $key => $defaultValue) {
            if (!$settingsService->get($key)) {
                $settingsService->set($key, $defaultValue);
            }
        }

        // Set all weights to 0 (disabled) to isolate the test
        foreach ($defaultWeights as $key => $value) {
            $settingsService->set($key, 0);
        }

        // Set weight to 100 for specified outcomes (enabled with high probability)
        foreach ($outcomes as $outcome) {
            if (isset($weightMapping[$outcome->value])) {
                $settingsService->set($weightMapping[$outcome->value], 100);
            }
        }
    }

    /**
     * Test expedition with pirate battle outcome.
     *
     * @return void
     */
    public function testExpeditionWithPirateBattleResult(): void
    {
        $this->basicSetup();

        // Add combat ships for the expedition
        $this->planetAddUnit('battlecruiser', 50);
        $this->planetAddUnit('cruiser', 100);

        // Set some combat tech levels
        $this->playerSetResearchLevel('weapon_technology', 10);
        $this->playerSetResearchLevel('shielding_technology', 10);
        $this->playerSetResearchLevel('armor_technology', 10);

        // Get the number of ships before the expedition
        $initialShipCount = $this->planetService->getShipUnits()->getAmount();

        // Enable only the pirate battle outcome
        $this->settingsEnableExpeditionOutcomes([ExpeditionOutcomeType::BattlePirates]);

        // Send the expedition mission
        $this->sendTestExpedition(true);

        // Wait for the mission to complete
        $this->travel(10)->hours();

        // Load the planet again to get the latest state
        $this->get('/overview');
        $this->planetService->reloadPlanet();

        // Assert that the expedition message contains battle information
        $this->assertMessageReceivedAndContains('fleets', 'expeditions', [
            'Expedition Result',
        ]);

        // Check that a battle report was created
        $battleReports = \OGame\Models\BattleReport::where('planet_user_id', $this->planetService->getPlayer()->getId())->get();
        $this->assertGreaterThan(0, $battleReports->count(), 'Battle report should be created');

        // Verify the battle report has expedition battle markers
        $report = $battleReports->first();
        $this->assertTrue($report->general['expedition_battle'] ?? false, 'Report should be marked as expedition battle');
        $this->assertEquals('pirate', $report->general['npc_type'] ?? '', 'NPC type should be pirate');

        // Verify NPC appears as attacker with correct ID
        $this->assertEquals(-1, $report->attacker['player_id'], 'Pirate player ID should be -1');

        // Verify player appears as defender
        $this->assertEquals($this->planetService->getPlayer()->getId(), $report->defender['player_id']);

        // Verify NPC has lower tech (player tech - 3)
        $this->assertEquals(7, $report->attacker['weapon_technology'], 'Pirates should have player tech - 3');
        $this->assertEquals(7, $report->attacker['shielding_technology'], 'Pirates should have player tech - 3');
        $this->assertEquals(7, $report->attacker['armor_technology'], 'Pirates should have player tech - 3');
    }

    /**
     * Test expedition with alien battle outcome.
     *
     * @return void
     */
    public function testExpeditionWithAlienBattleResult(): void
    {
        $this->basicSetup();

        // Add combat ships for the expedition
        $this->planetAddUnit('battlecruiser', 50);
        $this->planetAddUnit('cruiser', 100);

        // Set some combat tech levels
        $this->playerSetResearchLevel('weapon_technology', 10);
        $this->playerSetResearchLevel('shielding_technology', 10);
        $this->playerSetResearchLevel('armor_technology', 10);

        // Get the number of ships before the expedition
        $initialShipCount = $this->planetService->getShipUnits()->getAmount();

        // Enable only the alien battle outcome
        $this->settingsEnableExpeditionOutcomes([ExpeditionOutcomeType::BattleAliens]);

        // Send the expedition mission
        $this->sendTestExpedition(true);

        // Wait for the mission to complete
        $this->travel(10)->hours();

        // Load the planet again to get the latest state
        $this->get('/overview');
        $this->planetService->reloadPlanet();

        // Assert that the expedition message contains battle information
        $this->assertMessageReceivedAndContains('fleets', 'expeditions', [
            'Expedition Result',
        ]);

        // Check that a battle report was created
        $battleReports = \OGame\Models\BattleReport::where('planet_user_id', $this->planetService->getPlayer()->getId())->get();
        $this->assertGreaterThan(0, $battleReports->count(), 'Battle report should be created');

        // Verify the battle report has expedition battle markers
        $report = $battleReports->first();
        $this->assertTrue($report->general['expedition_battle'] ?? false, 'Report should be marked as expedition battle');
        $this->assertEquals('alien', $report->general['npc_type'] ?? '', 'NPC type should be alien');

        // Verify NPC appears as attacker with correct ID
        $this->assertEquals(-2, $report->attacker['player_id'], 'Alien player ID should be -2');

        // Verify player appears as defender
        $this->assertEquals($this->planetService->getPlayer()->getId(), $report->defender['player_id']);

        // Verify NPC has higher tech (player tech + 3)
        $this->assertEquals(13, $report->attacker['weapon_technology'], 'Aliens should have player tech + 3');
        $this->assertEquals(13, $report->attacker['shielding_technology'], 'Aliens should have player tech + 3');
        $this->assertEquals(13, $report->attacker['armor_technology'], 'Aliens should have player tech + 3');
    }

    /**
     * Test that surviving ships return from battle.
     *
     * @return void
     */
    public function testExpeditionBattleSurvivingShipsReturn(): void
    {
        $this->basicSetup();

        // Add many ships to ensure some survive
        $this->planetAddUnit('battlecruiser', 200);

        // Set high tech to ensure player wins
        $this->playerSetResearchLevel('weapon_technology', 20);
        $this->playerSetResearchLevel('shielding_technology', 20);
        $this->playerSetResearchLevel('armor_technology', 20);

        // Get initial ship count
        $initialBattlecruisers = $this->planetService->getObjectAmount('battlecruiser');

        // Enable only pirate battles (weaker opponent)
        $this->settingsEnableExpeditionOutcomes([ExpeditionOutcomeType::BattlePirates]);

        // Send the expedition mission
        $this->sendTestExpedition(true);

        // Wait for the mission to complete and return
        $this->travel(20)->hours();

        // Load the planet again
        $this->get('/overview');
        $this->planetService->reloadPlanet();

        // Verify ships returned (might be fewer due to losses)
        $finalBattlecruisers = $this->planetService->getObjectAmount('battlecruiser');
        $this->assertGreaterThan(0, $finalBattlecruisers, 'Some ships should return from battle');
    }
}
