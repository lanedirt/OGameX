<?php

namespace Tests\Unit;

use Illuminate\Contracts\Container\BindingResolutionException;
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

        // Assertions for production values with zero energy
        $this->assertEquals(240, $this->planetService->getMetalProductionPerHour());
        $this->assertEquals(120, $this->planetService->getCrystalProductionPerHour());
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
        ]);

        // Currently solar satellites produce fixed 20 energy each.
        // So 100 satellites should produce 2000 energy.
        // @TODO: this will need to be updated once the planet temperature
        // is added and the energy production formula is updated.
        $this->assertEquals(2000, $this->planetService->energyProduction()->get());
    }
}
