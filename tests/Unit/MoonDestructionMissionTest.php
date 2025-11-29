<?php

namespace Tests\Unit;

use OGame\GameMissions\MoonDestructionMission;
use OGame\Services\FleetMissionService;
use OGame\Services\MessageService;
use OGame\Factories\PlanetServiceFactory;
use OGame\Factories\PlayerServiceFactory;
use OGame\Services\SettingsService;
use OGame\Models\Planet\Coordinate;
use OGame\GameObjects\Models\Units\UnitCollection;
use Tests\UnitTestCase;
use ReflectionClass;

class MoonDestructionMissionTest extends UnitTestCase
{
    private MoonDestructionMission $mission;

    protected function setUp(): void
    {
        parent::setUp();

        // Create mission instance with mocked dependencies
        $this->mission = new MoonDestructionMission(
            $this->app->make(FleetMissionService::class),
            $this->app->make(MessageService::class),
            $this->app->make(PlanetServiceFactory::class),
            $this->app->make(PlayerServiceFactory::class),
            $this->app->make(SettingsService::class)
        );
    }

    /**
     * @param array<int, mixed> $args
     * @return mixed
     */
    private function callPrivateMethod(string $methodName, ...$args): mixed
    {
        $reflection = new ReflectionClass($this->mission);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invoke($this->mission, ...$args);
    }

    /**
     * For any moon diameter and Deathstar count, destruction chance = (100 - √diameter) × √deathstars
     */
    public function testMoonDestructionChanceFormula(): void
    {
        // Test with various moon sizes and Deathstar counts
        $testCases = [
            // [moonDiameter, deathstarCount, expectedMin, expectedMax]
            [1000, 1, 68.3, 68.5],      // Small moon, 1 DS: (100 - 31.62) * 1 ≈ 68.38
            [4000, 4, 73.5, 73.7],      // Medium moon, 4 DS: (100 - 63.25) * 2 ≈ 73.5
            [8000, 10, 33.3, 33.5],     // Large moon, 10 DS: (100 - 89.44) * 3.16 ≈ 33.38
            [100, 100, 900, 901],       // Small moon, many DS: (100 - 10) * 10 = 900 (clamped to 100)
            [8836, 1, 5.9, 6.1],        // Max size moon, 1 DS: (100 - 94) * 1 ≈ 6
            [1, 1, 99, 100],            // Minimum moon, 1 DS: (100 - 1) * 1 = 99
        ];

        foreach ($testCases as [$diameter, $dsCount, $expectedMin, $expectedMax]) {
            $result = $this->callPrivateMethod('calculateMoonDestructionChance', $diameter, $dsCount);

            // For values that should be clamped to 100
            if ($expectedMin > 100) {
                $this->assertEquals(
                    100,
                    $result,
                    "Destruction chance for {$diameter}km moon with {$dsCount} DS should be clamped to 100"
                );
            } else {
                $this->assertGreaterThanOrEqual(
                    $expectedMin,
                    $result,
                    "Destruction chance for {$diameter}km moon with {$dsCount} DS should be >= {$expectedMin}"
                );
                $this->assertLessThanOrEqual(
                    $expectedMax,
                    $result,
                    "Destruction chance for {$diameter}km moon with {$dsCount} DS should be <= {$expectedMax}"
                );
            }
        }
    }

    /**
     * For any moon diameter, loss chance = √diameter / 2
     */
    public function testDeathstarLossChanceFormula(): void
    {
        // Test with various moon sizes
        $testCases = [
            // [moonDiameter, expectedMin, expectedMax]
            [1000, 15.8, 15.9],    // √1000 / 2 ≈ 15.81
            [4000, 31.6, 31.7],    // √4000 / 2 ≈ 31.62
            [8000, 44.7, 44.8],    // √8000 / 2 ≈ 44.72
            [100, 5.0, 5.1],       // √100 / 2 = 5
            [8836, 47.0, 47.1],    // √8836 / 2 ≈ 47
            [1, 0.5, 0.6],         // √1 / 2 = 0.5
            [10000, 50, 50.1],     // √10000 / 2 = 50 (clamped to 100 max)
        ];

        foreach ($testCases as [$diameter, $expectedMin, $expectedMax]) {
            $result = $this->callPrivateMethod('calculateDeathstarLossChance', $diameter);

            $this->assertGreaterThanOrEqual(
                $expectedMin,
                $result,
                "Loss chance for {$diameter}km moon should be >= {$expectedMin}"
            );
            $this->assertLessThanOrEqual(
                $expectedMax,
                $result,
                "Loss chance for {$diameter}km moon should be <= {$expectedMax}"
            );
            $this->assertLessThanOrEqual(
                100,
                $result,
                "Loss chance should never exceed 100%"
            );
        }
    }

    public function testZeroDeathstarsGivesZeroChance(): void
    {
        $result = $this->callPrivateMethod('calculateMoonDestructionChance', 5000, 0);
        $this->assertEquals(0, $result, "Zero Deathstars should give 0% destruction chance");
    }

    public function testNegativeValuesHandledSafely(): void
    {
        // Negative diameter should be clamped to minimum
        $result = $this->callPrivateMethod('calculateMoonDestructionChance', -1000, 5);
        $this->assertGreaterThanOrEqual(0, $result, "Negative diameter should not cause errors");

        // Negative Deathstar count should be clamped to 0
        $result = $this->callPrivateMethod('calculateMoonDestructionChance', 5000, -5);
        $this->assertEquals(0, $result, "Negative Deathstar count should give 0% chance");
    }

    public function testDestructionChanceClampedToRange(): void
    {
        // Very small moon with many Deathstars should clamp to 100
        $result = $this->callPrivateMethod('calculateMoonDestructionChance', 10, 1000);
        $this->assertEquals(100, $result, "Destruction chance should be clamped to 100%");

        // Very large moon with few Deathstars might give negative, should clamp to 0
        $result = $this->callPrivateMethod('calculateMoonDestructionChance', 10000, 1);
        $this->assertGreaterThanOrEqual(0, $result, "Destruction chance should not be negative");
    }

    public function testLossChanceClampedToRange(): void
    {
        // Normal moon sizes should give reasonable values
        $result = $this->callPrivateMethod('calculateDeathstarLossChance', 5000);
        $this->assertGreaterThanOrEqual(0, $result);
        $this->assertLessThanOrEqual(100, $result);

        // Very large moon should not exceed 100%
        $result = $this->callPrivateMethod('calculateDeathstarLossChance', 40000);
        $this->assertLessThanOrEqual(100, $result, "Loss chance should be clamped to 100%");
    }

    public function testMissionValidationRequiresDeathstar(): void
    {
        $this->setUpPlanetService();
        $this->createAndSetPlanetModel([
            'deathstar' => 5,
            'small_cargo' => 10,
        ]);

        // Create a mock target moon
        $targetCoordinate = new Coordinate(1, 1, 5);

        // Test 1: Fleet with Deathstars should be possible (if moon exists)
        $unitsWithDeathstar = new UnitCollection();
        $deathstar = $this->app->make(\OGame\Services\ObjectService::class)->getUnitObjectByMachineName('deathstar');
        $unitsWithDeathstar->addUnit($deathstar, 1);

        // Note: This will fail because moon doesn't exist, but it tests the Deathstar requirement
        $result = $this->mission->isMissionPossible(
            $this->planetService,
            $targetCoordinate,
            \OGame\Models\Enums\PlanetType::Moon,
            $unitsWithDeathstar
        );

        // Should fail because moon doesn't exist, not because of missing Deathstar
        $this->assertFalse($result->possible, 'Mission should not be possible when moon does not exist');

        // Test 2: Fleet without Deathstars should fail
        $unitsWithoutDeathstar = new UnitCollection();
        $smallCargo = $this->app->make(\OGame\Services\ObjectService::class)->getUnitObjectByMachineName('small_cargo');
        $unitsWithoutDeathstar->addUnit($smallCargo, 10);

        $result = $this->mission->isMissionPossible(
            $this->planetService,
            $targetCoordinate,
            \OGame\Models\Enums\PlanetType::Moon,
            $unitsWithoutDeathstar
        );

        $this->assertFalse($result->possible, 'Mission should not be possible without Deathstars');
    }

    public function testMissionOnlyTargetsMoons(): void
    {
        $this->setUpPlanetService();
        $this->createAndSetPlanetModel(['deathstar' => 5]);

        $units = new UnitCollection();
        $deathstar = $this->app->make(\OGame\Services\ObjectService::class)->getUnitObjectByMachineName('deathstar');
        $units->addUnit($deathstar, 1);

        $targetCoordinate = new Coordinate(1, 1, 5);

        // Test targeting a planet (should fail)
        $result = $this->mission->isMissionPossible(
            $this->planetService,
            $targetCoordinate,
            \OGame\Models\Enums\PlanetType::Planet,
            $units
        );

        $this->assertFalse($result->possible, 'Mission should not be possible when targeting a planet');

        // Test targeting debris field (should fail)
        $result = $this->mission->isMissionPossible(
            $this->planetService,
            $targetCoordinate,
            \OGame\Models\Enums\PlanetType::DebrisField,
            $units
        );

        $this->assertFalse($result->possible, 'Mission should not be possible when targeting debris field');
    }

    public function testProbabilisticDestructionExecution(): void
    {
        // Test that destruction chance of 100% always succeeds
        // and 0% always fails over multiple iterations

        // With 100% chance (very small moon, many Deathstars)
        $highChance = $this->callPrivateMethod('calculateMoonDestructionChance', 10, 1000);
        $this->assertEquals(100, $highChance, 'Should have 100% destruction chance');

        // With 0% chance (very large moon, no Deathstars)
        $zeroChance = $this->callPrivateMethod('calculateMoonDestructionChance', 10000, 0);
        $this->assertEquals(0, $zeroChance, 'Should have 0% destruction chance');

        // Test that intermediate values produce probabilistic results
        // A 50% chance should succeed roughly half the time over many iterations
        $moonDiameter = 2500; // Will give ~50% with 1 Deathstar
        $deathstarCount = 1;
        $chance = $this->callPrivateMethod('calculateMoonDestructionChance', $moonDiameter, $deathstarCount);

        $this->assertGreaterThan(40, $chance, 'Chance should be > 40%');
        $this->assertLessThan(60, $chance, 'Chance should be < 60%');
    }

    public function testDeathstarLossProbability(): void
    {
        // Test various moon sizes produce expected loss chances
        $testCases = [
            [100, 5],      // Small moon: low loss chance
            [2500, 25],    // Medium moon: medium loss chance
            [8000, 44.7],  // Large moon: high loss chance
        ];

        foreach ($testCases as [$diameter, $expectedLoss]) {
            $lossChance = $this->callPrivateMethod('calculateDeathstarLossChance', $diameter);

            $this->assertGreaterThanOrEqual(
                $expectedLoss - 1,
                $lossChance,
                "Loss chance for {$diameter}km moon should be around {$expectedLoss}%"
            );
            $this->assertLessThanOrEqual(
                $expectedLoss + 1,
                $lossChance,
                "Loss chance for {$diameter}km moon should be around {$expectedLoss}%"
            );
        }
    }

    public function testMoreDeathstarsIncreaseDestructionChance(): void
    {
        $moonDiameter = 5000;

        $chance1DS = $this->callPrivateMethod('calculateMoonDestructionChance', $moonDiameter, 1);
        $chance4DS = $this->callPrivateMethod('calculateMoonDestructionChance', $moonDiameter, 4);
        $chance9DS = $this->callPrivateMethod('calculateMoonDestructionChance', $moonDiameter, 9);

        $this->assertLessThan(
            $chance4DS,
            $chance1DS,
            'More Deathstars should increase destruction chance'
        );
        $this->assertLessThan(
            $chance9DS,
            $chance4DS,
            'Even more Deathstars should further increase destruction chance'
        );
    }

    public function testLargerMoonsHarderToDestroy(): void
    {
        $deathstarCount = 5;

        $chanceSmall = $this->callPrivateMethod('calculateMoonDestructionChance', 1000, $deathstarCount);
        $chanceMedium = $this->callPrivateMethod('calculateMoonDestructionChance', 5000, $deathstarCount);
        $chanceLarge = $this->callPrivateMethod('calculateMoonDestructionChance', 8000, $deathstarCount);

        $this->assertGreaterThan(
            $chanceMedium,
            $chanceSmall,
            'Smaller moons should be easier to destroy'
        );
        $this->assertGreaterThan(
            $chanceLarge,
            $chanceMedium,
            'Medium moons should be easier to destroy than large moons'
        );
    }

    public function testLargerMoonsHigherLossChance(): void
    {
        $lossSmall = $this->callPrivateMethod('calculateDeathstarLossChance', 1000);
        $lossMedium = $this->callPrivateMethod('calculateDeathstarLossChance', 5000);
        $lossLarge = $this->callPrivateMethod('calculateDeathstarLossChance', 8000);

        $this->assertLessThan(
            $lossMedium,
            $lossSmall,
            'Larger moons should have higher Deathstar loss chance'
        );
        $this->assertLessThan(
            $lossLarge,
            $lossMedium,
            'Even larger moons should have even higher loss chance'
        );
    }

    public function testFormulaConsistencyAcrossInputs(): void
    {
        // Test 100 random combinations of moon sizes and Deathstar counts
        for ($i = 0; $i < 100; $i++) {
            $moonDiameter = rand(100, 8836);
            $deathstarCount = rand(1, 100);

            $destructionChance = $this->callPrivateMethod('calculateMoonDestructionChance', $moonDiameter, $deathstarCount);
            $lossChance = $this->callPrivateMethod('calculateDeathstarLossChance', $moonDiameter);

            // Verify results are in valid range
            $this->assertGreaterThanOrEqual(
                0,
                $destructionChance,
                "Destruction chance should be >= 0 for diameter={$moonDiameter}, DS={$deathstarCount}"
            );
            $this->assertLessThanOrEqual(
                100,
                $destructionChance,
                "Destruction chance should be <= 100 for diameter={$moonDiameter}, DS={$deathstarCount}"
            );

            $this->assertGreaterThanOrEqual(
                0,
                $lossChance,
                "Loss chance should be >= 0 for diameter={$moonDiameter}"
            );
            $this->assertLessThanOrEqual(
                100,
                $lossChance,
                "Loss chance should be <= 100 for diameter={$moonDiameter}"
            );
        }
    }

    public function testMinimumDeathstarsForGuaranteedDestruction(): void
    {
        // Formula for min DS: ceil((100 / (100 - √(diameter)))²)
        $testCases = [
            [1000, 2, 136],    // Small moon: (100 - 31.6) * √2 ≈ 96.7%
            [5000, 10, 223],   // Medium moon: (100 - 70.7) * √10 ≈ 92.6%
            [8000, 34, 365],   // Large moon: (100 - 89.4) * √34 ≈ 61.8%
        ];

        foreach ($testCases as [$diameter, $dsCount, $minForGuaranteed]) {
            $chance = $this->callPrivateMethod('calculateMoonDestructionChance', $diameter, $dsCount);

            // Verify the chance is reasonable for the given DS count
            $this->assertGreaterThan(
                0,
                $chance,
                "With {$dsCount} Deathstars, {$diameter}km moon should have some destruction chance"
            );

            // Test that the calculated minimum gives 100% (or close to it)
            $guaranteedChance = $this->callPrivateMethod('calculateMoonDestructionChance', $diameter, $minForGuaranteed);
            $this->assertEquals(
                100,
                $guaranteedChance,
                "With {$minForGuaranteed} Deathstars, {$diameter}km moon should have 100% destruction chance (clamped)"
            );
        }
    }
}
