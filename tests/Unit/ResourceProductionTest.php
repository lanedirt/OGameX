<?php

namespace Tests\Unit;

use Illuminate\Contracts\Container\BindingResolutionException;
use OGame\Models\Resources;
use OGame\Services\SettingsService;
use Tests\UnitTestCase;

class ResourceProductionTest extends UnitTestCase
{
    /**
     * Set up common test components.
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpPlanetService();

        // Set the universe speed to 1x for this test.
        $settingsService = app()->make(SettingsService::class);
        $settingsService->set('economy_speed', 1);
    }

    /**
     * Mock test for metal mine production with positive energy production.
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
     * Mock test for metal mine production with zero energy production.
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
        $this->assertEquals(30, $this->planetService->getMetalProductionPerHour());
        $this->assertEquals(15, $this->planetService->getCrystalProductionPerHour());
        $this->assertEquals(0, $this->planetService->getDeuteriumProductionPerHour());
    }

    /**
     * Mock test for metal mine production with zero energy production.
     */
    public function testDeductTooManyResources(): void
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

        // Specify the type of exception you expect to be thrown
        $this->expectException(\Exception::class);

        // Call the method that should throw the exception
        $this->planetService->deductResources(new Resources(9999, 9999, 9999, 0));
    }
}
