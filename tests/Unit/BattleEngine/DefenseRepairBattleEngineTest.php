<?php

namespace Tests\Unit\BattleEngine;

use OGame\GameMissions\BattleEngine\Models\AttackerFleet;
use OGame\GameMissions\BattleEngine\Models\DefenderFleet;
use OGame\GameMissions\BattleEngine\PhpBattleEngine;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Resources;
use OGame\Models\User;
use OGame\Services\DarkMatterService;
use OGame\Services\ObjectService;
use OGame\Services\OfficerService;
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

    protected function tearDown(): void
    {
        $this->settingsService->set('debris_field_from_ships', 30);
        $this->settingsService->set('debris_field_from_defense', 0);
        $this->settingsService->set('debris_field_deuterium_on', 0);
        $this->settingsService->set('defense_repair_rate', 70);

        parent::tearDown();
    }

    /**
     * Create a battle engine instance for testing.
     */
    protected function createBattleEngine(UnitCollection $attackerFleet): PhpBattleEngine
    {
        // Create defenders array with planet's stationary forces
        $defenders = [DefenderFleet::fromPlanet($this->planetService)];

        // Convert UnitCollection to AttackerFleet for the new multi-attacker architecture
        $attacker = new AttackerFleet();
        $attacker->units = $attackerFleet;
        $attacker->player = $this->playerService;
        $attacker->fleetMissionId = 0; // 0 for test battles without a real fleet mission
        $attacker->ownerId = $this->playerService->getId();
        $attacker->cargoResources = new Resources(0, 0, 0, 0);
        $attacker->isInitiator = true;
        $attacker->fleetMission = null;

        return new PhpBattleEngine([$attacker], $this->planetService, $defenders, $this->settingsService);
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

    /**
     * The Engineer halves the losses that the server's repair rate leaves behind, so with a
     * repair rate of 0 the defender still gets half of the destroyed defenses back.
     */
    public function testEngineerHalvesDefenseLosses(): void
    {
        // No repairs at all for a defender without the Engineer.
        $this->settingsService->set('defense_repair_rate', 0);

        $this->createAndSetPlanetModel([
            'rocket_launcher' => 200,
        ]);

        $attackerFleet = new UnitCollection();
        $attackerFleet->addUnit(ObjectService::getUnitObjectByMachineName('bomber'), 500);

        $withoutEngineer = $this->createBattleEngine($attackerFleet)->simulateBattle();
        $this->assertSame(
            0,
            $withoutEngineer->repairedDefenses->getAmount(),
            'Without the Engineer a repair rate of 0 must leave nothing repaired.'
        );

        // Same battle, but the defender now has an Engineer: 0% leaves 100% losses, halved to 50%.
        $this->giveDefenderAnEngineer();

        $this->createAndSetPlanetModel([
            'rocket_launcher' => 200,
        ]);

        $withEngineer = $this->createBattleEngine($attackerFleet)->simulateBattle();

        $repaired = $withEngineer->repairedDefenses->getAmount();
        $destroyed = $withEngineer->defenderUnitsLost->getAmount();

        $this->assertGreaterThan(
            0,
            $repaired,
            'The Engineer must rebuild part of the destroyed defenses even at a repair rate of 0.'
        );
        $this->assertLessThan(
            $destroyed,
            $repaired,
            'The Engineer halves the losses, it does not remove them.'
        );
        $this->assertEqualsWithDelta($destroyed * 0.5, $repaired, $destroyed * 0.2);
    }

    /**
     * Swap in an OfficerService that reports the Engineer as active, so the battle engine
     * takes the Engineer branch without needing a persisted officer record.
     */
    private function giveDefenderAnEngineer(): void
    {
        $this->app->instance(OfficerService::class, new class (app(DarkMatterService::class)) extends OfficerService {
            public function isActive(User $user, string $officerKey): bool
            {
                return $officerKey === 'engineer';
            }
        });
    }
}
