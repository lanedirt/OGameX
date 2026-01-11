<?php

namespace Tests\Feature;

use OGame\Factories\PlanetServiceFactory;
use OGame\GameMissions\AttackMission;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\BattleReport;
use OGame\Models\Resources;
use OGame\Services\FleetMissionService;
use OGame\Services\ObjectService;
use OGame\Services\SettingsService;
use Tests\FleetDispatchTestCase;

/**
 * Test that missiles (IPMs and ABMs) do not participate in fleet combat.
 *
 * In OGame, missiles should not be part of normal combat:
 * - ABMs only intercept IPMs during missile attacks
 * - IPMs only attack defenses via MissileMission
 */
class MissilesDoNotParticipateInCombatTest extends FleetDispatchTestCase
{
    protected int $missionType = 1;
    protected string $missionName = 'Attack';

    /**
     * Prepare the planet for the test.
     */
    protected function basicSetup(): void
    {
        $this->planetAddUnit('light_fighter', 10);
        $this->playerSetResearchLevel('computer_technology', object_level: 1);

        $settingsService = resolve(SettingsService::class);
        $settingsService->set('economy_speed', 8);
        $settingsService->set('fleet_speed_war', 1);
        $settingsService->set('fleet_speed_holding', 1);
        $settingsService->set('fleet_speed_peaceful', 1);

        $this->planetAddResources(new Resources(0, 0, 1000000, 0));
    }

    /**
     * Test that missiles on the defender planet do not participate in combat
     * and remain intact after the battle.
     */
    public function testMissilesDoNotParticipateInCombat(): void
    {
        $this->basicSetup();

        // Add bombers to attacker planet (bombers are strong against defenses)
        $this->planetAddUnit('bomber', 100);

        // Set 0% repair rate to ensure we can see if defenses are actually destroyed
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('defense_repair_rate', 0);

        // Add a strong attacker fleet (bombers are strong against defenses)
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('bomber'), 100);
        $foreignPlanet = $this->sendMissionToOtherPlayerCleanPlanet($unitCollection, new Resources(0, 0, 0, 0));

        // Add some defenses to foreign planet (these should be destroyed in combat)
        $foreignPlanet->addUnit('rocket_launcher', 50);
        $foreignPlanet->addUnit('light_laser', 25);

        // Add missiles to foreign planet (these should NOT participate in combat)
        $foreignPlanet->addUnit('anti_ballistic_missile', 10);
        $foreignPlanet->addUnit('interplanetary_missile', 5);

        $foreignPlanet->save();

        // Verify initial counts
        $this->assertEquals(50, $foreignPlanet->getObjectAmount('rocket_launcher'), 'Initial rocket launcher count should be 50');
        $this->assertEquals(25, $foreignPlanet->getObjectAmount('light_laser'), 'Initial light laser count should be 25');
        $this->assertEquals(10, $foreignPlanet->getObjectAmount('anti_ballistic_missile'), 'Initial ABM count should be 10');
        $this->assertEquals(5, $foreignPlanet->getObjectAmount('interplanetary_missile'), 'Initial IPM count should be 5');

        // Get mission and advance time to completion
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration(
            $this->planetService,
            $foreignPlanet->getPlanetCoordinates(),
            $unitCollection,
            resolve(AttackMission::class)
        );

        $this->travel($fleetMissionDuration + 1)->seconds();
        $this->reloadApplication();
        $this->playerSetAllMessagesRead();

        // Trigger the battle
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Reload the defender planet
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $foreignPlanetReloaded = $planetServiceFactory->make($foreignPlanet->getPlanetId());

        // Key assertions: missiles should NOT have participated in combat
        // Therefore they should still be at their original counts
        $this->assertEquals(
            10,
            $foreignPlanetReloaded->getObjectAmount('anti_ballistic_missile'),
            'ABMs should not participate in combat and should remain intact'
        );
        $this->assertEquals(
            5,
            $foreignPlanetReloaded->getObjectAmount('interplanetary_missile'),
            'IPMs should not participate in combat and should remain intact'
        );

        // Verify that actual defenses DID participate (most should be destroyed with 100 bombers)
        $totalDefenses = $foreignPlanetReloaded->getObjectAmount('rocket_launcher')
            + $foreignPlanetReloaded->getObjectAmount('light_laser');

        $this->assertLessThan(
            75,
            $totalDefenses,
            'Some defenses should have been destroyed in combat (started with 75)'
        );

        // Get the battle report
        $messageAttacker = \OGame\Models\Message::where('user_id', $this->planetService->getPlayer()->getId())
            ->where('key', 'battle_report')
            ->orderByDesc('id')
            ->first();
        $this->assertNotNull($messageAttacker, 'Attacker should have received a battle report');

        $battleReport = BattleReport::find($messageAttacker->battle_report_id);
        $this->assertNotNull($battleReport, 'Battle report should exist');

        // Verify that missiles are NOT in the defender's battle report units
        $defenderUnits = $battleReport->defender['units'] ?? [];
        $this->assertArrayNotHasKey('anti_ballistic_missile', $defenderUnits,
            'ABMs should not appear in battle report as defender units');
        $this->assertArrayNotHasKey('interplanetary_missile', $defenderUnits,
            'IPMs should not appear in battle report as defender units');

        // Verify that actual defenses DO appear in the battle report
        $this->assertArrayHasKey('rocket_launcher', $defenderUnits,
            'Rocket launchers should appear in battle report as defender units');
        $this->assertArrayHasKey('light_laser', $defenderUnits,
            'Light lasers should appear in battle report as defender units');
    }

    /**
     * Test that when only missiles are present on a planet (no other defenses),
     * the attacker wins without any combat occurring (missiles don't fight back).
     */
    public function testPlanetWithOnlyMissilesHasNoCombat(): void
    {
        $this->basicSetup();

        // Add attacker fleet
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 5);
        $foreignPlanet = $this->sendMissionToOtherPlayerCleanPlanet($unitCollection, new Resources(0, 0, 0, 0));

        // Add ONLY missiles (no other defenses)
        $foreignPlanet->addUnit('anti_ballistic_missile', 20);
        $foreignPlanet->addUnit('interplanetary_missile', 10);
        $foreignPlanet->save();

        // Get mission and advance time to completion
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMissionDuration = $fleetMissionService->calculateFleetMissionDuration(
            $this->planetService,
            $foreignPlanet->getPlanetCoordinates(),
            $unitCollection,
            resolve(AttackMission::class)
        );

        $this->travel($fleetMissionDuration + 1)->seconds();
        $this->reloadApplication();

        // Trigger the battle
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Reload the defender planet
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $foreignPlanetReloaded = $planetServiceFactory->make($foreignPlanet->getPlanetId());

        // Missiles should be untouched
        $this->assertEquals(
            20,
            $foreignPlanetReloaded->getObjectAmount('anti_ballistic_missile'),
            'ABMs should not be destroyed when planet has only missiles'
        );
        $this->assertEquals(
            10,
            $foreignPlanetReloaded->getObjectAmount('interplanetary_missile'),
            'IPMs should not be destroyed when planet has only missiles'
        );

        // Get the battle report
        $messageAttacker = \OGame\Models\Message::where('user_id', $this->planetService->getPlayer()->getId())
            ->where('key', 'battle_report')
            ->orderByDesc('id')
            ->first();
        $this->assertNotNull($messageAttacker, 'Attacker should have received a battle report');

        $battleReport = BattleReport::find($messageAttacker->battle_report_id);
        $this->assertNotNull($battleReport, 'Battle report should exist');

        // Defender should have 0 units in the battle report
        $defenderUnits = $battleReport->defender['units'] ?? [];
        $this->assertEmpty($defenderUnits,
            'Defender should have no units in battle report when only missiles are present');
    }
}
