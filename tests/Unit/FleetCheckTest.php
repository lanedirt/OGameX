<?php

namespace Tests\Unit;

use OGame\Factories\PlanetServiceFactory;
use OGame\Planet;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use PHPUnit\Framework\TestCase;

class FleetCheckTest extends TestCase
{
    protected $planetService;

    /**
     * Set up common test components.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Initialize empty playerService object directly without factory as we do not
        // actually want to load a player from the database.
        $playerService = app()->make(PlayerService::class, ['player_id' => 0]);
        // Initialize the planet service with factory.
        $planetServiceFactory =  app()->make(PlanetServiceFactory::class);
        $this->planetService = $planetServiceFactory->makeForPlayer($playerService, 0);
    }

    /**
     * Helper method to create a planet model and configure it.
     */
    protected function createAndSetPlanetModel(array $attributes): void
    {
        // Create fake planet eloquent model with additional attributes
        $planetModelFake = Planet::factory()->make($attributes);
        // Set the fake model to the planet service
        $this->planetService->setPlanet($planetModelFake);
    }

    /**
     * Mock test for checking positive fleet amount check on a planet.
     */
    public function testFleetAmountCheckPositive(): void
    {
        $this->createAndSetPlanetModel([
            'small_cargo' => 10,
            'destroyer' => 3,
            'espionage_probe' => 2,
        ]);

        // Verify that multiple ships count up to the sum of the ships.
        $this->assertEquals(15, $this->planetService->getFlightShipAmount());
    }

    /**
     * Mock test for checking zero fleet amount check on a planet.
     */
    public function testFleetAmountCheckZero(): void
    {
        $this->createAndSetPlanetModel([
            'solar_satellite' => 3,
        ]);

        // Verify that amount of ships returns 0 as there are no ships that can fly.
        $this->assertEquals(0, $this->planetService->getFlightShipAmount());
    }
}
