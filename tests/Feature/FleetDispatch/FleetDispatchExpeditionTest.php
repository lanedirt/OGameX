<?php

namespace Tests\Feature\FleetDispatch;

use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\GameMissions\Models\ExpeditionOutcomeType;
use OGame\Models\Resources;
use OGame\Services\ObjectService;
use OGame\Services\SettingsService;
use Tests\FleetDispatchTestCase;

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
    public function testExpeditionWithFailedMissionResult(): void
    {
        $this->basicSetup();

        // Enable only the "failed" expedition outcome.
        $this->settingsEnableExpeditionOutcomes([ExpeditionOutcomeType::Failed]);

        // Send the expedition mission.
        $this->sendTestExpedition(true);

        // Wait for the mission to complete.
        $this->travel(10)->hours();

        // Assert that the mission failed.
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
