<?php

namespace Tests;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use OGame\Models\Resources;
use OGame\Services\PlayerService;

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

        // Create a new user and login so we can access ingame features.
        $this->createAndLoginUser();

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

        $this->currentUserId = (int)$playerId;
        $this->currentUsername = $playerName;
        $this->currentPlanetId = (int)$planetId;
    }

    /**
     * Add resources to current users current planet.
     *
     * @param Resources $resources
     * @return void
     * @throws BindingResolutionException
     */
    protected function planetAddResources(Resources $resources): void
    {
        $playerService = app()->make(PlayerService::class, ['player_id' => $this->currentUserId]);
        $planetService = $playerService->planets->current();
        // Update resources.
        $planetService->addResources($resources, true);
    }

    /**
     * Deduct resources from current users current planet.
     *
     * @param Resources $resources
     * @return void
     * @throws BindingResolutionException
     * @throws Exception
     */
    protected function planetDeductResources(Resources $resources): void
    {
        $playerService = app()->make(PlayerService::class, ['player_id' => $this->currentUserId]);
        $planetService = $playerService->planets->current();
        // Update resources.
        $planetService->deductResources($resources);
    }

    /**
     * Set object level on current users current planet.
     *
     * @param string $machine_name
     * @param int $object_level
     * @return void
     * @throws BindingResolutionException
     * @throws Exception
     */
    protected function planetSetObjectLevel(string $machine_name, int $object_level): void
    {
        // Update current users planet buildings to allow for research by mutating database.
        $playerService = app()->make(PlayerService::class, ['player_id' => $this->currentUserId]);
        $planetService = $playerService->planets->current();
        // Update the object level on the planet.
        $object = $planetService->objects->getObjectByMachineName($machine_name);
        $planetService->setObjectLevel($object->id, $object_level, true);
    }

    /**
     * Set object level on current users current planet.
     *
     * @param string $machine_name
     * @param int $object_level
     * @return void
     * @throws BindingResolutionException
     */
    protected function playerSetResearchLevel(string $machine_name, int $object_level): void
    {
        // Update current users planet buildings to allow for research by mutating database.
        $playerService = app()->make(PlayerService::class, ['player_id' => $this->currentUserId]);
        // Update the technology level for the player.
        $playerService->setResearchLevel($machine_name, $object_level, true);
    }

}
