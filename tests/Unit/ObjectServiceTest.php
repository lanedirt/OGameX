<?php

namespace Tests\Unit;

use OGame\Services\ObjectService;
use Tests\UnitTestCase;

class ObjectServiceTest extends UnitTestCase
{
    /**
     * Tests maximum building amount returns correct value.
     */
    public function testGetObjectMaxBuildAmount(): void
    {
        $this->createAndSetPlanetModel([]);

        // Test with requirements not met
        $max_build_amount = ObjectService::getObjectMaxBuildAmount('plasma_turret', $this->planetService, false);
        $this->assertEquals(0, $max_build_amount);

        // Test with object limited to one instance per user
        $max_build_amount = ObjectService::getObjectMaxBuildAmount('small_shield_dome', $this->planetService, true);
        $this->assertEquals(1, $max_build_amount);

        $this->createAndSetPlanetModel([
            'small_shield_dome' => 1,
        ]);

        // Test with object limited to one instance which already exists
        $maxBuildAmount = ObjectService::getObjectMaxBuildAmount('small_shield_dome', $this->planetService, true);
        $this->assertEquals(0, $maxBuildAmount);

        $this->createAndSetPlanetModel([
            'metal' => 24000,
            'crystal' => 6000,
            'missile_silo' => 1,
        ]);

        // Test it calculates max amount correctly
        $maxBuildAmount = ObjectService::getObjectMaxBuildAmount('anti_ballistic_missile', $this->planetService, true);
        $this->assertEquals(3, $maxBuildAmount);
    }

    /**
     * Test downgrade price calculation without Ion technology bonus.
     */
    public function testGetObjectDowngradePriceWithoutIonBonus(): void
    {
        $this->createAndSetPlanetModel([
            'metal_mine' => 5,
        ]);
        $this->createAndSetUserTechModel([
            'ion_technology' => 0,
        ]);

        // Get upgrade cost from level 4 to 5
        $upgrade_cost = ObjectService::getObjectRawPrice('metal_mine', 5);

        // Get downgrade cost (should be 62.5% of upgrade cost)
        $downgrade_cost = ObjectService::getObjectDowngradePrice('metal_mine', $this->planetService);

        // Verify downgrade cost is 62.5% of upgrade cost
        $expected_metal = floor($upgrade_cost->metal->get() * 0.625);
        $expected_crystal = floor($upgrade_cost->crystal->get() * 0.625);
        $expected_deuterium = floor($upgrade_cost->deuterium->get() * 0.625);

        $this->assertEquals($expected_metal, $downgrade_cost->metal->get());
        $this->assertEquals($expected_crystal, $downgrade_cost->crystal->get());
        $this->assertEquals($expected_deuterium, $downgrade_cost->deuterium->get());
    }

    /**
     * Test downgrade price calculation with Ion technology bonus.
     */
    public function testGetObjectDowngradePriceWithIonBonus(): void
    {
        $this->createAndSetPlanetModel([
            'metal_mine' => 5,
        ]);
        $this->createAndSetUserTechModel([
            'ion_technology' => 24, // 24% reduction
        ]);

        // Get upgrade cost from level 4 to 5
        $upgrade_cost = ObjectService::getObjectRawPrice('metal_mine', 5);

        // Get downgrade cost (should be 62.5% of upgrade cost, then reduced by 24%)
        $downgrade_cost = ObjectService::getObjectDowngradePrice('metal_mine', $this->planetService);

        // Calculate expected cost: 62.5% * (1 - 0.24) = 62.5% * 0.76
        $base_downgrade_metal = $upgrade_cost->metal->get() * 0.625;
        $base_downgrade_crystal = $upgrade_cost->crystal->get() * 0.625;
        $base_downgrade_deuterium = $upgrade_cost->deuterium->get() * 0.625;

        $expected_metal = floor($base_downgrade_metal * (1 - 0.24));
        $expected_crystal = floor($base_downgrade_crystal * (1 - 0.24));
        $expected_deuterium = floor($base_downgrade_deuterium * (1 - 0.24));

        $this->assertEquals($expected_metal, $downgrade_cost->metal->get());
        $this->assertEquals($expected_crystal, $downgrade_cost->crystal->get());
        $this->assertEquals($expected_deuterium, $downgrade_cost->deuterium->get());
    }

    /**
     * Test downgrade price for level 0 building returns zero cost.
     */
    public function testGetObjectDowngradePriceLevelZero(): void
    {
        $this->createAndSetPlanetModel([
            'metal_mine' => 0,
        ]);

        $downgrade_cost = ObjectService::getObjectDowngradePrice('metal_mine', $this->planetService);

        $this->assertEquals(0, $downgrade_cost->metal->get());
        $this->assertEquals(0, $downgrade_cost->crystal->get());
        $this->assertEquals(0, $downgrade_cost->deuterium->get());
    }

    /**
     * Test canDowngradeBuilding returns false for level 0.
     */
    public function testCanDowngradeBuildingLevelZero(): void
    {
        $this->createAndSetPlanetModel([
            'metal_mine' => 0,
        ]);

        $can_downgrade = ObjectService::canDowngradeBuilding('metal_mine', $this->planetService);
        $this->assertFalse($can_downgrade);
    }

    /**
     * Test canDowngradeBuilding returns true when no dependencies.
     */
    public function testCanDowngradeBuildingNoDependencies(): void
    {
        $this->createAndSetPlanetModel([
            'metal_mine' => 5,
        ]);

        $can_downgrade = ObjectService::canDowngradeBuilding('metal_mine', $this->planetService);
        $this->assertTrue($can_downgrade);
    }
}
