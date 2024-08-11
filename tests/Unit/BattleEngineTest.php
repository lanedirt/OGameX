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
    }

    /**
     * Test that the loot gained from a battle is calculated correctly.
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
     * Test that the loot gained from a battle is calculated correctly.
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
     * Test that the loot gained from a battle is calculated correctly.
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
     * Test that the fleet of the attacker and defender is saved correctly in the battle report.
     */
    public function testAttackerDefenderFleet(): void
    {
        // Create a planet with resources.
        $this->createAndSetPlanetModel([
            'metal' => 100000,
            'crystal' => 100000,
            'deuterium' => 10000,
            'rocket_launcher' => 20,
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

        $this->assertEquals(5, $battleResult->attackerUnitsStart->getAmountByMachineName($smallCargo->machine_name));
        $this->assertEquals(10, $battleResult->attackerUnitsStart->getAmountByMachineName($lightFighter->machine_name));

        $this->assertEquals(20, $battleResult->defenderUnitsStart->getAmountByMachineName('rocket_launcher'));
    }
}
