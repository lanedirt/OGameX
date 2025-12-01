<?php

namespace Tests\Unit;

use OGame\Services\CounterEspionageService;
use Tests\UnitTestCase;

/**
 * Unit tests for CounterEspionageService.
 *
 * **Feature: counter-espionage**
 */
class CounterEspionageServiceTest extends UnitTestCase
{
    private CounterEspionageService $counterEspionageService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->counterEspionageService = new CounterEspionageService();
    }

    /**
     * Test that counter-espionage chance is always between 0 and 100.
     *
     * @dataProvider chanceCalculationBoundsProvider
     */
    public function testChanceCalculationIsBounded(
        int $attackerProbes,
        int $attackerEspLevel,
        int $defenderEspLevel,
        int $defenderShips
    ): void {
        $chance = $this->counterEspionageService->calculateChance(
            $attackerProbes,
            $attackerEspLevel,
            $defenderEspLevel,
            $defenderShips
        );

        $this->assertGreaterThanOrEqual(0, $chance, 'Chance should be >= 0');
        $this->assertLessThanOrEqual(100, $chance, 'Chance should be <= 100');
    }

    /**
     * Data provider for Property 1: bounds testing with various inputs.
     *
     * @return array<string, array<int>>
     */
    public static function chanceCalculationBoundsProvider(): array
    {
        $testCases = [];

        // Generate 100 random test cases for property-based testing
        for ($i = 0; $i < 100; $i++) {
            $attackerProbes = rand(1, 100);
            $attackerEspLevel = rand(0, 20);
            $defenderEspLevel = rand(0, 20);
            $defenderShips = rand(0, 1000);

            $testCases["random_case_{$i}"] = [
                $attackerProbes,
                $attackerEspLevel,
                $defenderEspLevel,
                $defenderShips,
            ];
        }

        // Add edge cases
        $testCases['min_probes_max_ships'] = [1, 0, 20, 1000];
        $testCases['max_probes_min_ships'] = [100, 20, 0, 1];
        $testCases['equal_tech_levels'] = [5, 10, 10, 50];
        $testCases['attacker_higher_tech'] = [5, 15, 5, 50];
        $testCases['defender_higher_tech'] = [5, 5, 15, 50];
        $testCases['zero_ships'] = [5, 5, 5, 0];

        return $testCases;
    }

    /**
     * Test that counter-espionage chance formula produces correct results.
     *
     * @dataProvider formulaCorrectnessProvider
     */
    public function testFormulaCorrectness(
        int $attackerProbes,
        int $attackerEspLevel,
        int $defenderEspLevel,
        int $defenderShips,
        int $expectedChance
    ): void {
        $chance = $this->counterEspionageService->calculateChance(
            $attackerProbes,
            $attackerEspLevel,
            $defenderEspLevel,
            $defenderShips
        );

        $this->assertEquals($expectedChance, $chance);
    }

    /**
     * Data provider for Property 2: formula correctness.
     *
     * @return array<string, array<int>>
     */
    public static function formulaCorrectnessProvider(): array
    {
        return [
            // Formula: (defender_ships * (defender_esp - attacker_esp + 1)) / (attacker_probes * 4) * 100
            // Example: (10 * (5 - 5 + 1)) / (5 * 4) * 100 = (10 * 1) / 20 * 100 = 50
            'equal_tech_10_ships_5_probes' => [5, 5, 5, 10, 50],

            // (20 * (8 - 5 + 1)) / (10 * 4) * 100 = (20 * 4) / 40 * 100 = 200 -> clamped to 100
            'defender_higher_tech_clamped' => [10, 5, 8, 20, 100],

            // (5 * (3 - 10 + 1)) / (5 * 4) * 100 = (5 * -6) / 20 * 100 = -150 -> 0 (tech factor <= 0)
            'attacker_much_higher_tech' => [5, 10, 3, 5, 0],

            // (0 * (5 - 5 + 1)) / (5 * 4) * 100 = 0
            'zero_ships' => [5, 5, 5, 0, 0],

            // (100 * (10 - 5 + 1)) / (1 * 4) * 100 = (100 * 6) / 4 * 100 = 15000 -> clamped to 100
            'very_high_chance_clamped' => [1, 5, 10, 100, 100],

            // (1 * (5 - 5 + 1)) / (100 * 4) * 100 = 1 / 400 * 100 = 0.25 -> floor to 0
            'very_low_chance' => [100, 5, 5, 1, 0],

            // (4 * (5 - 5 + 1)) / (1 * 4) * 100 = 4 / 4 * 100 = 100
            'exact_100_percent' => [1, 5, 5, 4, 100],

            // (2 * (5 - 5 + 1)) / (1 * 4) * 100 = 2 / 4 * 100 = 50
            'exact_50_percent' => [1, 5, 5, 2, 50],
        ];
    }

    /**
     * Test that zero chance never triggers counter-espionage.
     */
    public function testZeroChanceNeverTriggers(): void
    {
        // Run 100 iterations to verify zero chance never triggers
        for ($i = 0; $i < 100; $i++) {
            $triggered = $this->counterEspionageService->rollCounterEspionage(0);
            $this->assertFalse($triggered, 'Zero chance should never trigger counter-espionage');
        }
    }

    /**
     * Test that 100% chance always triggers.
     */
    public function testHundredPercentAlwaysTriggers(): void
    {
        // Run 100 iterations to verify 100% chance always triggers
        for ($i = 0; $i < 100; $i++) {
            $triggered = $this->counterEspionageService->rollCounterEspionage(100);
            $this->assertTrue($triggered, '100% chance should always trigger counter-espionage');
        }
    }

    /**
     * Test that only ships are counted for counter-espionage, not defense structures.
     */
    public function testOnlyShipsCountedForCounterEspionage(): void
    {
        // Create planet with ships and defense
        $this->createAndSetPlanetModel([
            'small_cargo' => 10,
            'large_cargo' => 5,
            'light_fighter' => 20,
            'rocket_launcher' => 100,
            'light_laser' => 50,
            'heavy_laser' => 25,
        ]);

        $shipCount = $this->counterEspionageService->getDefenderShipCount($this->planetService);

        // Should only count ships: 10 + 5 + 20 = 35
        // Defense (100 + 50 + 25 = 175) should NOT be counted
        $this->assertEquals(35, $shipCount, 'Only ships should be counted, not defense structures');
    }

    /**
     * Test that getDefenderShipsForBattle returns only ships.
     */
    public function testGetDefenderShipsForBattleExcludesDefense(): void
    {
        $this->createAndSetPlanetModel([
            'small_cargo' => 10,
            'rocket_launcher' => 100,
            'plasma_turret' => 10,
        ]);

        $ships = $this->counterEspionageService->getDefenderShipsForBattle($this->planetService);

        // Should only contain ships (small_cargo = 10)
        $this->assertEquals(10, $ships->getAmount(), 'Should only return ships for battle');
        $this->assertEquals(10, $ships->getAmountByMachineName('small_cargo'));
        $this->assertEquals(0, $ships->getAmountByMachineName('rocket_launcher'));
        $this->assertEquals(0, $ships->getAmountByMachineName('plasma_turret'));
    }

    /**
     * Test edge case: zero probes returns 0 chance.
     */
    public function testZeroProbesReturnsZeroChance(): void
    {
        $chance = $this->counterEspionageService->calculateChance(0, 5, 5, 100);
        $this->assertEquals(0, $chance, 'Zero probes should return 0 chance');
    }

    /**
     * Test edge case: negative probes returns 0 chance.
     */
    public function testNegativeProbesReturnsZeroChance(): void
    {
        $chance = $this->counterEspionageService->calculateChance(-5, 5, 5, 100);
        $this->assertEquals(0, $chance, 'Negative probes should return 0 chance');
    }
}
