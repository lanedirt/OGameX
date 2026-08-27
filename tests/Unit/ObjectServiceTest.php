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

        // Get construction cost for level 5 (cost to build from level 4 to 5)
        $construction_cost = ObjectService::getObjectRawPrice('metal_mine', 5);

        // Get downgrade cost (should equal construction cost without bonus)
        $downgrade_cost = ObjectService::getObjectDowngradePrice('metal_mine', $this->planetService);

        // Verify downgrade cost equals construction cost (no bonus applied)
        $expected_metal = floor($construction_cost->metal->get());
        $expected_crystal = floor($construction_cost->crystal->get());
        $expected_deuterium = floor($construction_cost->deuterium->get());

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
            'ion_technology' => 6, // 6 levels = 24% reduction (4% per level)
        ]);

        // Get construction cost for level 5 (cost to build from level 4 to 5)
        $construction_cost = ObjectService::getObjectRawPrice('metal_mine', 5);

        // Get downgrade cost (should equal construction cost, then reduced by Ion technology bonus)
        $downgrade_cost = ObjectService::getObjectDowngradePrice('metal_mine', $this->planetService);

        // Calculate expected cost: construction cost * (1 - 0.24) = construction cost * 0.76
        // Ion technology level 6 = 6 * 4% = 24% reduction
        $ion_bonus = 6 * 0.04; // 0.24 = 24%
        $expected_metal = floor($construction_cost->metal->get() * (1 - $ion_bonus));
        $expected_crystal = floor($construction_cost->crystal->get() * (1 - $ion_bonus));
        $expected_deuterium = floor($construction_cost->deuterium->get() * (1 - $ion_bonus));

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

    /**
     * Test canDowngradeBuilding returns false for Terraformer (permanent building).
     */
    public function testCanDowngradeBuildingTerraformerIsPermanent(): void
    {
        $this->createAndSetPlanetModel([
            'terraformer' => 1,
        ]);

        $can_downgrade = ObjectService::canDowngradeBuilding('terraformer', $this->planetService);
        $this->assertFalse($can_downgrade);
    }

    /**
     * Test getRecursiveRequirements returns all prerequisites for a technology.
     * Example: shielding_technology requires research_lab level 6 and energy_technology level 3.
     */
    public function testGetRecursiveRequirementsForTechnology(): void
    {
        $requirements = ObjectService::getRecursiveRequirements('shielding_technology');

        // shielding_technology requires: research_lab 6, energy_technology 3
        $this->assertArrayHasKey('research_lab', $requirements);
        $this->assertArrayHasKey('energy_technology', $requirements);
        $this->assertEquals(6, $requirements['research_lab']);
        $this->assertEquals(3, $requirements['energy_technology']);
    }

    /**
     * Test getRecursiveRequirements returns nested prerequisites.
     * Example: hyperspace_drive requires hyperspace_technology, which requires shielding_technology,
     * which requires energy_technology - so all should be included.
     */
    public function testGetRecursiveRequirementsNestedDependencies(): void
    {
        $requirements = ObjectService::getRecursiveRequirements('hyperspace_drive');

        // hyperspace_drive requires: research_lab 7, hyperspace_technology 3
        // hyperspace_technology requires: research_lab 7, energy_technology 5, shielding_technology 5
        // shielding_technology requires: research_lab 6, energy_technology 3
        $this->assertArrayHasKey('research_lab', $requirements);
        $this->assertArrayHasKey('hyperspace_technology', $requirements);
        $this->assertArrayHasKey('shielding_technology', $requirements);
        $this->assertArrayHasKey('energy_technology', $requirements);

        // Should have highest required level for each
        $this->assertEquals(7, $requirements['research_lab']); // max of 7 and 6
        $this->assertEquals(5, $requirements['energy_technology']); // max of 5 and 3
        $this->assertEquals(5, $requirements['shielding_technology']);
        $this->assertEquals(3, $requirements['hyperspace_technology']);
    }

    /**
     * Test getRecursiveRequirements returns empty array for object with no requirements.
     */
    public function testGetRecursiveRequirementsNoRequirements(): void
    {
        // metal_mine has no requirements
        $requirements = ObjectService::getRecursiveRequirements('metal_mine');
        $this->assertEmpty($requirements);
    }

    /**
     * Test getRecursiveRequirements returns empty array for invalid object.
     */
    public function testGetRecursiveRequirementsInvalidObject(): void
    {
        $requirements = ObjectService::getRecursiveRequirements('nonexistent_object');
        $this->assertEmpty($requirements);
    }
}
