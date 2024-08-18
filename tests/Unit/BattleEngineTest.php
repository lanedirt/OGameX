<?php

namespace Tests\Unit;

use Illuminate\Contracts\Container\BindingResolutionException;
use OGame\GameMissions\BattleEngine\BattleEngine;
use OGame\GameObjects\Models\Units\UnitCollection;
use Tests\UnitTestCase;

class BattleEngineTest extends UnitTestCase
{
    /**
     * Set up common test components.
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpPlanetService();

        // Initialize the planet and user tech models with empty data to avoid errors.
        $this->createAndSetPlanetModel([]);
        $this->createAndSetUserTechModel([]);
    }

    /**
     * Test that the loot gained from a battle is calculated correctly when the attacker ships
     * have enough cargo space.
     */
    public function testLootGained(): void
    {
        // Create a planet with resources.
        $this->createAndSetPlanetModel([
            'metal' => 10000,
            'crystal' => 5000,
            'deuterium' => 0,
        ]);

        // Create fleet of attacker player.
        $attackerFleet = new UnitCollection();
        $smallCargo = $this->planetService->objects->getUnitObjectByMachineName('small_cargo');
        $attackerFleet->addUnit($smallCargo, 5);

        // Simulate battle.
        $battleEngine = new BattleEngine($attackerFleet, $this->playerService, $this->planetService);
        $battleResult = $battleEngine->simulateBattle();

        // Verify that the gained loot is calculated correctly.
        $this->assertEquals(5000, $battleResult->loot->metal->get());
        $this->assertEquals(2500, $battleResult->loot->crystal->get());
        $this->assertEquals(0, $battleResult->loot->deuterium->get());
    }

    /**
     * Test that the loot gained from a battle is calculated correctly when attacker fleet
     * does not have enough cargo space to take all resources. Test with only metal and crystal.
     */
    public function testLootGainedCapacityConstraintMetalCrystal(): void
    {
        // Create a planet with resources.
        $this->createAndSetPlanetModel([
            'metal' => 100000,
            'crystal' => 100000,
            'deuterium' => 0,
        ]);

        // Create fleet of attacker player.
        $attackerFleet = new UnitCollection();
        $smallCargo = $this->planetService->objects->getUnitObjectByMachineName('small_cargo');
        $attackerFleet->addUnit($smallCargo, 5);

        // Simulate battle.
        $battleEngine = new BattleEngine($attackerFleet, $this->playerService, $this->planetService);
        $battleResult = $battleEngine->simulateBattle();

        // Verify that the gained loot is calculated correctly.
        // 5 cargos have 25k capacity, total potential loot is 100k but in this case
        // it should be limited to 12.5k metal and 12.5k crystal.
        $this->assertEquals(12500, $battleResult->loot->metal->get());
        $this->assertEquals(12500, $battleResult->loot->crystal->get());
        $this->assertEquals(0, $battleResult->loot->deuterium->get());
    }

    /**
     * Test that the loot gained from a battle is calculated correctly when attacker fleet
     *  does not have enough cargo space to take all resources. Test with all resources.
     */
    public function testLootGainedCapacityConstraintMetalCrystalDeuterium(): void
    {
        // Create a planet with resources.
        $this->createAndSetPlanetModel([
            'metal' => 100000,
            'crystal' => 100000,
            'deuterium' => 10000,
        ]);

        // Create fleet of attacker player.
        $attackerFleet = new UnitCollection();
        $smallCargo = $this->planetService->objects->getUnitObjectByMachineName('small_cargo');
        $attackerFleet->addUnit($smallCargo, 5);

        // Simulate battle.
        $battleEngine = new BattleEngine($attackerFleet, $this->playerService, $this->planetService);
        $battleResult = $battleEngine->simulateBattle();

        // Verify that the gained loot is calculated correctly.
        // 5 cargos have 25k capacity, total potential loot is 100k but in this case
        // it should be limited to 10k metal, 10k crystal and 5k deuterium.
        $this->assertEquals(10000, $battleResult->loot->metal->get());
        $this->assertEquals(10000, $battleResult->loot->crystal->get());
        $this->assertEquals(5000, $battleResult->loot->deuterium->get());
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
        $smallCargo = $this->planetService->objects->getUnitObjectByMachineName('small_cargo');
        $attackerFleet->addUnit($smallCargo, 5);
        $lightFighter = $this->planetService->objects->getUnitObjectByMachineName('light_fighter');
        $attackerFleet->addUnit($lightFighter, 10);

        // Simulate battle.
        $battleEngine = new BattleEngine($attackerFleet, $this->playerService, $this->planetService);
        $battleResult = $battleEngine->simulateBattle();

        // Assert that starting fleets are saved correctly in the battle report.

        // Attacker.
        $this->assertEquals(5, $battleResult->attackerUnitsStart->getAmountByMachineName($smallCargo->machine_name));
        $this->assertEquals(10, $battleResult->attackerUnitsStart->getAmountByMachineName($lightFighter->machine_name));
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

    /**
     * Test that the research levels of the attacker and defender are saved correctly in the battle report.
     */
    public function testAttackerDefenderResearchLevels(): void
    {
        // Create a planet with resources.
        $this->createAndSetPlanetModel([
            'metal' => 100000,
            'crystal' => 100000,
            'deuterium' => 10000,
            'rocket_launcher' => 20,
        ]);
        $this->createAndSetUserTechModel([
            'weapon_technology' => 5,
            'shielding_technology' => 3,
            'armor_technology' => 18,
        ]);

        // Create fleet of attacker player.
        $attackerFleet = new UnitCollection();
        $smallCargo = $this->planetService->objects->getUnitObjectByMachineName('small_cargo');
        $attackerFleet->addUnit($smallCargo, 5);

        // Simulate battle.
        $battleEngine = new BattleEngine($attackerFleet, $this->playerService, $this->planetService);
        $battleResult = $battleEngine->simulateBattle();

        $this->assertEquals(5, $battleResult->attackerWeaponLevel);
        $this->assertEquals(3, $battleResult->attackerShieldLevel);
        $this->assertEquals(18, $battleResult->attackerArmorLevel);
    }

    /**
     * Test that the battle engine result contains correct information about the rounds of the battle.
     */
    public function testBattleEngineRounds(): void
    {
        // Create a planet with resources.
        $this->createAndSetPlanetModel([
            'metal' => 100000,
            'crystal' => 100000,
            'deuterium' => 10000,
            'rocket_launcher' => 20,
        ]);
        $this->createAndSetUserTechModel([
            'weapon_technology' => 5,
            'shielding_technology' => 3,
            'armor_technology' => 18,
        ]);

        // Create fleet of attacker player.
        $attackerFleet = new UnitCollection();
        $smallCargo = $this->planetService->objects->getUnitObjectByMachineName('small_cargo');
        $attackerFleet->addUnit($smallCargo, 5);

        // Simulate battle.
        $battleEngine = new BattleEngine($attackerFleet, $this->playerService, $this->planetService);
        $battleResult = $battleEngine->simulateBattle();

        // Assert the rounds are not empty and contain valid data.
        $this->assertNotEmpty($battleResult->rounds);

        // Check first round.
        $firstRound = $battleResult->rounds[0];
        $this->assertNotEmpty($firstRound->attackerShips);
        // Assert that the first round attacker remaining ships are not the same as the starting attacker fleet.
        $this->assertNotEquals($attackerFleet->units, $firstRound->attackerShips->units);
        $this->assertNotEmpty($firstRound->defenderShips);
        $this->assertNotEmpty($firstRound->attackerLosses);
        $this->assertNotEmpty($firstRound->defenderLosses);
        $this->assertNotEmpty($firstRound->attackerLossesInThisRound);
        $this->assertNotEmpty($firstRound->defenderLossesInThisRound);
        $this->assertGreaterThan(0, $firstRound->absorbedDamageAttacker);
        $this->assertGreaterThan(0, $firstRound->absorbedDamageDefender);
        $this->assertGreaterThan(0, $firstRound->fullStrengthAttacker);
        $this->assertGreaterThan(0, $firstRound->fullStrengthDefender);
        $this->assertGreaterThan(0, $firstRound->hitsAttacker);
        $this->assertGreaterThan(0, $firstRound->hitsDefender);
    }

    /**
     * Test that if the defender does not have any defense units there are no rounds as battle is won immediately.
     */
    public function testBattleEngineNoRoundsWithZeroDefense(): void
    {
        // Create a planet with resources.
        $this->createAndSetPlanetModel([
            'metal' => 100000,
            'crystal' => 100000,
            'deuterium' => 10000,
        ]);

        // Create fleet of attacker player.
        $attackerFleet = new UnitCollection();
        $smallCargo = $this->planetService->objects->getUnitObjectByMachineName('small_cargo');
        $attackerFleet->addUnit($smallCargo, 5);

        // Simulate battle.
        $battleEngine = new BattleEngine($attackerFleet, $this->playerService, $this->planetService);
        $battleResult = $battleEngine->simulateBattle();

        // Assert the rounds are empty and contain valid data.
        $this->assertEmpty($battleResult->rounds);
    }
}
