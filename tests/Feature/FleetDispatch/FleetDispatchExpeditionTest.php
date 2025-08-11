<?php

namespace Tests\Feature\FleetDispatch;

use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\GameMissions\Models\ExpeditionOutcomeType;
use OGame\Models\Resources;
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
        $this->planetAddUnit('light_fighter', 5);
        $this->planetAddUnit('espionage_probe', 1);

        // Set astrophysics research level to 1 to allow expeditions.
        $this->playerSetResearchLevel('astrophysics', 1);
        // Set computer technology to a high enough level to allow enough concurrent fleets.
        $this->playerSetResearchLevel('computer_technology', 10);

        // Set the fleet speed to 1x for this test.
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('economy_speed', 8);
        $settingsService->set('fleet_speed', 1);
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
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1);
        $this->fleetCheckToSecondPlanet($unitCollection, false);
    }

    /**
     * Assert that check request to dispatch fleet to planet of other player fails with expedition mission.
     */
    public function testFleetCheckToForeignPlanetError(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1);
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
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1);
        $this->fleetCheckToPosition16($unitCollection, false);
    }

    /**
     * Assert that check request to dispatch fleet to position 16 succeeds with expedition mission.
     */
    public function testFleetCheckToPosition16Success(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1);
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
     * Send an expedition mission expecting failed mission result.
     *
     * @return void
     */
    public function testExpeditionWithGainResourcesResult(): void
    {
        $this->basicSetup();

        // Enable only the "gain resources" expedition outcome.
        $this->settingsEnableExpeditionOutcomes([ExpeditionOutcomeType::GainResources]);

        // Send the expedition mission.
        $this->sendTestExpedition(true);

        // Wait for the mission to complete.
        $this->travel(10)->hours();

        // Assert that the expedition message contains the correct information.
        $this->assertMessageReceivedAndContains('fleets', 'expeditions', [
            'Expedition Result',
            'have been captured',
        ]);
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
     * Send an expedition mission to position 16.
     *
     * @param bool $assertStatus
     * @return void
     */
    protected function sendTestExpedition(bool $assertStatus = true): void
    {
        // Send fleet to position 16 for expedition
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1);
        $this->sendMissionToPosition16($unitCollection, new Resources(1, 1, 0, 0), $assertStatus);
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

        // Disable all expedition outcomes.
        foreach (ExpeditionOutcomeType::cases() as $outcome) {
            $settingsService->set($outcome->getSettingKey(), 0);
        }

        // Enable the specified outcomes.
        foreach ($outcomes as $outcome) {
            $settingsService->set($outcome->getSettingKey(), 1);
        }
    }
}
