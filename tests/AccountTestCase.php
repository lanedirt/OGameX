<?php

namespace Tests;

use Illuminate\Support\Str;

/**
 * Base class for tests that require account context. Common setup includes signup of new account and login.
 */
abstract class AccountTestCase extends TestCase
{
    /**
     * @var int
     */
    protected int $currentUserId = 0;
    /**
     * @var int
     */
    protected int $currentPlanetId = 0;

    /**
     * Set up common test components.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create a new user and login so we can access ingame features
        $this->createAndLoginUser();
    }

    /**
     * Create a new user and login via the register form on login page.
     *
     * @return void
     */
    protected function createAndLoginUser(): void
    {
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
        $this->post('/register', $formData);

        // We should now automatically be logged in. Retrieve meta fields to verify.
        $this->retrieveMetaFields();
    }

    /**
     * Retrieve meta fields from page response to extract player id and planet id.
     *
     * @return void
     */
    protected function retrieveMetaFields(): void
    {
        //  Extract current user planet ID based on meta tag in the overview page
        $response = $this->get('/overview');

        $content = $response->getContent();

        preg_match('/<meta name="ogame-planet-id" content="([^"]+)"/', $content, $planetIdMatches);
        preg_match('/<meta name="ogame-player-id" content="([^"]+)"/', $content, $playerIdMatches);

        $playerId = $playerIdMatches[1] ?? null;
        $planetId = $planetIdMatches[1] ?? null;

        // Now you can assert these values to ensure they are what you expect.
        $this->assertNotEmpty($playerId);
        $this->assertNotEmpty($planetId);

        $this->currentUserId = $playerId;
        $this->currentPlanetId = $planetId;
    }

    /**
     * Add resources to current users current planet.
     *
     * @param $resources
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function planetAddResources($resources): void
    {
        // Update current users planet buildings to allow for research by mutating database.
        $playerService = app()->make(\OGame\Services\PlayerService::class, ['player_id' => $this->currentUserId]);
        $planetService = $playerService->planets->current();
        // Update resources to allow for research by mutating database.
        $planetService->addResources($resources, true);
    }

    /**
     * Set object level on current users current planet.
     *
     * @param $object_id
     * @param $object_level
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function planetSetObjectLevel($object_id, $object_level): void
    {
        // Update current users planet buildings to allow for research by mutating database.
        $playerService = app()->make(\OGame\Services\PlayerService::class, ['player_id' => $this->currentUserId]);
        $planetService = $playerService->planets->current();
        // Update the object level on the planet.
        $planetService->setObjectLevel($object_id, $object_level, true);
    }

    /**
     * Set object level on current users current planet.
     *
     * @param $object_id
     * @param $object_level
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function playerSetResearchLevel($object_id, $object_level): void
    {
        // Update current users planet buildings to allow for research by mutating database.
        $playerService = app()->make(\OGame\Services\PlayerService::class, ['player_id' => $this->currentUserId]);
        // Update the technology level for the player.
        $playerService->setResearchLevel($object_id, $object_level, true);
    }

}
