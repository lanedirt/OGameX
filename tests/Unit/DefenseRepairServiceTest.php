<?php

namespace Tests\Unit;

use OGame\GameMissions\BattleEngine\Services\DefenseRepairService;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Services\ObjectService;
use Tests\UnitTestCase;

/**
 * Test DefenseRepairService functionality.
 */
class DefenseRepairServiceTest extends UnitTestCase
{
    /**
     * For any large set of destroyed defenses (N > 100) and repair rate R,
     * the percentage of repaired defenses should converge to R% with a tolerance of ±5%.
     */
    public function testRepairRateStatisticalConvergence(): void
    {
        $repairRate = 70;
        $service = new DefenseRepairService($repairRate);

        $rocketLauncher = ObjectService::getUnitObjectByMachineName('rocket_launcher');

        $totalDestroyed = 0;
        $totalRepaired = 0;
        $iterations = 100;
        $unitsPerIteration = 100;

        for ($i = 0; $i < $iterations; $i++) {
            $destroyedDefenses = new UnitCollection();
            $destroyedDefenses->addUnit($rocketLauncher, $unitsPerIteration);

            $repaired = $service->calculateRepairedDefenses($destroyedDefenses);

            $totalDestroyed += $unitsPerIteration;
            $totalRepaired += $repaired->getAmountByMachineName('rocket_launcher');
        }

        $actualRate = ($totalRepaired / $totalDestroyed) * 100;

        // Allow ±5% tolerance
        $this->assertGreaterThanOrEqual(
            $repairRate - 5,
            $actualRate,
            "Repair rate {$actualRate}% is below expected range ({$repairRate}% ± 5%)"
        );
        $this->assertLessThanOrEqual(
            $repairRate + 5,
            $actualRate,
            "Repair rate {$actualRate}% is above expected range ({$repairRate}% ± 5%)"
        );
    }

    /**
     * For any seed value and destroyed defense collection, calling calculateRepairedDefenses
     * with the same seed should always produce identical results.
     */
    public function testDeterministicModeProducesReproducibleResults(): void
    {
        $seed = 12345;
        $repairRate = 70;

        $rocketLauncher = ObjectService::getUnitObjectByMachineName('rocket_launcher');
        $lightLaser = ObjectService::getUnitObjectByMachineName('light_laser');

        $destroyedDefenses = new UnitCollection();
        $destroyedDefenses->addUnit($rocketLauncher, 50);
        $destroyedDefenses->addUnit($lightLaser, 30);

        // Run multiple times with same seed
        $results = [];
        for ($i = 0; $i < 5; $i++) {
            $service = new DefenseRepairService($repairRate, $seed);
            $repaired = $service->calculateRepairedDefenses($destroyedDefenses);
            $results[] = $repaired->toArray();
        }

        // All results should be identical
        for ($i = 1; $i < count($results); $i++) {
            $this->assertEquals(
                $results[0],
                $results[$i],
                "Result {$i} differs from result 0 with same seed"
            );
        }
    }

    /**
     * Test 0% repair rate returns empty collection.
     */
    public function testZeroRepairRateReturnsEmptyCollection(): void
    {
        $service = new DefenseRepairService(0);

        $rocketLauncher = ObjectService::getUnitObjectByMachineName('rocket_launcher');
        $destroyedDefenses = new UnitCollection();
        $destroyedDefenses->addUnit($rocketLauncher, 100);

        $repaired = $service->calculateRepairedDefenses($destroyedDefenses);

        $this->assertEquals(
            0,
            $repaired->getAmount(),
            "With 0% repair rate, no defenses should be repaired"
        );
    }

    /**
     * Test 100% repair rate returns all destroyed defenses.
     */
    public function testFullRepairRateReturnsAllDefenses(): void
    {
        $service = new DefenseRepairService(100);

        $rocketLauncher = ObjectService::getUnitObjectByMachineName('rocket_launcher');
        $lightLaser = ObjectService::getUnitObjectByMachineName('light_laser');

        $destroyedDefenses = new UnitCollection();
        $destroyedDefenses->addUnit($rocketLauncher, 50);
        $destroyedDefenses->addUnit($lightLaser, 30);

        $repaired = $service->calculateRepairedDefenses($destroyedDefenses);

        $this->assertEquals(50, $repaired->getAmountByMachineName('rocket_launcher'));
        $this->assertEquals(30, $repaired->getAmountByMachineName('light_laser'));
    }

    /**
     * Test that only defense units are processed (ships are ignored).
     */
    public function testOnlyDefenseUnitsAreProcessed(): void
    {
        $service = new DefenseRepairService(100); // 100% to ensure all defenses would be repaired

        $rocketLauncher = ObjectService::getUnitObjectByMachineName('rocket_launcher');
        $lightFighter = ObjectService::getUnitObjectByMachineName('light_fighter');

        $destroyedUnits = new UnitCollection();
        $destroyedUnits->addUnit($rocketLauncher, 50);
        $destroyedUnits->addUnit($lightFighter, 30); // Ship should be ignored

        $repaired = $service->calculateRepairedDefenses($destroyedUnits);

        $this->assertEquals(
            50,
            $repaired->getAmountByMachineName('rocket_launcher'),
            "Defense units should be repaired"
        );
        $this->assertEquals(
            0,
            $repaired->getAmountByMachineName('light_fighter'),
            "Ships should not be repaired"
        );
    }

    /**
     * Test shield dome repair never exceeds 1.
     */
    public function testShieldDomeRepairNeverExceedsOne(): void
    {
        $service = new DefenseRepairService(100); // 100% to ensure repair

        $smallShieldDome = ObjectService::getUnitObjectByMachineName('small_shield_dome');
        $largeShieldDome = ObjectService::getUnitObjectByMachineName('large_shield_dome');

        // Even if somehow multiple shield domes were "destroyed", only 1 can be repaired
        $destroyedDefenses = new UnitCollection();
        $destroyedDefenses->addUnit($smallShieldDome, 5); // Hypothetical edge case
        $destroyedDefenses->addUnit($largeShieldDome, 3);

        $repaired = $service->calculateRepairedDefenses($destroyedDefenses);

        $this->assertLessThanOrEqual(
            1,
            $repaired->getAmountByMachineName('small_shield_dome'),
            "Small shield dome repair should not exceed 1"
        );
        $this->assertLessThanOrEqual(
            1,
            $repaired->getAmountByMachineName('large_shield_dome'),
            "Large shield dome repair should not exceed 1"
        );
    }

    /**
     * Test empty collection returns empty collection.
     */
    public function testEmptyCollectionReturnsEmptyCollection(): void
    {
        $service = new DefenseRepairService(70);

        $destroyedDefenses = new UnitCollection();
        $repaired = $service->calculateRepairedDefenses($destroyedDefenses);

        $this->assertEquals(0, $repaired->getAmount());
    }

    /**
     * Test different repair rates produce different average results.
     */
    public function testDifferentRepairRatesProduceDifferentResults(): void
    {
        $rocketLauncher = ObjectService::getUnitObjectByMachineName('rocket_launcher');

        $rates = [30, 50, 70, 90];
        $averages = [];

        foreach ($rates as $rate) {
            $service = new DefenseRepairService($rate);
            $totalRepaired = 0;
            $iterations = 50;

            for ($i = 0; $i < $iterations; $i++) {
                $destroyedDefenses = new UnitCollection();
                $destroyedDefenses->addUnit($rocketLauncher, 100);
                $repaired = $service->calculateRepairedDefenses($destroyedDefenses);
                $totalRepaired += $repaired->getAmountByMachineName('rocket_launcher');
            }

            $averages[$rate] = $totalRepaired / ($iterations * 100) * 100;
        }

        // Each higher rate should produce more repairs on average
        $this->assertLessThan($averages[50], $averages[30] + 15); // Allow some variance
        $this->assertLessThan($averages[70], $averages[50] + 15);
        $this->assertLessThan($averages[90], $averages[70] + 15);
    }
}
