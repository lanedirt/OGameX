<?php

namespace Tests\Unit;

use OGame\Services\SettingsService;
use Tests\UnitTestCase;

class ResourceProductionTest extends UnitTestCase
{
    /**
     * Set up common test components.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpPlanetService();

        // Set the universe speed to 1x for this test.
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('economy_speed', 8);
    }

    /**
     * Test metal mine production with positive energy production.
     */
    public function testMineProduction(): void
    {
        $this->createAndSetPlanetModel([
            'metal_mine_percent' => 10,
            'metal_mine' => 20,
            'crystal_mine_percent' => 10,
            'crystal_mine' => 20,
            'deuterium_synthesizer_percent' => 10,
            'deuterium_synthesizer' => 20,
            'solar_plant' => 20,
            'solar_plant_percent' => 10,
        ]);

        // Assertions for production values with positive energy
        $this->assertGreaterThan(1000, $this->planetService->getMetalProductionPerHour());
        $this->assertGreaterThan(1000, $this->planetService->getCrystalProductionPerHour());
        $this->assertGreaterThan(500, $this->planetService->getDeuteriumProductionPerHour());
    }

    /**
     * Test metal mine production with zero energy production.
     */
    public function testMineProductionNoEnergy(): void
    {
        $this->createAndSetPlanetModel([
            'metal_mine_percent' => 10,
            'metal_mine' => 20,
            'crystal_mine_percent' => 10,
            'crystal_mine' => 20,
            'deuterium_synthesizer_percent' => 10,
            'deuterium_synthesizer' => 20,
            'solar_plant' => 0,
            'solar_plant_percent' => 10,
        ]);

        $position = $this->planetService->getPlanetCoordinates()->position;

        // Get the bonus for the planet position
        $bonus_planet_position_bonuses = $this->planetService->getProductionForPositionBonuses($position);

        // Assertions for production values with zero energy
        $this->assertEquals(240 * $bonus_planet_position_bonuses["metal"], $this->planetService->getMetalProductionPerHour());
        $this->assertEquals(120 * $bonus_planet_position_bonuses["crystal"], $this->planetService->getCrystalProductionPerHour());
        $this->assertEquals(0, $this->planetService->getDeuteriumProductionPerHour());
    }

    /**
     * Test solar satellite energy production.
     */
    public function testSolarSatelliteEnergyProduction(): void
    {
        $this->createAndSetPlanetModel([
            'solar_satellite' => 100,
            'solar_satellite_percent' => 10,
            'temp_max' => 100
        ]);

        $this->assertEquals(4000, $this->planetService->energyProduction()->get());
    }

    /**
     * Test that fusion plant energy production depends on deuterium storage.
     */
    public function testFusionPlantEnergyProductionScalesWithDeuterium(): void
    {
        // Test with positive deuterium production
        $this->createAndSetPlanetModel([
            'deuterium_synthesizer' => 20,
            'deuterium_synthesizer_percent' => 10,
            'fusion_plant' => 20,
            'fusion_plant_percent' => 10,
            'deuterium' => 1000, // Some deuterium in storage
        ]);

        $fullEnergy = $this->planetService->energyProduction()->get();
        $this->assertGreaterThan(0, $fullEnergy, 'Fusion plant should produce energy when deuterium storage is above 0');

        // Test with negative deuterium production but deuterium still in storage
        $this->createAndSetPlanetModel([
            'deuterium_synthesizer' => 0,
            'deuterium_synthesizer_percent' => 10,
            'fusion_plant' => 20,
            'fusion_plant_percent' => 10,
            'deuterium' => 500, // Some deuterium in storage
        ]);

        $energyWithStorage = $this->planetService->energyProduction()->get();
        $this->assertEquals($fullEnergy, $energyWithStorage, 'Fusion plant should produce full energy when deuterium storage is above 0, regardless of production rate');

        // Test with positive deuterium production but no deuterium in storage
        $this->createAndSetPlanetModel([
            'deuterium_synthesizer' => 30,
            'deuterium_synthesizer_percent' => 10,
            'fusion_plant' => 20,
            'fusion_plant_percent' => 10,
            'deuterium' => 0, // No deuterium in storage
        ]);

        $noStorageEnergy = $this->planetService->energyProduction()->get();
        $this->assertGreaterThan(0, $noStorageEnergy, 'Fusion plant should produce energy when deuterium production is positive but deuterium storage is 0');

        // Test with negative deuterium production and no deuterium in storage
        $this->createAndSetPlanetModel([
            'deuterium_synthesizer' => 10,
            'deuterium_synthesizer_percent' => 10,
            'fusion_plant' => 20,
            'fusion_plant_percent' => 10,
            'deuterium' => 0, // No deuterium in storage
        ]);

        $noStorageEnergy = $this->planetService->energyProduction()->get();
        $this->assertEquals(0, $noStorageEnergy, 'Fusion plant should produce no energy when deuterium storage is 0');
    }

    /**
     * Test Planet Position and Plasma Technology bonus application to both mine and planet position bonus
     * Note: Plasma Technology does not apply to basic income, but Planet Slot does apply to basic income
     */
    public function testPlanetSlotAndPlasmaProductionBonus(): void
    {
        $this->createAndSetUserTechModel([
            'plasma_technology' => 12,
        ]);

        // base values breakdown (8x speed)
        // basic: metal = 30 * 8 = 240, crystal = 15 * 8 = 120, deuterium = 0
        // metal mine lv 20 = 30 * 8 * 20 * 1.1 ** 20 = 32_292
        // crystal mine lv 20 = 20 * 8 * 20 * 1.1 ** 20 = 21_528
        // deuterium mine lv 20 = 8 * 10 * 20 * 1.1 ** 20 * (1.44 - 0.004 * 47) = 13_477
        //      planet avg temp = (27 + 67) / 2 = 47

        // +35% metal production (basic + mine), +12% plasma tech
        $this->createAndSetPlanetModel([
            'planet' => 8,
            'metal_mine_percent' => 10,
            'metal_mine' => 20,
            'crystal_mine_percent' => 10,
            'crystal_mine' => 20,
            'deuterium_synthesizer_percent' => 10,
            'deuterium_synthesizer' => 20,
            'solar_plant' => 50, // ensures 100% production factor
            'solar_plant_percent' => 10,
            'temp_min' => 27,
            'temp_max' => 67,
        ]);

        $this->assertEquals(49_149, $this->planetService->getMetalProductionPerHour());
        $this->assertEquals(23_353, $this->planetService->getCrystalProductionPerHour());
        $this->assertEquals(14_010, $this->planetService->getDeuteriumProductionPerHour());

        // +40% crystal production, +7.92% (1+0.0066*12) plasma tech
        $this->createAndSetPlanetModel([
            'planet' => 1,
            'metal_mine_percent' => 10,
            'metal_mine' => 20,
            'crystal_mine_percent' => 10,
            'crystal_mine' => 20,
            'deuterium_synthesizer_percent' => 10,
            'deuterium_synthesizer' => 20,
            'solar_plant' => 50, // ensures 100% production factor
            'solar_plant_percent' => 10,
            'temp_min' => 27,
            'temp_max' => 67,
        ]);

        $this->assertEquals(36_407, $this->planetService->getMetalProductionPerHour());
        $this->assertEquals(32_694, $this->planetService->getCrystalProductionPerHour());
        $this->assertEquals(14_010, $this->planetService->getDeuteriumProductionPerHour());
    }
}
