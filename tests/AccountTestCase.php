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
    protected string $currentUsername = '';
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
     * Retrieve meta fields from page response to extract player id and planet id.
     *
     * @return void
     */
    protected function retrieveMetaFields(): void
    {
        //  Extract current user planet ID based on meta tag in the overview page
        $response = $this->get('/overview');
        if ($response->status() !== 200) {
            // Log first 200 chars.
            $this->fail('Failed to retrieve overview page after registration. Response HTTP code: ' . $response->status() . '. Response first 2k chars: ' . substr($response->getContent(),0,2000));
        }
        $content = $response->getContent();

        preg_match('/<meta name="ogame-player-id" content="([^"]+)"/', $content, $playerIdMatches);
        preg_match('/<meta name="ogame-player-name" content="([^"]+)"/', $content, $playerNameMatches);
        preg_match('/<meta name="ogame-planet-id" content="([^"]+)"/', $content, $planetIdMatches);

        $playerId = $playerIdMatches[1] ?? null;
        $playerName = $playerNameMatches[1] ?? null;
        $planetId = $planetIdMatches[1] ?? null;

        // Now you can assert these values to ensure they are what you expect.
        $this->assertNotEmpty($playerId);
        $this->assertNotEmpty($playerName);
        $this->assertNotEmpty($planetId);

        $this->currentUserId = $playerId;
        $this->currentUsername = $playerName;
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
        $playerService = app()->make(\OGame\Services\PlayerService::class, ['player_id' => $this->currentUserId]);
        $planetService = $playerService->planets->current();
        // Update resources.
        $planetService->addResources($resources, true);
    }

    /**
     * Deduct resources from current users current planet.
     *
     * @param $resources
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function planetDeductResources($resources): void
    {
        $playerService = app()->make(\OGame\Services\PlayerService::class, ['player_id' => $this->currentUserId]);
        $planetService = $playerService->planets->current();
        // Update resources.
        $planetService->deductResources($resources, true);
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
