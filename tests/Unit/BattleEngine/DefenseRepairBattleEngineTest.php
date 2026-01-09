<?php

namespace Tests\Unit\BattleEngine;

use OGame\GameMissions\BattleEngine\PhpBattleEngine;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Services\ObjectService;
use Tests\UnitTestCase;

/**
 * Test defense repair functionality in battle engine.
 */
class DefenseRepairBattleEngineTest extends UnitTestCase
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
     * Create a battle engine instance for testing.
     */
    protected function createBattleEngine(UnitCollection $attackerFleet): PhpBattleEngine
    {
        // Create defenders array with planet's stationary forces
        $defenders = [\OGame\GameMissions\BattleEngine\Models\DefenderFleet::fromPlanet($this->planetService)];

        // For test battles, use fleetMissionId = 0 and current player's ID
        return new PhpBattleEngine($attackerFleet, $this->playerService, $this->planetService, $defenders, $this->settingsService, 0, $this->playerService->getId());
    }

    /**
     * Test that debris field only includes resources from permanently lost defenses,
     * not from repaired defenses.
     */
    public function testDebrisFieldExcludesRepairedDefenses(): void
    {
        // Set up: 100% defense repair rate so all destroyed defenses are repaired
        $this->settingsService->set('defense_repair_rate', 100);
        $this->settingsService->set('debris_field_from_defense', 30);
        $this->settingsService->set('debris_field_from_ships', 0);
        $this->settingsService->set('debris_field_deuterium_on', 0);

        // Create a planet with defenses that will be destroyed
        $this->createAndSetPlanetModel([
            'rocket_launcher' => 100,
        ]);

        // Create a strong attacker fleet that will destroy all defenses
        $attackerFleet = new UnitCollection();
        $bomber = ObjectService::getUnitObjectByMachineName('bomber');
        $attackerFleet->addUnit($bomber, 500);

        // Simulate battle
        $battleResult = $this->createBattleEngine($attackerFleet)->simulateBattle();

        // With 100% repair rate, all destroyed defenses should be repaired
        // Therefore, debris from defenses should be 0
        $this->assertEquals(
            0,
            $battleResult->debris->metal->get(),
            "With 100% repair rate, no defense debris should be generated"
        );
        $this->assertEquals(
            0,
            $battleResult->debris->crystal->get(),
            "With 100% repair rate, no defense debris should be generated"
        );
    }

    /**
     * Test that repaired defenses are calculated and stored in battle result.
     */
    public function testRepairedDefensesInBattleResult(): void
    {
        // Set up: 100% defense repair rate
        $this->settingsService->set('defense_repair_rate', 100);

        // Create a planet with defenses
        $this->createAndSetPlanetModel([
            'rocket_launcher' => 50,
            'light_laser' => 30,
        ]);

        // Create a strong attacker fleet
        $attackerFleet = new UnitCollection();
        $bomber = ObjectService::getUnitObjectByMachineName('bomber');
        $attackerFleet->addUnit($bomber, 500);

        // Simulate battle
        $battleResult = $this->createBattleEngine($attackerFleet)->simulateBattle();

        // With 100% repair rate, all destroyed defenses should be in repairedDefenses
        $destroyedRocketLaunchers = $battleResult->defenderUnitsLost->getAmountByMachineName('rocket_launcher');
        $repairedRocketLaunchers = $battleResult->repairedDefenses->getAmountByMachineName('rocket_launcher');

        $this->assertEquals(
            $destroyedRocketLaunchers,
            $repairedRocketLaunchers,
            "With 100% repair rate, all destroyed rocket launchers should be repaired"
        );
    }

    /**
     * Test partial repair rate produces proportional debris.
     */
    public function testPartialRepairRateProducesProportionalDebris(): void
    {
        // Run multiple iterations to get statistical average
        $totalDebrisMetal = 0;
        $iterations = 20;

        $this->settingsService->set('defense_repair_rate', 50); // 50% repair rate
        $this->settingsService->set('debris_field_from_defense', 100); // 100% debris for easier calculation
        $this->settingsService->set('debris_field_from_ships', 0);
        $this->settingsService->set('debris_field_deuterium_on', 0);

        for ($i = 0; $i < $iterations; $i++) {
            // Create a planet with defenses
            $this->createAndSetPlanetModel([
                'rocket_launcher' => 100,
            ]);

            // Create a strong attacker fleet
            $attackerFleet = new UnitCollection();
            $bomber = ObjectService::getUnitObjectByMachineName('bomber');
            $attackerFleet->addUnit($bomber, 500);

            // Simulate battle
            $battleResult = $this->createBattleEngine($attackerFleet)->simulateBattle();
            $totalDebrisMetal += $battleResult->debris->metal->get();
        }

        $averageDebris = $totalDebrisMetal / $iterations;

        // With 50% repair rate and 100% debris rate:
        // 100 rocket launchers * 2000 metal = 200000 total
        // 50% repaired = 100000 permanently lost
        // 100% debris = 100000 debris (average)
        // Allow ±20% tolerance for randomness
        $expectedDebris = 100000;
        $this->assertGreaterThan(
            $expectedDebris * 0.7,
            $averageDebris,
            "Average debris should be around 100000 with 50% repair rate"
        );
        $this->assertLessThan(
            $expectedDebris * 1.3,
            $averageDebris,
            "Average debris should be around 100000 with 50% repair rate"
        );
    }
}
