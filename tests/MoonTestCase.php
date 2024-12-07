<?php

namespace Tests;

use Illuminate\Contracts\Container\BindingResolutionException;
use OGame\Factories\PlanetServiceFactory;
use OGame\Services\PlanetService;

/**
 * Base class for tests that expect the current user to have a moon.
 */
abstract class MoonTestCase extends AccountTestCase
{
    /**
     * Test user moon that is associated with the main planet.
     *
     * @var PlanetService
     */
    protected PlanetService $moonService;

    /**
     * Set up common test components.
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create moon for the current user.
        $planetServiceFactory =  resolve(PlanetServiceFactory::class);
        $this->moonService = $planetServiceFactory->createMoonForPlayer($this->planetService);

        // Switch to the moon to ensure the correct planet is used by default in tests.
        $this->switchToMoon();

        // Reload the moon object to ensure all stats are loaded correctly.
        $this->moonService->reloadPlanet();
    }
}
