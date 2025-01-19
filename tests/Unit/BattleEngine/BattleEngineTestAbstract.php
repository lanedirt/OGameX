<?php

namespace Tests\Unit\BattleEngine;

use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Services\ObjectService;
use Tests\UnitTestCase;

abstract class BattleEngineTestAbstract extends UnitTestCase
{
    /**
     * Factory method that should return the battle engine instance to test.
     */
    abstract protected function createBattleEngine(UnitCollection $attackerFleet): mixed;

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
        $smallCargo = ObjectService::getUnitObjectByMachineName('small_cargo');
        $attackerFleet->addUnit($smallCargo, 5);

        // Simulate battle.
        $battleResult = $this->createBattleEngine($attackerFleet)->simulateBattle();

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
        $smallCargo = ObjectService::getUnitObjectByMachineName('small_cargo');
        $attackerFleet->addUnit($smallCargo, 5);

        // Simulate battle.
        $battleResult = $this->createBattleEngine($attackerFleet)->simulateBattle();

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
        $smallCargo = ObjectService::getUnitObjectByMachineName('small_cargo');
        $attackerFleet->addUnit($smallCargo, 5);

        // Simulate battle.
        $battleResult = $this->createBattleEngine($attackerFleet)->simulateBattle();

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
        $smallCargo = ObjectService::getUnitObjectByMachineName('small_cargo');
        $attackerFleet->addUnit($smallCargo, 5);
        $lightFighter = ObjectService::getUnitObjectByMachineName('light_fighter');
        $attackerFleet->addUnit($lightFighter, 75);

        // Simulate battle.
        $battleResult = $this->createBattleEngine($attackerFleet)->simulateBattle();

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
        $smallCargo = ObjectService::getUnitObjectByMachineName('small_cargo');
        $attackerFleet->addUnit($smallCargo, 5);

        // Simulate battle.
        $battleResult = $this->createBattleEngine($attackerFleet)->simulateBattle();

        $this->assertEquals(5, $battleResult->attackerWeaponLevel);
        $this->assertEquals(3, $battleResult->attackerShieldLevel);
        $this->assertEquals(18, $battleResult->attackerArmorLevel);
    }

    /**
     * Test that the battle engine result contains correct basic information about the rounds of the battle.
     */
    public function testBattleEngineBasicRounds(): void
    {
        // Create a planet with resources.
        $this->createAndSetPlanetModel([
            'metal' => 100000,
            'crystal' => 100000,
            'deuterium' => 10000,
            'rocket_launcher' => 10,
        ]);
        $this->createAndSetUserTechModel([
            'weapon_technology' => 5,
            'shielding_technology' => 3,
            'armor_technology' => 18,
        ]);

        // Create fleet of attacker player.
        $attackerFleet = new UnitCollection();
        $smallCargo = ObjectService::getUnitObjectByMachineName('small_cargo');
        $attackerFleet->addUnit($smallCargo, 5);

        // Simulate battle.
        $battleResult = $this->createBattleEngine($attackerFleet)->simulateBattle();

        // Assert the rounds are not empty and contain valid data.
        $this->assertNotEmpty($battleResult->rounds);

        // Check last round.
        $lastRound = end($battleResult->rounds);
        $this->assertNotEmpty($lastRound->attackerShips);

        $this->assertNotEquals($attackerFleet->getAmountByMachineName($smallCargo->machine_name), $lastRound->attackerShips->getAmountByMachineName($smallCargo->machine_name));
        $this->assertNotEmpty($lastRound->defenderShips);

        // The last round attacker losses should be a sum of all losses in the previous rounds. Because we expect
        // the attacker to lose all units this should be equal to the total amount of units in the attacker fleet.
        $this->assertEquals(5, $lastRound->attackerLosses->getAmountByMachineName($smallCargo->machine_name));
        $this->assertEquals(0, $lastRound->defenderLosses->getAmountByMachineName('rocket_launcher'));

        $this->assertGreaterThan(0, $lastRound->attackerLossesInRound->getAmountByMachineName($smallCargo->machine_name));
        $this->assertLessThan(5, $lastRound->attackerLossesInRound->getAmountByMachineName($smallCargo->machine_name));
        $this->assertEquals(0, $lastRound->defenderLossesInRound->getAmountByMachineName('rocket_launcher'));

        $this->assertGreaterThan(0, $lastRound->absorbedDamageAttacker);
        $this->assertGreaterThan(0, $lastRound->absorbedDamageDefender);
        $this->assertGreaterThan(0, $lastRound->fullStrengthAttacker);
        $this->assertGreaterThan(0, $lastRound->fullStrengthDefender);
        $this->assertGreaterThan(0, $lastRound->hitsAttacker);
        $this->assertGreaterThan(0, $lastRound->hitsDefender);
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
        $smallCargo = ObjectService::getUnitObjectByMachineName('small_cargo');
        $attackerFleet->addUnit($smallCargo, 5);

        // Simulate battle.
        $battleResult = $this->createBattleEngine($attackerFleet)->simulateBattle();

        // Assert the rounds are empty and contain valid data.
        $this->assertEmpty($battleResult->rounds);
    }

    /**
     * Test that the battle engine result matches the expected output of prepared simulation.
     */
    public function testBattleEngineSimulationCorrect1(): void
    {
        // Simulation 1: attacker with 150 light fighters vs defender with 200 rocket launchers (not taking into account any tech levels).
        // Expected result: attacker loses, defender rocket launchers remaining >= 160.
        $this->createAndSetPlanetModel([
            'rocket_launcher' => 200,
        ]);

        // Create fleet of attacker player.
        $attackerFleet = new UnitCollection();
        $lightFighter = ObjectService::getUnitObjectByMachineName('light_fighter');
        $attackerFleet->addUnit($lightFighter, 150);

        // Simulate battle.
        $battleResult = $this->createBattleEngine($attackerFleet)->simulateBattle();

        // Assert the rounds are not empty and contain valid data.
        $this->assertNotEmpty($battleResult->rounds);

        // Get last round with result.
        $lastRound = end($battleResult->rounds);
        $this->assertNotEmpty($lastRound->attackerShips);
        $this->assertNotEmpty($lastRound->defenderShips);
        $this->assertEquals(0, $lastRound->attackerShips->getAmountByMachineName($lightFighter->machine_name));
        $this->assertGreaterThanOrEqual(100, $lastRound->defenderShips->getAmountByMachineName('rocket_launcher'));

        // The attackerLosses property should be a sum of all losses in the previous rounds. Because we expect the attacker
        // to lose all units this should be equal to the total amount of units in the attacker fleet.
        $this->assertEquals(150, $lastRound->attackerLosses->getAmountByMachineName($lightFighter->machine_name));

        // Calculate that the resource loss of attacker matches the expected loss.
        $this->assertEquals(150 * $lightFighter->price->resources->metal->get(), $battleResult->attackerResourceLoss->metal->get());
        $this->assertEquals(150 * $lightFighter->price->resources->crystal->get(), $battleResult->attackerResourceLoss->crystal->get());
        $this->assertEquals(0, $battleResult->attackerResourceLoss->deuterium->get());

        // Calculate that the metal resource loss of defender is higher than 0. Others are 0 as rocket launcher
        // only costs metal to build.
        $this->assertGreaterThan(0, $battleResult->defenderResourceLoss->metal->get());
        $this->assertEquals(0, $battleResult->defenderResourceLoss->crystal->get());
        $this->assertEquals(0, $battleResult->defenderResourceLoss->deuterium->get());
    }

    /**
     * Test that the battle engine result matches the expected output of prepared simulation.
     */
    public function testBattleEngineSimulationCorrect2(): void
    {
        // Simulation 1: attacker with 1 death star and 1k light fighters vs defender with 200 plasma turrets, 100 rocket launchers and 50 light lasers (not taking into account any tech levels).
        // Expected result: draw. attacker keeps death star and < 100 light fighters. Defender keeps > 180 plasma turrets, < 20 rocket launchers and < 20 light lasers.
        $this->createAndSetPlanetModel([
            'rocket_launcher' => 100,
            'light_laser' => 50,
            'plasma_turret' => 200,
        ]);

        // Create fleet of attacker player.
        $attackerFleet = new UnitCollection();
        $deathStar = ObjectService::getUnitObjectByMachineName('deathstar');
        $attackerFleet->addUnit($deathStar, 1);
        $lightFighter = ObjectService::getUnitObjectByMachineName('light_fighter');
        $attackerFleet->addUnit($lightFighter, 1000);

        // Simulate battle.
        $battleResult = $this->createBattleEngine($attackerFleet)->simulateBattle();

        // Assert the rounds are not empty and contain valid data.
        $this->assertNotEmpty($battleResult->rounds);

        // Get last round with result.
        $lastRound = end($battleResult->rounds);
        $this->assertNotEmpty($lastRound->attackerShips);
        $this->assertNotEmpty($lastRound->defenderShips);
        $this->assertEquals(1, $lastRound->attackerShips->getAmountByMachineName($deathStar->machine_name));
        $this->assertLessThanOrEqual(100, $lastRound->attackerShips->getAmountByMachineName($lightFighter->machine_name));
        $this->assertGreaterThanOrEqual(180, $lastRound->defenderShips->getAmountByMachineName('plasma_turret'));
        $this->assertLessThanOrEqual(20, $lastRound->defenderShips->getAmountByMachineName('rocket_launcher'));
        $this->assertLessThanOrEqual(20, $lastRound->defenderShips->getAmountByMachineName('light_laser'));
    }

    /**
     * Test that the battle engine total attack power and shield absorption stats calculations are correct.
     */
    public function testBattleEngineStatistics(): void
    {
        $this->createAndSetPlanetModel([
            'rocket_launcher' => 100,
        ]);

        // Create fleet of attacker player.
        $attackerFleet = new UnitCollection();
        $lightFighter = ObjectService::getUnitObjectByMachineName('light_fighter');
        $attackerFleet->addUnit($lightFighter, 30);

        // Simulate battle.
        $battleResult = $this->createBattleEngine($attackerFleet)->simulateBattle();

        // Assert the rounds are not empty and contain valid data.
        $this->assertNotEmpty($battleResult->rounds);

        // Loop through all rounds and assert that all stats are greater than 0.
        foreach ($battleResult->rounds as $round) {
            $this->assertGreaterThan(40, $round->fullStrengthAttacker);
            $this->assertGreaterThan(40, $round->fullStrengthDefender);
            $this->assertGreaterThan(5, $round->absorbedDamageAttacker);
            $this->assertGreaterThan(5, $round->absorbedDamageDefender);
        }
    }

    /**
     * Test that the battle engine rapidfire works correctly.
     */
    public function testBattleEngineRapidfire(): void
    {
        $this->createAndSetPlanetModel([
            'rocket_launcher' => 500,
        ]);

        // Create fleet of attacker player.
        $attackerFleet = new UnitCollection();
        $cruiser = ObjectService::getUnitObjectByMachineName('cruiser');
        $attackerFleet->addUnit($cruiser, 30);

        // Simulate battle.
        $battleResult = $this->createBattleEngine($attackerFleet)->simulateBattle();

        // Assert the rounds are not empty and contain valid data.
        $this->assertNotEmpty($battleResult->rounds);

        // Get first round
        $firstRound = $battleResult->rounds[0];

        // Assert that rapidfire works by checking total hits on defender. It should be greater than the amount of
        // cruisers as each cruiser has a chance of rapidfire against a rocket launcher.
        $this->assertGreaterThan(30, $firstRound->hitsAttacker);

        // Get last round with result.
        $lastRound = end($battleResult->rounds);
        // Expected result: attacker loses, defender rocket launchers remaining < 400. Without rapidfire the estimated
        // remaining rocket launchers would be between 450-500.
        $this->assertLessThan(400, $lastRound->defenderShips->getAmountByMachineName('rocket_launcher'));
    }

    /**
     * Test that the battle engine shield bounce logic works correctly.
     */
    public function testBattleEngineBounce(): void
    {
        // This test is to verify that the shield bounce logic works correctly. A light fighter's attack power is less
        // than 1% of a large shield dome's shield points. Therefore, the attack should bounce off the shield and not
        // cause any damage to the shield dome. The battle will end in a draw after 6 rounds.
        $this->createAndSetPlanetModel([
            'large_shield_dome' => 1,
        ]);

        // Create fleet of attacker player.
        $attackerFleet = new UnitCollection();
        $cruiser = ObjectService::getUnitObjectByMachineName('light_fighter');
        $attackerFleet->addUnit($cruiser, 5000);

        // Simulate battle.
        $battleResult = $this->createBattleEngine($attackerFleet)->simulateBattle();

        // Assert that there are 6 rounds in the battle.
        $this->assertCount(6, $battleResult->rounds);

        // Get last round.
        $lastRound = end($battleResult->rounds);
        $this->assertNotEmpty($lastRound);
        // Assert that attacker has all light fighters remaining.
        $this->assertEquals(5000, $lastRound->attackerShips->getAmountByMachineName('light_fighter'));
        // Assert that defender has the large shield dome remaining.
        $this->assertEquals(1, $lastRound->defenderShips->getAmountByMachineName('large_shield_dome'));
    }

    /**
     * Test that the battle engine shield bounce logic works correctly with high tech levels.
     */
    public function testBattleEngineNoBounceHighTechLevel(): void
    {
        // If attacker has a very high weapon tech level the light fighters attack power is upgraded and will
        // be able to destroy a large shield dome. Attacker needs to have weapon tech level 10 higher than
        // twice the defender's shield tech level to destroy the shield.
        // In this case we test with weapon tech level 31 and shield tech level 10.
        $this->createAndSetPlanetModel([
            'large_shield_dome' => 1,
        ]);
        // TODO: currently the attacker player and defender player are the same so the tech level used in the
        // battle engine are the same. Refactor this unit test logic later so that the attacker and defender tech
        // levels can be set separately.
        $this->createAndSetUserTechModel([
            'weapon_technology' => 31,
            'shielding_technology' => 10,
        ]);

        // Create fleet of attacker player.
        $attackerFleet = new UnitCollection();
        $cruiser = ObjectService::getUnitObjectByMachineName('light_fighter');
        $attackerFleet->addUnit($cruiser, 5000);

        // Simulate battle.
        $battleResult = $this->createBattleEngine($attackerFleet)->simulateBattle();

        // Assert that there is only 1 round in the battle.
        $this->assertCount(1, $battleResult->rounds);

        // Get last round.
        $lastRound = end($battleResult->rounds);
        $this->assertNotEmpty($lastRound);
        // Assert that attacker has all light fighters remaining.
        $this->assertEquals(5000, $lastRound->attackerShips->getAmountByMachineName('light_fighter'));
        // Assert that the large shield dome is destroyed.
        $this->assertEquals(0, $lastRound->defenderShips->getAmountByMachineName('large_shield_dome'));
    }

    /**
     * Test that the battle engine produces debris when ships are destroyed.
     */
    public function testBattleEngineSimulationDebris(): void
    {
        // Attacker with 50 light fighters vs. defender with 1000 heavy lasers.
        // Expecting attacker to lose all units which are turned to debris at 30% rate.
        $this->createAndSetPlanetModel([
            'heavy_laser' => 1000,
        ]);

        // Create fleet of attacker player.
        $attackerFleet = new UnitCollection();
        $lightFighter = ObjectService::getUnitObjectByMachineName('light_fighter');
        $attackerFleet->addUnit($lightFighter, 50);

        $this->settingsService->set('debris_field_from_ships', 30);
        $this->settingsService->set('debris_field_from_defense', 0);
        $this->settingsService->set('debris_field_deuterium_on', 0);

        // Simulate battle.
        $battleResult = $this->createBattleEngine($attackerFleet)->simulateBattle();

        // Assert the rounds are not empty and contain valid data.
        $this->assertNotEmpty($battleResult->rounds);
        $lastRound = end($battleResult->rounds);

        // Expect attacker to have lost all ships, defender to have all heavy lasers remaining.
        $this->assertEquals(0, $lastRound->attackerShips->getAmountByMachineName($lightFighter->machine_name));
        $this->assertEquals(50, $lastRound->attackerLosses->getAmountByMachineName($lightFighter->machine_name));
        $this->assertGreaterThanOrEqual(1000, $lastRound->defenderShips->getAmountByMachineName('heavy_laser'));

        // Calculate the debris generated from the destroyed ships.
        // Expected total losses: 150k metal and 50k crystal.
        // Expected debris (30%): 45k metal and 15k crystal.
        $this->assertEquals(45000, $battleResult->debris->metal->get());
        $this->assertEquals(15000, $battleResult->debris->crystal->get());
        $this->assertEquals(0, $battleResult->debris->deuterium->get());
    }

    /**
     * Test debris generation with different settings.
     */
    public function testBattleEngineSimulationDebrisDifferentSettings(): void
    {
        // Attacker with 50 light fighters vs. defender with 1000 heavy lasers.
        // Expecting attacker to lose all units which are turned to debris at 30% rate
        $this->settingsService->set('debris_field_from_ships', 30);
        $this->settingsService->set('debris_field_from_defense', 0);
        $this->settingsService->set('debris_field_deuterium_on', 0);

        // Calculate the debris generated from the destroyed ships.
        // Test with different debris percentages
        $debrisPercentages = [0, 30, 50, 100];
        foreach ($debrisPercentages as $percentage) {
            $this->settingsService->set('debris_field_from_ships', $percentage);

            $this->createAndSetPlanetModel([
                'heavy_laser' => 1000,
            ]);

            // Create fleet of attacker player.
            // Expected total losses of attacker player: 150k metal and 50k crystal.
            $attackerFleet = new UnitCollection();
            $lightFighter = ObjectService::getUnitObjectByMachineName('light_fighter');
            $attackerFleet->addUnit($lightFighter, 50);

            // Simulate battle
            $battleResult = $this->createBattleEngine($attackerFleet)->simulateBattle();

            // Calculate expected debris
            $expectedMetal = (150000 * $percentage) / 100;
            $expectedCrystal = (50000 * $percentage) / 100;

            // Assert debris matches expectations
            $this->assertEquals($expectedMetal, $battleResult->debris->metal->get(), "Metal debris doesn't match for {$percentage}% setting");
            $this->assertEquals($expectedCrystal, $battleResult->debris->crystal->get(), "Crystal debris doesn't match for {$percentage}% setting");
            $this->assertEquals(0, $battleResult->debris->deuterium->get(), "Deuterium debris should always be 0");
        }

        // Test defense debris
        $defenseDebrisPercentages = [0, 30, 50, 100];
        foreach ($defenseDebrisPercentages as $percentage) {
            $this->settingsService->set('debris_field_from_defense', $percentage);
            $this->settingsService->set('debris_field_from_ships', 0);

            $this->createAndSetPlanetModel([
                'light_laser' => 200,
            ]);

            // Create fleet of attacker player.
            // Expected total losses of defender player: 300k metal and 100k crystal.
            $attackerFleet = new UnitCollection();
            $bomber = ObjectService::getUnitObjectByMachineName('bomber');
            $attackerFleet->addUnit($bomber, 500);

            // Simulate battle.
            $battleResult = $this->createBattleEngine($attackerFleet)->simulateBattle();

            // Calculate expected debris.
            $expectedMetal = (300000 * $percentage) / 100;
            $expectedCrystal = (100000 * $percentage) / 100;

            // Assert debris matches expectations.
            $this->assertEquals($expectedMetal, $battleResult->debris->metal->get(), "Metal debris doesn't match for {$percentage}% defense debris setting");
            $this->assertEquals($expectedCrystal, $battleResult->debris->crystal->get(), "Crystal debris doesn't match for {$percentage}% defense debris setting");
            $this->assertEquals(0, $battleResult->debris->deuterium->get(), "Deuterium debris should be 0 when deuterium_on is false");
        }

        // Test deuterium off and on with 30% debris from ships.
        $deuteriumSettings = [0, 1];
        foreach ($deuteriumSettings as $setting) {
            $this->settingsService->set('debris_field_deuterium_on', $setting);
            $this->settingsService->set('debris_field_from_ships', 30);
            $this->settingsService->set('debris_field_from_defense', 0);

            $this->createAndSetPlanetModel([
                'plasma_turret' => 1000,
            ]);

            // Create fleet of attacker player.
            // Expected total losses of attacker player: 1M metal and 350k crystal, 100k deuterium.
            $attackerFleet = new UnitCollection();
            $cruiser = ObjectService::getUnitObjectByMachineName('cruiser');
            $attackerFleet->addUnit($cruiser, 50);

            // Simulate battle
            $battleResult = $this->createBattleEngine($attackerFleet)->simulateBattle();

            // Calculate expected debris
            $expectedMetal = 300000;
            $expectedCrystal = 105000;
            $expectedDeuterium = $setting == 1 ? 30000 : 0;

            // Assert debris matches expectations
            $this->assertEquals($expectedMetal, $battleResult->debris->metal->get(), "Metal debris doesn't match for deuterium_on={$setting}");
            $this->assertEquals($expectedCrystal, $battleResult->debris->crystal->get(), "Crystal debris doesn't match for deuterium_on={$setting}");
            $this->assertEquals($expectedDeuterium, $battleResult->debris->deuterium->get(), "Deuterium debris doesn't match for deuterium_on={$setting}");
        }
    }

    /**
     * Test that a larger battle with 50.000 units on each side (100k total) works correctly.
     */
    public function testBattleEngineLargeBattle(): void
    {
        $start_rocket_launcher = 50000;
        $start_light_fighter = 50000;
        $this->createAndSetPlanetModel([
            'rocket_launcher' => $start_rocket_launcher,
        ]);

        // Create fleet of attacker player.
        $attackerFleet = new UnitCollection();
        $lightFighter = ObjectService::getUnitObjectByMachineName('light_fighter');
        $attackerFleet->addUnit($lightFighter, $start_light_fighter);

        // Simulate battle.
        $battleResult = $this->createBattleEngine($attackerFleet)->simulateBattle();

        // Assert that there is more than 1 round in the battle.
        $this->assertGreaterThan(1, count($battleResult->rounds));

        // Get last round.
        $lastRound = end($battleResult->rounds);
        $this->assertNotEmpty($lastRound);
        // Assert that attacker lost some light fighters.
        $this->assertLessThan($start_light_fighter, $lastRound->attackerShips->getAmountByMachineName('light_fighter'));
        // Assert that defender has lost some rocket launchers.
        $this->assertLessThan($start_rocket_launcher, $lastRound->defenderShips->getAmountByMachineName('rocket_launcher'));
    }
}
