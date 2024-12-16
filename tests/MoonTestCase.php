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
        $this->moonService = $planetServiceFactory->createMoonForPlanet($this->planetService);

        // Switch to the moon to ensure the correct planet is used by default in tests.
        $this->switchToMoon();

        // Reload the moon object to ensure all stats are loaded correctly.
        $this->moonService->reloadPlanet();
    }

    /**
     * Switch the active planet context to the second planet of the current user which affects
     * interactive requests done such as building queue items or canceling build queue items.
     *
     * @return void
     */
    protected function switchToMoon(): void
    {
        $response = $this->get('/overview?cp=' . $this->moonService->getPlanetId());
        $response->assertStatus(200);
    }
}
