<?php

namespace Tests;

use Illuminate\Contracts\Container\BindingResolutionException;
use OGame\Factories\PlanetServiceFactory;
use OGame\Models\Planet;
use OGame\Models\UserTech;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;

abstract class UnitTestCase extends TestCase
{
    protected PlayerService $playerService;
    protected PlanetService $planetService;

    /**
     * Set up common test components.
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpPlayerService();
        $this->setUpPlanetService();
    }

    /**
     * Helper method to create a planet model and configure it.
     *
     * @param array<string, int> $attributes
     */
    protected function createAndSetPlanetModel(array $attributes): void
    {
        // Create fake planet eloquent model with additional attributes
        $planetModelFake = Planet::factory()->make($attributes);
        // Set the fake model to the planet service
        $this->planetService->setPlanet($planetModelFake);
        // Update resource production stats
        $this->planetService->updateResourceProductionStats(false);
    }

    /**
     * Helper method to create a user tech model with preconfigured levels.
     *
     * @param array<string, int> $attributes
     */
    protected function createAndSetUserTechModel(array $attributes): void
    {
        // Create fake user tech eloquent model with additional attributes
        $userTechModelFake = UserTech::factory()->make($attributes);
        // Set the fake model to the planet service
        $this->playerService->setUserTech($userTechModelFake);
    }

    /**
     * Set up the planet service for testing.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function setUpPlanetService(): void
    {
        // Initialize the planet service with factory.
        $planetServiceFactory =  app()->make(PlanetServiceFactory::class);
        $this->planetService = $planetServiceFactory->makeForPlayer($this->playerService, 0);
    }

    /**
     * Set up the player service for testing.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function setUpPlayerService(): void
    {
        // Initialize empty playerService object for testing.
        // We do not use the factory as that would require a database connection.
        $this->playerService = app()->make(PlayerService::class, ['player_id' => 0]);
    }
}
