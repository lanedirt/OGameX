<?php

namespace Tests\Unit;

use OGame\Planet;
use OGame\Services\PlanetService;
use PHPUnit\Framework\TestCase;

class ResourceProductionTest extends TestCase
{
    // Debug:
    // Build your mock object.
    /*$mockPlanet = Mockery::mock(new OGame\Services\PlanetService);

    // Have Laravel return the mocked object instead of the actual model.
    $this->app->instance('OGame\Services\PlanetService', $mockPlanet);

    // Tell your mocked instance what methods it should receive.
    $mockProduct
        ->shouldReceive('findByItemCode')
        ->once()
        ->andReturn(false);*/

    protected $planetService;

    /**
     * Mock test for metal mine production with positive energy production.
     */
    public function testMineProduction(): void
    {
        $this->createAndConfigurePlanetModel([
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
     * Helper method to create a planet model with mine configurations and update resource stats.
     */
    protected function createAndConfigurePlanetModel(array $attributes, bool $updateStats = true): void
    {
        // Create fake planet eloquent model with additional attributes
        $planetModelFake = Planet::factory()->make($attributes);
        // Set the fake model to the planet service
        $this->planetService->setPlanet($planetModelFake);

        if ($updateStats) {
            // Update resource production stats
            $this->planetService->updateResourceProductionStats(false);
        }
    }

    /**
     * Mock test for metal mine production with zero energy production.
     */
    public function testMineProductionNoEnergy(): void
    {
        $this->createAndConfigurePlanetModel([
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
     * Set up common test components.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Initialize the planet service before each test
        $this->planetService = app()->make(PlanetService::class);
    }
}
