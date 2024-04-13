<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Str;
use OGame\Factories\PlanetServiceFactory;
use OGame\Models\Planet;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    protected PlanetService $planetService;

    /**
     * Create a new user and login via the register form on login page.
     *
     * @return void
     */
    protected function createAndLoginUser(): void
    {
        // First go to logout page to ensure we are not logged in.
        $this->post('/logout');

        $response = $this->get('/login');

        // Check for existence of register form
        $response->assertSee('subscribeForm');

        // Simulate form data
        // Generate random email
        $randomEmail = Str::random(10) . '@example.com';

        $formData = [
            '_token' => csrf_token(),
            'email' => $randomEmail,
            'password' => 'asdasdasd',
            'v' => '3',
            'step' => 'validate',
            'kid' => '',
            'errorCodeOn' => '1',
            'is_utf8' => '1',
            'agb' => 'on',
        ];

        // Submit the registration form
        $response = $this->post('/register', $formData);
        if ($response->status() !== 302) {
            var_dump($response->getContent());
            $this->fail('Failed to register account. Response status: ' . $response->status(). '. Check the logs.');
        }

        // Check if we are authenticated after registration.
        $this->assertAuthenticated();
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
    }

    /**
     * Set up the planet service for testing.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function setUpPlanetService(): void
    {
        // Initialize empty playerService object directly without factory as we do not
        // actually want to load a player from the database.
        $playerService = app()->make(PlayerService::class, ['player_id' => 0]);
        // Initialize the planet service with factory.
        $planetServiceFactory =  app()->make(PlanetServiceFactory::class);
        $this->planetService = $planetServiceFactory->makeForPlayer($playerService, 0);
    }
}
