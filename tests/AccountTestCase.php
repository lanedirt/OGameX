<?php

namespace Tests;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Testing\TestResponse;
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
        $content = $response->getContent();
        if (empty($content)) {
            $content = '';
        }

        if ($response->status() !== 200) {
            // Return first 2k chars for debug purposes.
            $this->fail('Failed to retrieve overview page after registration. Response HTTP code: ' . $response->status() . '. Response first 2k chars: ' . substr($content, 0, 2000));
        }

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
        if (!isset($this->planetService)) {
            $this->planetService = app()->make(PlayerService::class, ['player_id' => $this->currentUserId])->planets->current();
        }
        // Update resources.
        $this->planetService->addResources($resources, true);
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
        if (!isset($this->planetService)) {
            $this->planetService = app()->make(PlayerService::class, ['player_id' => $this->currentUserId])->planets->current();
        }
        // Update resources.
        $this->planetService->deductResources($resources);
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
        if (!isset($this->planetService)) {
            $this->planetService = app()->make(PlayerService::class, ['player_id' => $this->currentUserId])->planets->current();
        }
        // Update the object level on the planet.
        $object = $this->planetService->objects->getObjectByMachineName($machine_name);
        $this->planetService->setObjectLevel($object->id, $object_level, true);
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

    /**
     * Assert that the object level is as expected on the page.
     *
     * @param TestResponse $response
     * @param string $machine_name
     * @param int $expected_level
     * @param string $error_message
     * @return void
     */
    protected function assertObjectLevelOnPage(TestResponse $response, string $machine_name, int $expected_level, string $error_message = ''): void
    {
        // Get object name from machine name.
        try {
            if (!isset($this->planetService)) {
                $this->planetService = app()->make(PlayerService::class, ['player_id' => $this->currentUserId])->planets->current();
            }
            $object = $this->planetService->objects->getObjectByMachineName($machine_name);
        } catch (Exception $e) {
            $this->fail('Failed to get object by machine name: ' . $machine_name . '. Error: ' . $e->getMessage());
        }
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*' . $object->title . '\s*<\/span>\s*(\d+)\s*<\/span>/';

        $content = $response->getContent();
        if (empty($content)) {
            $content = '';
        }
        if (preg_match($pattern, $content, $matches)) {
            $actual_level = $matches[1];  // The captured digits
            if (!empty($error_message)) {
                $this->assertEquals($expected_level, $actual_level, $error_message);
            } else {
                $this->assertEquals($expected_level, $actual_level, $object->title . ' is at level (' . $actual_level . ') while it is expected to be at level (' . $expected_level . ').');
            }
        } else {
            $this->fail('No matching level found on page for object ' . $object->title);
        }
    }

    /**
     * Assert that the resources are as expected on the page.
     *
     * @param TestResponse $response
     * @param Resources $resources
     * @return void
     */
    protected function assertResourcesOnPage(TestResponse $response, Resources $resources): void{
        $content = $response->getContent();
        if (empty($content)) {
            $content = '';
        }

        if ($resources->metal->get() > 0) {
            $pattern = '/<span id="resources_metal" class="[^"]*">\s*' . $resources->metal->getFormattedLong() . '\s*<\/span>/';
            $result = preg_match($pattern, $content);
            $this->assertTrue($result === 1, 'Resource metal is not at ' . $resources->metal->getFormattedLong() . '.');
        }
        if ($resources->crystal->get() > 0) {
            $pattern = '/<span\s+id="resources_crystal" class="[^"]*">\s*' . $resources->crystal->getFormattedLong() . '\s*<\/span>/';
            $result = preg_match($pattern, $content);
            $this->assertTrue($result === 1, 'Resource crystal is not at ' . $resources->crystal->getFormattedLong() . '.');
        }

        if ($resources->deuterium->get() > 0) {
            $pattern = '/<span\s+id="resources_deuterium" class="[^"]*">\s*' . $resources->deuterium->getFormattedLong() . '\s*<\/span>/';
            $result = preg_match($pattern, $content);
            $this->assertTrue($result === 1, 'Resource deuterium is not at ' . $resources->deuterium->getFormattedLong() . '.');
        }

        if ($resources->energy->get() > 0) {
            $pattern = '/<span\s+id="resources_energy" class="[^"]*">\s*' . $resources->energy->getFormattedLong() . '\s*<\/span>/';
            $result = preg_match($pattern, $content);
            $this->assertTrue($result === 1, 'Resource energy is not at ' . $resources->energy->getFormattedLong() . '.');
        }
    }
}
