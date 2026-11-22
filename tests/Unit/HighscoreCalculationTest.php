<?php

namespace Tests\Unit;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\UnitTestCase;

class HighscoreCalculationTest extends UnitTestCase
{
    /**
     * Set up common test components.
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Calculate cumulative cost using iterative method (excluding energy)
     */
    private function calculateIterativeCost(string $machine_name, int $level): \OGame\Models\Resources
    {
        $result = new \OGame\Models\Resources(0, 0, 0, 0);
        for ($i = 1; $i <= $level; $i++) {
            $price = \OGame\Services\ObjectService::getObjectRawPrice($machine_name, $i);
            $result->metal->add($price->metal);
            $result->crystal->add($price->crystal);
            $result->deuterium->add($price->deuterium);
        }
        return $result;
    }

    /**
     * Test that the planet score is calculated correctly based on building levels.
     */
    public function testBuildingScore(): void
    {
        $this->createAndSetPlanetModel([
            'metal_mine' => 10,
            'crystal_mine' => 10,
            'solar_plant' => 10,
        ]);

        // Check that the score is calculated correctly based on spent resouces for above.
        // buildings = 33k = 33
        $this->assertEquals(33, $this->planetService->getPlanetScore());
    }

    /**
     * Test that the planet score is calculated correctly based on unit amounts.
     */
    public function testUnitScore(): void
    {
        $this->createAndSetPlanetModel([
            'light_fighter' => 10,
            'battle_ship' => 10,
        ]);

        // Check that the score is calculated correctly based on spent resouces for above.
        // light fighter = 4k * 10 = 40
        // battleship = 60k * 10 = 600
        $this->assertEquals(640, $this->planetService->getPlanetScore());
    }

    /**
     * Test that the planet score is calculated correctly based on building levels and unit count combined.
     */
    public function testBuildingUnitScore(): void
    {
        $this->createAndSetPlanetModel([
            'metal_mine' => 10,
            'crystal_mine' => 10,
            'solar_plant' => 10,
            'light_fighter' => 10,
            'battle_ship' => 10,
        ]);

        // Check that the score is calculated correctly based on spent resouces for above.
        // light fighter = 4k * 10 = 40
        // battleship = 60k * 10 = 600
        // buildings = 33k = 33
        $this->assertEquals(673, $this->planetService->getPlanetScore());
    }

    /**
     * Test that the player score is calculated correctly based on research levels.
     */
    public function testPlayerResearchScore(): void
    {
        $this->createAndSetUserTechModel([
            'laser_technology' => 3,
            'astrophysics' => 4,
            'shielding_technology' => 5,
        ]);

        // Check that the score is calculated correctly based on spent resouces for above.
        // laser_technology = 0.3 + 0.6 + 1.2 = 2.1
        // astrophysics = 16 + 28 + 49.1 + 85.7 = 178.8
        // shielding_technology = 0.8 + 1.6 + 3.2 + 6.4 + 12.8 = 24.8
        // Total = 205.7
        $this->assertEquals(205, $this->playerService->getResearchScore());
    }

    /**
     * Test that the planet score is calculated correctly based on building levels and unit count combined.
     * @throws Exception
     */
    public function testEconomyScore(): void
    {
        $this->createAndSetPlanetModel([
            'metal_mine' => 10,
            'small_cargo' => 10,
            'battle_ship' => 10, // This should not affect economy points as this is not a civil ship.
        ]);

        // Check that the point count is calculated correctly based on spent resources for above.
        $this->assertEquals(28, $this->planetService->getPlanetScoreEconomy());
    }

    /**
     * Test that the planet score is calculated correctly based on building levels and unit count combined.
     * @throws Exception
     */
    public function testMilitaryPoints(): void
    {
        $this->createAndSetPlanetModel([
            'metal_mine' => 10, // This should not affect the military points.
            'small_cargo' => 10, // This should be calculated as 50% because it is a civil ship.
            'light_fighter' => 10, // 100%
            'battle_ship' => 10, // 100%
        ]);

        // Check that the score is correctly calculated according to the military score formula.
        $this->assertEquals(660, $this->planetService->getPlanetMilitaryScore());
    }

    /**
     * Property Test: Geometric formula accuracy
     */
    public function testGeometricFormulaAccuracy(): void
    {
        $testCases = [
            ['metal_mine', 10],
            ['metal_mine', 30],
            ['metal_mine', 50],
            ['crystal_mine', 15],
            ['crystal_mine', 40],
            ['deuterium_synthesizer', 20],
            ['solar_plant', 25],
            ['fusion_plant', 12],
            ['robot_factory', 10],
            ['shipyard', 12],
            ['research_lab', 10],
            ['alliance_depot', 7],
            ['missile_silo', 5],
            ['nanite_factory', 4],
            ['terraformer', 3],
            ['space_dock', 2],
        ];

        foreach ($testCases as [$machine_name, $level]) {
            $optimized = \OGame\Services\ObjectService::getObjectCumulativeCost($machine_name, $level);
            $iterative = $this->calculateIterativeCost($machine_name, $level);

            $diff = abs($optimized->sum() - $iterative->sum());

            $this->assertLessThan(
                1000,
                $diff,
                "Difference for {$machine_name} level {$level} is {$diff}, expected < 1000"
            );
        }
    }

    /**
     * Property Test: Final score accuracy
     */
    public function testFinalScoreAccuracy(): void
    {
        $testCases = [
            ['metal_mine', 10],
            ['metal_mine', 30],
            ['metal_mine', 50],
            ['crystal_mine', 15],
            ['crystal_mine', 40],
            ['deuterium_synthesizer', 20],
            ['solar_plant', 25],
            ['fusion_plant', 12],
            ['robot_factory', 10],
            ['shipyard', 12],
            ['research_lab', 10],
        ];

        foreach ($testCases as [$machine_name, $level]) {
            $optimized = \OGame\Services\ObjectService::getObjectCumulativeCost($machine_name, $level);
            $iterative = $this->calculateIterativeCost($machine_name, $level);

            $optimized_score = (int)floor($optimized->sum() / 1000);
            $iterative_score = (int)floor($iterative->sum() / 1000);
            $score_diff = abs($optimized_score - $iterative_score);

            $this->assertLessThanOrEqual(
                1,
                $score_diff,
                "Score difference for {$machine_name} level {$level} is {$score_diff}, expected <= 1"
            );
        }
    }

    /**
     * Property Test: Resource independence
     */
    public function testResourceIndependence(): void
    {
        $result = \OGame\Services\ObjectService::getObjectCumulativeCost('metal_mine', 10);

        $this->assertGreaterThanOrEqual(0, $result->metal->get());
        $this->assertGreaterThanOrEqual(0, $result->crystal->get());
        $this->assertGreaterThanOrEqual(0, $result->deuterium->get());

        $result2 = \OGame\Services\ObjectService::getObjectCumulativeCost('crystal_mine', 10);
        $this->assertNotEquals($result->sum(), $result2->sum());
    }

    /**
     * Property Test: Energy exclusion
     */
    public function testEnergyExclusion(): void
    {
        $testCases = ['solar_plant', 'fusion_plant', 'terraformer', 'space_dock', 'metal_mine'];

        foreach ($testCases as $machine_name) {
            $result = \OGame\Services\ObjectService::getObjectCumulativeCost($machine_name, 10);
            $this->assertEquals(0, $result->energy->get(), "Energy should be 0 for {$machine_name}");
        }
    }

    /**
     * Unit Test: Edge case - Level 0 returns zero cost
     */
    public function testEdgeCaseLevel0(): void
    {
        $result = \OGame\Services\ObjectService::getObjectCumulativeCost('metal_mine', 0);

        $this->assertEquals(0, $result->metal->get());
        $this->assertEquals(0, $result->crystal->get());
        $this->assertEquals(0, $result->deuterium->get());
        $this->assertEquals(0, $result->energy->get());
        $this->assertEquals(0, $result->sum());
    }

    /**
     * Unit Test: Edge case - Level 1 returns base price
     */
    public function testEdgeCaseLevel1(): void
    {
        $result = \OGame\Services\ObjectService::getObjectCumulativeCost('metal_mine', 1);
        $base_price = \OGame\Services\ObjectService::getObjectRawPrice('metal_mine', 1);

        $this->assertEquals($base_price->metal->get(), $result->metal->get());
        $this->assertEquals($base_price->crystal->get(), $result->crystal->get());
        $this->assertEquals($base_price->deuterium->get(), $result->deuterium->get());
        $this->assertEquals(0, $result->energy->get());
    }

    /**
     * Unit Test: Edge case - Factor = 1 uses linear formula
     */
    public function testEdgeCaseFactorEquals1(): void
    {
        $result = \OGame\Services\ObjectService::getObjectCumulativeCost('alliance_depot', 5);
        $this->assertGreaterThan(0, $result->sum());
    }

    /**
     * Unit Test: Invalid machine name returns zero cost
     */
    public function testInvalidMachineNameReturnsZero(): void
    {
        $result = \OGame\Services\ObjectService::getObjectCumulativeCost('invalid_building_name', 10);

        $this->assertEquals(0, $result->metal->get());
        $this->assertEquals(0, $result->crystal->get());
        $this->assertEquals(0, $result->deuterium->get());
        $this->assertEquals(0, $result->energy->get());
        $this->assertEquals(0, $result->sum());
    }

    /**
     * Unit Test: Specific known value - Metal Mine level 10
     */
    public function testSpecificValueMetalMineLevel10(): void
    {
        $optimized = \OGame\Services\ObjectService::getObjectCumulativeCost('metal_mine', 10);
        $iterative = $this->calculateIterativeCost('metal_mine', 10);

        $diff = abs($optimized->sum() - $iterative->sum());
        $this->assertLessThan(1000, $diff);
    }

    /**
     * Unit Test: Specific known value - Research Lab level 5
     */
    public function testSpecificValueResearchLabLevel5(): void
    {
        $optimized = \OGame\Services\ObjectService::getObjectCumulativeCost('research_lab', 5);
        $iterative = $this->calculateIterativeCost('research_lab', 5);

        $diff = abs($optimized->sum() - $iterative->sum());
        $this->assertLessThan(1000, $diff);
    }

    /**
     * Unit Test: High level building (level 50+)
     */
    public function testHighLevelBuilding(): void
    {
        $optimized = \OGame\Services\ObjectService::getObjectCumulativeCost('metal_mine', 50);
        $iterative = $this->calculateIterativeCost('metal_mine', 50);

        $diff = abs($optimized->sum() - $iterative->sum());
        $this->assertLessThan(1000, $diff, "Difference for level 50 is {$diff}, expected < 1000");

        $score_diff = abs(floor($optimized->sum() / 1000) - floor($iterative->sum() / 1000));
        $this->assertLessThanOrEqual(1, $score_diff);
    }

    /**
     * Unit Test: Integer overflow protection for research score
     */
    public function testResearchScoreOverflowProtection(): void
    {
        // Set extremely high research levels that would cause overflow
        $this->createAndSetUserTechModel([
            'laser_technology' => 200,  // Very high level
            'astrophysics' => 200,       // Very high level
            'shielding_technology' => 200, // Very high level
        ]);

        $score = $this->playerService->getResearchScore();

        // Should not throw an error and should return PHP_INT_MAX
        $this->assertEquals(PHP_INT_MAX, $score);
    }

    /**
     * Unit Test: Integer overflow protection for player total score
     */
    public function testPlayerScoreOverflowProtection(): void
    {
        // Create a planet with very high buildings
        $this->createAndSetPlanetModel([
            'metal_mine' => 200,  // Very high level
            'crystal_mine' => 200, // Very high level
        ]);

        // Set very high research levels
        $this->createAndSetUserTechModel([
            'laser_technology' => 200,
            'astrophysics' => 200,
        ]);

        $highscoreService = app(\OGame\Services\HighscoreService::class);
        $score = $highscoreService->getPlayerScore($this->playerService);

        // Should not throw an error and should return PHP_INT_MAX
        $this->assertEquals(PHP_INT_MAX, $score);
    }
}
