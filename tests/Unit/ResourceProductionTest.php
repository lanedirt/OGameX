<?php

namespace Tests\Unit;

use Mockery;
use OGame\Planet;
use OGame\Services\ObjectService;
use OGame\User;
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

    /**
     * Mock test for metal mine production with positive energy production.
     */
    public function testMineProduction(): void
    {
        // Create fake planet eloquent model
        $planetModelFake = Planet::factory()->make();
        $planetService = app()->make(\OGame\Services\PlanetService::class);
        // Set the fake model to the planet service so we can test various methods..
        // Metal
        $planetModelFake->metal_mine_percent = 10;
        $planetModelFake->metal_mine = 20;
        // Crystal
        $planetModelFake->crystal_mine_percent = 10;
        $planetModelFake->crystal_mine = 20;
        // Deuterium
        $planetModelFake->deuterium_synthesizer_percent = 10;
        $planetModelFake->deuterium_synthesizer = 20;
        // Solar plant
        $planetModelFake->solar_plant = 20;
        $planetModelFake->solar_plant_percent = 10;
        $planetService->setPlanet($planetModelFake);

        // Update resource production stats.
        $planetService->updateResourceProductionStats(false);

        // Verify that resource production calculation equals > 1000 (base production + a lot)
        // because there is positive power production.
        $this->assertGreaterThan(1000, $planetService->getMetalProductionPerHour());
        $this->assertGreaterThan(1000, $planetService->getCrystalProductionPerHour());
        $this->assertGreaterThan(500, $planetService->getDeuteriumProductionPerHour());
    }

    /**
     * Mock test for metal mine production with zero energy production.
     */
    public function testMineProductionNoEnergy(): void
    {
        // Create fake planet eloquent model
        $planetModelFake = Planet::factory()->make();
        $planetService = app()->make(\OGame\Services\PlanetService::class);
        // Set the fake model to the planet service so we can test various methods..
        // Metal
        $planetModelFake->metal_mine_percent = 10;
        $planetModelFake->metal_mine = 20;
        // Crystal
        $planetModelFake->crystal_mine_percent = 10;
        $planetModelFake->crystal_mine = 20;
        // Deuterium
        $planetModelFake->deuterium_synthesizer_percent = 10;
        $planetModelFake->deuterium_synthesizer = 20;
        // Solar plant
        $planetModelFake->solar_plant = 0;
        $planetModelFake->solar_plant_percent = 10;
        $planetService->setPlanet($planetModelFake);

        // Update resource production stats.
        $planetService->updateResourceProductionStats(false);

        // Verify that resource production calculation equals 30 (base production)
        // because there is no power production.
        $this->assertEquals(30, $planetService->getMetalProductionPerHour());
        $this->assertEquals(15, $planetService->getCrystalProductionPerHour());
        $this->assertEquals(0, $planetService->getDeuteriumProductionPerHour());
    }
}
