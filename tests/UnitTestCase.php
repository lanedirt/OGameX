<?php

namespace Tests;

use Exception;
use OGame\Factories\PlanetServiceFactory;
use OGame\Models\Planet;
use OGame\Models\UserTech;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use OGame\Services\SettingsService;

abstract class UnitTestCase extends TestCase
{
    protected PlayerService $playerService;
    protected PlanetService $planetService;
    protected SettingsService $settingsService;

    /**
     * Set up common test components.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpPlayerService();
        $this->setUpPlanetService();
        $this->setUpSettingsService();
    }

    /**
     * Helper method to create a planet model and configure it.
     *
     * @param array<string, int> $attributes
     * @throws Exception
     */
    protected function createAndSetPlanetModel(array $attributes): void
    {
        // Create fake planet eloquent model with additional attributes
        try {
            // Fill in mandatory attributes with default values ir
            // they are not provided.
            $attributes['galaxy'] = $attributes['galaxy'] ?? 1;
            $attributes['system'] = $attributes['system'] ?? 1;
            $attributes['planet'] = $attributes['planet'] ?? 1;

            $planetModelFake = Planet::factory()->make($attributes);
        } catch (Exception $e) {
            $this->fail('Failed to create fake planet model: ' . $e->getMessage());
        }

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
     */
    protected function setUpPlanetService(): void
    {
        // Initialize the planet service with factory.
        $planetServiceFactory =  resolve(PlanetServiceFactory::class);
        $this->planetService = $planetServiceFactory->makeForPlayer($this->playerService, 0);
    }

    /**
     * Set up the player service for testing.
     *
     * @return void
     */
    protected function setUpPlayerService(): void
    {
        // Initialize empty playerService object for testing.
        // We do not use the factory as that would require a database connection.
        $this->playerService = resolve(PlayerService::class, ['player_id' => 0]);
    }

    /**
     * Set up the settings service for testing.
     *
     * @return void
     */
    protected function setUpSettingsService(): void
    {
        // Initialize the planet service with factory.
        $settingsService =  resolve(SettingsService::class);
        $this->settingsService = $settingsService;
    }
}
