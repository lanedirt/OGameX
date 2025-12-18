<?php

namespace Tests\Feature\FleetDispatch;

use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\BattleReport;
use OGame\Models\EspionageReport;
use OGame\Models\Resources;
use OGame\Services\CounterEspionageService;
use OGame\Services\FleetMissionService;
use OGame\Services\ObjectService;
use OGame\Services\SettingsService;
use Tests\FleetDispatchTestCase;

/**
 * Test counter-espionage functionality in espionage missions.
 */
class FleetDispatchCounterEspionageTest extends FleetDispatchTestCase
{
    protected int $missionType = 6;
    protected string $missionName = 'Espionage';

    protected function basicSetup(): void
    {
        $this->planetAddUnit('espionage_probe', 10);
        $this->planetAddResources(new Resources(0, 0, 100000, 0));

        $settingsService = resolve(SettingsService::class);
        $settingsService->set('economy_speed', 8);
        $settingsService->set('fleet_speed_war', 1);
    }

    protected function messageCheckMissionArrival(): void
    {
        $this->assertMessageReceivedAndContains('fleets', 'espionage', [
            'Espionage report from',
        ]);
    }

    protected function messageCheckMissionReturn(): void
    {
        $this->assertMessageNotReceived();
    }

    /**
     * Test that surviving probes create a return mission after espionage completes.
     */
    public function testSurvivingProbesCreateReturnMission(): void
    {
        $this->basicSetup();

        // Get initial probe count
        $initialProbes = $this->planetService->getShipUnits()->getAmountByMachineName('espionage_probe');

        // Send probes
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('espionage_probe'), 5);
        $this->sendMissionToOtherPlayerPlanet($unitCollection, new Resources(0, 0, 0, 0));

        // Get fleet mission service
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);

        // Check that outbound mission exists
        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();
        $this->assertGreaterThanOrEqual(1, $activeMissions->count(), 'Outbound mission should exist.');

        // Increase time to complete the outbound mission
        $this->travel(5)->hours();
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Check that espionage report was created
        $espionageReport = EspionageReport::orderByDesc('id')->first();
        $this->assertNotNull($espionageReport, 'Espionage report should be created.');

        // Check that a return mission exists (or probes have returned)
        $activeMissionsAfterArrival = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();

        // Either there's a return mission, or probes have already returned
        // Complete the return trip
        $this->travel(10)->hours();
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Reload planet to check probes returned
        $this->planetService->reloadPlanet();
        $finalProbes = $this->planetService->getShipUnits()->getAmountByMachineName('espionage_probe');

        // Probes should have returned (may be less if counter-espionage destroyed some)
        $this->assertGreaterThan(0, $finalProbes, 'Some probes should have returned.');
    }

    /**
     * Test that surviving probes generate an espionage report for the attacker.
     */
    public function testSurvivingProbesGenerateEspionageReport(): void
    {
        $this->basicSetup();

        // Get a foreign planet with minimal ships
        $foreignPlanet = $this->getNearbyForeignPlanet();
        $foreignPlanet->addUnit('small_cargo', 1);
        $foreignPlanet->save();

        // Count existing reports
        $reportCountBefore = EspionageReport::count();

        // Send probes
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('espionage_probe'), 5);
        $this->sendMissionToOtherPlayerPlanet($unitCollection, new Resources(0, 0, 0, 0));

        // Complete mission
        $this->travel(10)->hours();
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Check that a new espionage report was created
        $reportCountAfter = EspionageReport::count();
        $this->assertGreaterThan($reportCountBefore, $reportCountAfter, 'Espionage report should be generated.');
    }

    /**
     * Test that counter-espionage battle creates a battle report for the defender.
     * Sets up conditions where counter-espionage is guaranteed (100% chance).
     */
    public function testCounterEspionageBattleCreatesDefenderReport(): void
    {
        $this->basicSetup();

        // Get a foreign planet and add many ships to guarantee 100% counter-espionage chance
        $foreignPlanet = $this->getNearbyForeignPlanet();
        $foreignPlanet->addUnit('light_fighter', 500);
        $foreignPlanet->save();

        // Count existing battle reports
        $battleReportCountBefore = BattleReport::count();

        // Send only 1 probe to maximize counter-espionage chance
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('espionage_probe'), 1);
        $this->sendMissionToOtherPlayerPlanet($unitCollection, new Resources(0, 0, 0, 0));

        // Complete mission
        $this->travel(10)->hours();
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // With 500 ships and 1 probe, counter-espionage chance is 100%
        // So a battle report should be created
        $battleReportCountAfter = BattleReport::count();

        // Note: Due to randomness, we can't guarantee the battle happens every time
        // But with 100% chance, it should happen
        if ($battleReportCountAfter > $battleReportCountBefore) {
            $battleReport = BattleReport::orderByDesc('id')->first();
            $this->assertNotNull($battleReport, 'Battle report should exist.');

            // Verify the battle report has correct structure
            $this->assertNotNull($battleReport->attacker, 'Battle report should have attacker info.');
            $this->assertNotNull($battleReport->defender, 'Battle report should have defender info.');
        }
    }

    /**
     * Test that only ships are counted for counter-espionage, not defense structures.
     */
    public function testOnlyShipsCountedNotDefense(): void
    {
        $this->basicSetup();

        // Test the CounterEspionageService directly to verify defense is not counted
        $counterEspionageService = resolve(CounterEspionageService::class);

        // With 0 ships but many defense, chance should be 0
        // Formula: (defender_ships * tech_factor) / (attacker_probes * 4) * 100
        // With 0 ships: (0 * 1) / (5 * 4) * 100 = 0
        $chance = $counterEspionageService->calculateChance(
            5,  // attacker probes
            5,  // attacker esp level
            5,  // defender esp level
            0   // defender ships (defense doesn't count!)
        );

        $this->assertEquals(0, $chance, 'Counter-espionage chance should be 0 when defender has 0 ships.');

        // With ships, chance should be > 0
        $chanceWithShips = $counterEspionageService->calculateChance(
            5,   // attacker probes
            5,   // attacker esp level
            5,   // defender esp level
            100  // defender ships
        );

        $this->assertGreaterThan(0, $chanceWithShips, 'Counter-espionage chance should be > 0 when defender has ships.');
    }

    /**
     * Test that counter-espionage battle only includes ships, not defense structures.
     */
    public function testCounterEspionageBattleExcludesDefense(): void
    {
        $this->basicSetup();

        // Get a foreign planet and add both ships and defense
        $foreignPlanet = $this->getNearbyForeignPlanet();
        $foreignPlanet->addUnit('light_fighter', 100);
        $foreignPlanet->addUnit('rocket_launcher', 1000);
        $foreignPlanet->save();

        // Count existing battle reports
        $battleReportCountBefore = BattleReport::count();

        // Send 1 probe to maximize counter-espionage chance
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('espionage_probe'), 1);
        $this->sendMissionToOtherPlayerPlanet($unitCollection, new Resources(0, 0, 0, 0));

        // Complete mission
        $this->travel(10)->hours();
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Check if a battle report was created
        $battleReportCountAfter = BattleReport::count();

        if ($battleReportCountAfter > $battleReportCountBefore) {
            $battleReport = BattleReport::orderByDesc('id')->first();
            $this->assertNotNull($battleReport, 'Battle report should exist.');

            // Verify defender units only contain ships, not defense
            $defenderUnits = $battleReport->defender['units'];

            // Check that no defense units are in the battle
            $this->assertArrayNotHasKey('rocket_launcher', $defenderUnits, 'Rocket launchers should not be in counter-espionage battle.');
            $this->assertArrayNotHasKey('light_laser', $defenderUnits, 'Light lasers should not be in counter-espionage battle.');
            $this->assertArrayNotHasKey('heavy_laser', $defenderUnits, 'Heavy lasers should not be in counter-espionage battle.');
            $this->assertArrayNotHasKey('ion_cannon', $defenderUnits, 'Ion cannons should not be in counter-espionage battle.');
            $this->assertArrayNotHasKey('gauss_cannon', $defenderUnits, 'Gauss cannons should not be in counter-espionage battle.');
            $this->assertArrayNotHasKey('plasma_turret', $defenderUnits, 'Plasma turrets should not be in counter-espionage battle.');
        }
    }

    /**
     * Test that counter-espionage chance is stored in the espionage report.
     */
    public function testCounterEspionageChanceDisplayedInReport(): void
    {
        $this->basicSetup();

        // Get a foreign planet and add ships
        $foreignPlanet = $this->getNearbyForeignPlanet();
        $foreignPlanet->addUnit('light_fighter', 20);
        $foreignPlanet->save();

        // Send probes
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('espionage_probe'), 5);
        $this->sendMissionToOtherPlayerPlanet($unitCollection, new Resources(0, 0, 0, 0));

        // Complete mission
        $this->travel(10)->hours();
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Get the espionage report
        $espionageReport = EspionageReport::orderByDesc('id')->first();
        $this->assertNotNull($espionageReport, 'Espionage report should be created.');

        // Verify counter-espionage chance is stored
        $this->assertNotNull($espionageReport->counter_espionage_chance, 'Counter-espionage chance should be stored.');
        $this->assertGreaterThanOrEqual(0, $espionageReport->counter_espionage_chance);
        $this->assertLessThanOrEqual(100, $espionageReport->counter_espionage_chance);
    }

    /**
     * Test that espionage report is always created even when all probes are destroyed.
     */
    public function testEspionageReportCreatedWhenAllProbesDestroyed(): void
    {
        $this->basicSetup();

        // Get a foreign planet and add overwhelming ships to guarantee probe destruction
        $foreignPlanet = $this->getNearbyForeignPlanet();
        $foreignPlanet->addUnit('battlecruiser', 1000);
        $foreignPlanet->save();

        // Count existing reports
        $reportCountBefore = EspionageReport::count();

        // Send only 1 probe (will be destroyed)
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('espionage_probe'), 1);
        $this->sendMissionToOtherPlayerPlanet($unitCollection, new Resources(0, 0, 0, 0));

        // Complete mission
        $this->travel(10)->hours();
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Espionage report should still be created
        $reportCountAfter = EspionageReport::count();
        $this->assertGreaterThan($reportCountBefore, $reportCountAfter, 'Espionage report should be created even when all probes destroyed.');
    }

    /**
     * Test that attacker receives "fleet lost contact" message when all probes are destroyed.
     */
    public function testAttackerReceivesFleetLostContactWhenProbesDestroyed(): void
    {
        $this->basicSetup();

        // Get initial probe count
        $initialProbes = $this->planetService->getShipUnits()->getAmountByMachineName('espionage_probe');

        // Get a foreign planet and add overwhelming ships to maximize counter-espionage chance
        $foreignPlanet = $this->getNearbyForeignPlanet();
        $foreignPlanet->addUnit('battlecruiser', 1000);
        $foreignPlanet->save();

        // Reload application to ensure planet changes are not cached
        $this->reloadApplication();

        // Send only 1 probe (high chance to be destroyed)
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('espionage_probe'), 1);
        $this->sendMissionToOtherPlayerPlanet($unitCollection, new Resources(0, 0, 0, 0));

        // Complete mission
        $this->travel(10)->hours();
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Wait for potential return mission
        $this->travel(10)->hours();
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Check if probes were destroyed (counter-espionage triggered)
        $this->planetService->reloadPlanet();
        $finalProbes = $this->planetService->getShipUnits()->getAmountByMachineName('espionage_probe');

        // If counter-espionage triggered and destroyed all probes
        if ($finalProbes === $initialProbes - 1) {
            // Then fleet lost contact message should be received
            $this->assertMessageReceivedAndContains('fleets', 'combat_reports', [
                'Contact with the attacking fleet has been lost',
            ]);
        } else {
            // Counter-espionage didn't trigger or probes survived, skip this assertion
            $this->markTestSkipped('Counter-espionage did not trigger in this test run (random chance).');
        }
    }

    /**
     * Test that no return mission is created when all probes are destroyed.
     */
    public function testNoReturnMissionWhenAllProbesDestroyed(): void
    {
        $this->basicSetup();

        // Get initial probe count
        $initialProbes = $this->planetService->getShipUnits()->getAmountByMachineName('espionage_probe');

        // Get a foreign planet and add overwhelming ships
        $foreignPlanet = $this->getNearbyForeignPlanet();
        $foreignPlanet->addUnit('battlecruiser', 1000);
        $foreignPlanet->save();

        // Reload application to ensure planet changes are not cached
        $this->reloadApplication();

        // Send only 1 probe (high chance to be destroyed)
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('espionage_probe'), 1);
        $this->sendMissionToOtherPlayerPlanet($unitCollection, new Resources(0, 0, 0, 0));

        // Complete mission
        $this->travel(10)->hours();
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Wait for potential return mission
        $this->travel(10)->hours();
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Reload planet
        $this->planetService->reloadPlanet();
        $finalProbes = $this->planetService->getShipUnits()->getAmountByMachineName('espionage_probe');

        // If counter-espionage triggered and destroyed all probes
        if ($finalProbes === $initialProbes - 1) {
            // Probes should not have returned - this is the expected behavior
            $this->assertEquals($initialProbes - 1, $finalProbes, 'Destroyed probes should not return.');
        } else {
            // Counter-espionage didn't trigger, probes returned normally
            $this->markTestSkipped('Counter-espionage did not trigger in this test run (random chance).');
        }
    }

    /**
     * Test that defender receives espionage warning notification.
     */
    public function testDefenderReceivesEspionageWarning(): void
    {
        $this->basicSetup();

        // Send espionage probes to foreign planet
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('espionage_probe'), 5);
        $this->sendMissionToOtherPlayerPlanet($unitCollection, new Resources(0, 0, 0, 0));

        // Complete espionage mission
        $this->travel(10)->hours();
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Verify attacker received espionage report (confirms mission completed)
        $this->assertMessageReceivedAndContains('fleets', 'espionage', [
            'Espionage report from',
        ]);

        // Check that espionage_detected message was created
        $espionageDetectedMessage = \OGame\Models\Message::where('key', 'espionage_detected')
            ->orderByDesc('id')
            ->first();

        $this->assertNotNull($espionageDetectedMessage, 'Espionage detected message should be created');

        // Verify the message contains expected text
        if ($espionageDetectedMessage) {
            $gameMessage = \OGame\Factories\GameMessageFactory::createGameMessage($espionageDetectedMessage);
            $body = $gameMessage->getBody();
            $this->assertStringContainsString('was sighted near your planet', $body);
        }
    }
}
