<?php

namespace Tests\Feature\FleetDispatch;

use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\BattleReport;
use OGame\Models\Resources;
use OGame\Services\ObjectService;
use OGame\Services\SettingsService;
use Tests\FleetDispatchTestCase;

/**
 * Test that repaired defenses functionality works correctly in attack missions.
 */
class FleetDispatchAttackRepairedDefensesTest extends FleetDispatchTestCase
{
    protected int $missionType = 1;
    protected string $missionName = 'Attack';

    protected function basicSetup(): void
    {
        $this->playerSetResearchLevel('computer_technology', object_level: 1);

        $settingsService = resolve(SettingsService::class);
        $settingsService->set('economy_speed', 0);
        $settingsService->set('fleet_speed_war', 1);
    }

    protected function messageCheckMissionArrival(): void
    {
        $this->assertMessageReceivedAndContains('fleets', 'combat_reports', [
            'Combat report',
        ]);
    }

    protected function messageCheckMissionReturn(): void
    {
        $this->assertMessageReceivedAndContains('fleets', 'other', [
            'Your fleet is returning from',
        ]);
    }

    /**
     * Test that with 100% repair rate, all destroyed defenses are restored to the planet.
     */
    public function testRepairedDefensesRestoredToPlanet(): void
    {
        $this->basicSetup();

        // Set 100% repair rate for deterministic testing
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('defense_repair_rate', 100);

        // Add units to attacker planet
        $this->planetAddUnit('bomber', 500);
        $this->planetAddResources(new Resources(5000, 5000, 1000000, 0));

        // Send fleet to a foreign planet with defenses
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('bomber'), 500);
        $foreignPlanet = $this->sendMissionToOtherPlayerCleanPlanet($unitCollection, new Resources(0, 0, 0, 0));

        // Add defenses to the foreign planet
        $foreignPlanet->addUnit('rocket_launcher', 100);
        $foreignPlanet->addUnit('light_laser', 50);
        $foreignPlanet->save();

        // Verify initial defense count
        $this->assertEquals(100, $foreignPlanet->getObjectAmount('rocket_launcher'));
        $this->assertEquals(50, $foreignPlanet->getObjectAmount('light_laser'));

        // Increase time to complete the mission
        $this->travel(24)->hours();

        // Reload application
        $this->reloadApplication();

        // Trigger update
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Reload the foreign planet to get updated state
        $foreignPlanet->reloadPlanet();

        // With 100% repair rate, all destroyed defenses should be repaired
        // So the planet should have the same number of defenses as before
        // (surviving + repaired = original)
        $this->assertEquals(
            100,
            $foreignPlanet->getObjectAmount('rocket_launcher'),
            'With 100% repair rate, all rocket launchers should be restored'
        );
        $this->assertEquals(
            50,
            $foreignPlanet->getObjectAmount('light_laser'),
            'With 100% repair rate, all light lasers should be restored'
        );
    }

    /**
     * Test that with 0% repair rate, no defenses are restored.
     */
    public function testNoRepairedDefensesWithZeroRate(): void
    {
        $this->basicSetup();

        // Set 0% repair rate
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('defense_repair_rate', 0);

        // Add units to attacker planet
        $this->planetAddUnit('bomber', 500);
        $this->planetAddResources(new Resources(5000, 5000, 1000000, 0));

        // Send fleet to a foreign planet with defenses
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('bomber'), 500);
        $foreignPlanet = $this->sendMissionToOtherPlayerCleanPlanet($unitCollection, new Resources(0, 0, 0, 0));

        // Add defenses to the foreign planet
        $foreignPlanet->addUnit('rocket_launcher', 100);
        $foreignPlanet->save();

        // Increase time to complete the mission
        $this->travel(24)->hours();

        // Reload application
        $this->reloadApplication();

        // Trigger update
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Reload the foreign planet
        $foreignPlanet->reloadPlanet();

        // With 0% repair rate and strong attacker, all defenses should be destroyed
        $this->assertEquals(
            0,
            $foreignPlanet->getObjectAmount('rocket_launcher'),
            'With 0% repair rate, no rocket launchers should be restored'
        );
    }

    /**
     * Test that battle report contains repaired defenses data.
     */
    public function testBattleReportContainsRepairedDefenses(): void
    {
        $this->basicSetup();

        // Set 100% repair rate for deterministic testing
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('defense_repair_rate', 100);

        // Add units to attacker planet
        $this->planetAddUnit('bomber', 500);
        $this->planetAddResources(new Resources(5000, 5000, 1000000, 0));

        // Send fleet to a foreign planet with defenses
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('bomber'), 500);
        $foreignPlanet = $this->sendMissionToOtherPlayerCleanPlanet($unitCollection, new Resources(0, 0, 0, 0));

        // Add defenses to the foreign planet
        $foreignPlanet->addUnit('rocket_launcher', 100);
        $foreignPlanet->save();

        // Increase time to complete the mission
        $this->travel(24)->hours();

        // Reload application
        $this->reloadApplication();

        // Trigger update
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Get the latest battle report
        $battleReport = BattleReport::orderBy('id', 'desc')->first();
        $this->assertNotNull($battleReport, 'Battle report should exist');

        // Check that repaired_defenses contains the repaired units
        $this->assertNotEmpty(
            $battleReport->repaired_defenses,
            'Battle report should contain repaired defenses'
        );
        $this->assertArrayHasKey(
            'rocket_launcher',
            $battleReport->repaired_defenses,
            'Battle report should contain repaired rocket launchers'
        );
        $this->assertEquals(
            100,
            $battleReport->repaired_defenses['rocket_launcher'],
            'With 100% repair rate, all 100 rocket launchers should be in repaired_defenses'
        );
    }
}
