<?php

namespace Tests\Unit;

use OGame\GameMissions\BattleEngine\RustBattleEngine;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Services\ObjectService;
use Tests\UnitTestCase;

class RustBattleEngineTest extends UnitTestCase
{
    /**
     * Set up common test components.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Initialize the planet and user tech models with empty data to avoid errors.
        $this->createAndSetPlanetModel([]);
        $this->createAndSetUserTechModel([]);
    }

    /**
     * Test that the starting fleet of the attacker and defender is saved correctly in the battle report.
     */
    public function testAttackerDefenderFleet(): void
    {
        // Create a planet with resources.
        $this->createAndSetPlanetModel([
            'metal' => 100000,
            'crystal' => 100000,
            'deuterium' => 10000,
            'rocket_launcher' => 100,
        ]);

        // Create fleet of attacker player.
        $attackerFleet = new UnitCollection();
        $smallCargo = ObjectService::getUnitObjectByMachineName('small_cargo');
        $attackerFleet->addUnit($smallCargo, 5);
        $lightFighter = ObjectService::getUnitObjectByMachineName('light_fighter');
        $attackerFleet->addUnit($lightFighter, 75);

        // Simulate battle.
        $battleResult = (new RustBattleEngine($attackerFleet, $this->playerService, $this->planetService, $this->settingsService))->simulateBattle();

        // Assert that starting fleets are saved correctly in the battle report.

        // Attacker.
        $this->assertEquals(5, $battleResult->attackerUnitsStart->getAmountByMachineName($smallCargo->machine_name));
        $this->assertEquals(75, $battleResult->attackerUnitsStart->getAmountByMachineName($lightFighter->machine_name));
        // Defender.
        $this->assertEquals(100, $battleResult->defenderUnitsStart->getAmountByMachineName('rocket_launcher'));

        // We expect the defender to win, so the attacker fleet should be reduced to 0.
        $this->assertEquals(0, $battleResult->attackerUnitsResult->getAmountByMachineName($smallCargo->machine_name));
        $this->assertEquals(0, $battleResult->attackerUnitsResult->getAmountByMachineName($lightFighter->machine_name));
        // Defender should have some rocket launchers left but less than 100.
        $this->assertLessThan(100, $battleResult->defenderUnitsResult->getAmountByMachineName('rocket_launcher'));

        // Verify that the attacker fleet unit array entries still exist even though they are set to 0.
        // This is important because the battle report should contain all units that were part of the battle.
        // This should be controlled via UnitCollection $remove_empty_units parameter in remove/subtract methods.
        $this->assertCount(2, $battleResult->attackerUnitsResult->units);
    }
}
