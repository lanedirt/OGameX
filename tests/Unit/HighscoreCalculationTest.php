<?php

namespace Tests\Unit;

use OGame\Planet;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use PHPUnit\Framework\TestCase;

class HighscoreCalculationTest extends TestCase
{
    protected $planetService;

    /**
     * Set up common test components.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Initialize empty playerService object
        $playerService = app()->make(PlayerService::class, ['player_id' => 0]);
        // Initialize the planet service before each test
        $this->planetService = app()->make(PlanetService::class, ['player' => $playerService, 'planet_id' => 0]);
    }

    /**
     * Helper method to create a planet model with preconfigured levels.
     */
    protected function createAndConfigurePlanetModel(array $attributes): void
    {
        // Create fake planet eloquent model with additional attributes
        $planetModelFake = Planet::factory()->make($attributes);
        // Set the fake model to the planet service
        $this->planetService->setPlanet($planetModelFake);
    }

    /**
     * Test that the planet score is calculated correctly based on building levels.
     */
    public function testBuildingScore(): void
    {
        $this->createAndConfigurePlanetModel([
            'metal_mine' => 10,
            'crystal_mine' => 10,
            'solar_plant' => 10,
        ]);

        // Check that the score is calculated correctly based on spent resouces for above.
        // buildings = 33k = 33
        $this->assertEquals(33, $this->planetService->getGeneralScore());
    }

    /**
     * Test that the planet score is calculated correctly based on unit amounts.
     */
    public function testUnitScore(): void
    {
        $this->createAndConfigurePlanetModel([
            'light_fighter' => 10,
            'battle_ship' => 10,
        ]);

        // Check that the score is calculated correctly based on spent resouces for above.
        // light fighter = 4k * 10 = 40
        // battleship = 60k * 10 = 600
        $this->assertEquals(640, $this->planetService->getGeneralScore());
    }

    /**
     * Test that the planet score is calculated correctly based on building levels and unit count combined.
     */
    public function testBuildingUnitScore(): void
    {
        $this->createAndConfigurePlanetModel([
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
        $this->assertEquals(673, $this->planetService->getGeneralScore());
    }
}
