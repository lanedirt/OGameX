<?php

namespace Tests;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use OGame\Factories\PlanetServiceFactory;
use OGame\Models\Message;
use OGame\Models\Planet;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\Models\User;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use OGame\ViewModels\MessageViewModel;

/**
 * Base class for tests that require account context. Common setup includes signup of new account and login.
 */
abstract class AccountTestCase extends TestCase
{
    protected int $currentUserId = 0;
    protected string $currentUsername = '';
    protected int $currentPlanetId = 0;
    protected PlanetService $secondPlanetService;

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

    protected PlanetService $planetService;

    /**
     * By default, Laravel does not refresh the application state between requests in a single test.
     * By invoking this method we ensure that the application state is refreshed to avoid any side effects from
     * previous requests such as planets not being updated on the initial request.
     *
     * @return void
     */
    public function reloadApplication(): void
    {
        $this->refreshApplication();
        $this->be(User::find($this->currentUserId));
    }

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
            'password' => 'password',
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
     * @throws BindingResolutionException
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

    /**
     * Retrieve meta fields from page response to extract player id and planet id.
     *
     * @return void
     * @throws BindingResolutionException
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

        $playerService = app()->make(PlayerService::class, ['player_id' => $this->currentUserId]);
        $this->planetService = $playerService->planets->current();
        $this->secondPlanetService = $playerService->planets->all()[1];
    }

    /**
     * Get a random second user id from the database. This is useful for testing interactions between two players.
     *
     * @return int
     */
    protected function getSecondPlayerId(): int
    {
        $playerIds = \DB::table('users')->whereNot('id', $this->currentUserId)->inRandomOrder()->limit(1)->pluck('id');
        if (count($playerIds) < 1) {
            // Create user if there are not enough in the database.
            $this->createAndLoginUser();
            $playerIds = \DB::table('users')->whereNot('id', $this->currentUserId)->inRandomOrder()->limit(1)->pluck('id');
        }

        return $playerIds[0];
    }

    /**
     * Gets a nearby foreign planet for the current user. This is useful for testing interactions between two players.
     *
     * @return PlanetService
     * @throws BindingResolutionException
     */
    protected function getNearbyForeignPlanet(): PlanetService
    {
        // Find a planet of another player that is close to the current player by checking the same galaxy
        // and up to 10 systems away.
        $planet_id = \DB::table('planets')
            ->where('user_id', '!=', $this->currentUserId)
            ->where('galaxy', $this->planetService->getPlanetCoordinates()->galaxy)
            ->whereBetween('system', [$this->planetService->getPlanetCoordinates()->system - 10, $this->planetService->getPlanetCoordinates()->system + 10])
            ->inRandomOrder()
            ->limit(1)
            ->pluck('id');

        if ($planet_id == null) {
            // No planets found, attempt to create a new user to see if this fixes it.
            $this->createAndLoginUser();
            $planet_id = \DB::table('planets')
                ->where('user_id', '!=', $this->currentUserId)
                ->where('galaxy', $this->planetService->getPlanetCoordinates()->galaxy)
                ->whereBetween('system', [$this->planetService->getPlanetCoordinates()->system - 10, $this->planetService->getPlanetCoordinates()->system + 10])
                ->inRandomOrder()
                ->limit(1)
                ->pluck('id');
        }

        if ($planet_id == null) {
            $this->fail('Failed to find a nearby foreign planet for testing.');
        } else {
            // Create and return a new PlanetService instance for the found planet.
            $planetServiceFactory =  app()->make(PlanetServiceFactory::class);
            return $planetServiceFactory->make($planet_id[0]);
        }
    }

    /**
     * Gets a nearby empty coordinate for the current user. This is useful for testing interactions towards empty planets.
     *
     * @param int $min_position
     * @param int $max_position
     * @return Coordinate
     */
    protected function getNearbyEmptyCoordinate(int $min_position = 4, int $max_position = 12): Coordinate
    {
        // Find a position that has no planet in the same galaxy and up to 10 systems away between position 4-13.
        $coordinate = new Coordinate($this->planetService->getPlanetCoordinates()->galaxy, 0, 0);
        $tryCount = 0;
        while ($tryCount < 100) {
            $tryCount++;
            $coordinate->system = $this->planetService->getPlanetCoordinates()->system + rand(-10, 10);
            $coordinate->position = rand($min_position, $max_position);
            $planetCount = \DB::table('planets')
                ->where('galaxy', $coordinate->galaxy)
                ->where('system', $coordinate->system)
                ->where('planet', $coordinate->position)
                ->count();
            if ($planetCount == 0) {
                return $coordinate;
            }
        }

        $this->fail('Failed to find an empty coordinate for testing.');
    }

    /**
     * Add resources to current users current planet.
     *
     * @param Resources $resources
     * @return void
     */
    protected function planetAddResources(Resources $resources): void
    {
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
        // Update the object level on the planet.
        $object = $this->planetService->objects->getObjectByMachineName($machine_name);
        $this->planetService->setObjectLevel($object->id, $object_level, true);
    }

    /**
     * Add units to current users current planet.
     *
     * @param string $machine_name
     * @param int $amount
     * @return void
     * @throws BindingResolutionException
     * @throws Exception
     */
    protected function planetAddUnit(string $machine_name, int $amount): void
    {
        // Update the object level on the planet.
        $this->planetService->addUnit($machine_name, $amount);
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
        // Assert response is successful
        $response->assertStatus(200);

        // Get object name from machine name.
        try {
            $object = $this->planetService->objects->getObjectByMachineName($machine_name);
        } catch (Exception $e) {
            $this->fail('Failed to get object by machine name: ' . $machine_name . '. Error: ' . $e->getMessage());
        }

        // Update pattern to extract level from data-value attribute
        $pattern = '/<li[^>]*\bclass="[^"]*\b' . preg_quote($object->class_name, '/') . '\b[^"]*"[^>]*>.*?<span[^>]+class="(?:level|amount)"[^>]*data-value="(\d+)"[^>]*>/s';

        $content = $response->getContent();
        if (empty($content)) {
            $content = '';
        }
        if (preg_match($pattern, $content, $matches)) {
            $actual_level = $matches[1];  // The captured digits from data-value
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
    protected function assertResourcesOnPage(TestResponse $response, Resources $resources): void
    {
        $content = $response->getContent();
        if (empty($content)) {
            $content = '';
        }

        if ($resources->metal->get() > 0) {
            $pattern = '/<span\s+id="resources_metal"\s+class="[^"]*"\s+data-raw="[^"]*">\s*' . preg_quote($resources->metal->getFormattedLong(), '/') . '\s*<\/span>/';
            $result = preg_match($pattern, $content);
            $this->assertTrue($result === 1, 'Resource metal is not at ' . $resources->metal->getFormattedLong() . '.');
        }

        if ($resources->crystal->get() > 0) {
            $pattern = '/<span\s+id="resources_crystal"\s+class="[^"]*"\s+data-raw="[^"]*">\s*' . preg_quote($resources->crystal->getFormattedLong(), '/') . '\s*<\/span>/';
            $result = preg_match($pattern, $content);
            $this->assertTrue($result === 1, 'Resource crystal is not at ' . $resources->crystal->getFormattedLong() . '.');
        }

        if ($resources->deuterium->get() > 0) {
            $pattern = '/<span\s+id="resources_deuterium"\s+class="[^"]*"\s+data-raw="[^"]*">\s*' . preg_quote($resources->deuterium->getFormattedLong(), '/') . '\s*<\/span>/';
            $result = preg_match($pattern, $content);
            $this->assertTrue($result === 1, 'Resource deuterium is not at ' . $resources->deuterium->getFormattedLong() . '.');
        }

        if ($resources->energy->get() > 0) {
            $pattern = '/<span\s+id="resources_energy"\s+class="[^"]*"\s+data-raw="[^"]*">\s*' . preg_quote($resources->energy->getFormattedLong(), '/') . '\s*<\/span>/';
            $result = preg_match($pattern, $content);
            $this->assertTrue($result === 1, 'Resource energy is not at ' . $resources->energy->getFormattedLong() . '.');
        }
    }

    protected function assertObjectInQueue(TestResponse $response, string $machine_name, string $error_message = ''): void
    {
        // Get object name from machine name.
        try {
            $object = $this->planetService->objects->getObjectByMachineName($machine_name);
        } catch (Exception $e) {
            $this->fail('Failed to get object by machine name: ' . $machine_name . '. Error: ' . $e->getMessage());
        }

        // Check if cancel text is present on page.
        try {
            $response->assertSee('Cancel production of ' . $object->title);
        } catch (Exception $e) {
            if (!empty($error_message)) {
                $this->fail($error_message . '. Error: ' . $e->getMessage());
            } else {
                $this->fail('Object ' . $object->title . ' is not in the queue. Error: ' . $e->getMessage());
            }
        }
    }

    protected function assertObjectNotInQueue(TestResponse $response, string $machine_name, string $error_message = ''): void
    {
        // Get object name from machine name.
        try {
            $object = $this->planetService->objects->getObjectByMachineName($machine_name);
        } catch (Exception $e) {
            $this->fail('Failed to get object by machine name: ' . $machine_name . '. Error: ' . $e->getMessage());
        }

        // Check if cancel text is present on page.
        try {
            $response->assertDontSee('Cancel production of ' . $object->title);
        } catch (Exception $e) {
            if (!empty($error_message)) {
                $this->fail($error_message . '. Error: ' . $e->getMessage());
            } else {
                $this->fail('Object ' . $object->title . ' is not in the queue. Error: ' . $e->getMessage());
            }
        }
    }

    /**
     * Add a resource build request to the current users current planet.
     * @param string $machine_name
     * @param bool $ignoreErrors
     * @return void
     * @throws Exception
     */
    protected function addResourceBuildRequest(string $machine_name, bool $ignoreErrors = false): void
    {
        $object = $this->planetService->objects->getObjectByMachineName($machine_name);

        $response = $this->post('/resources/add-buildrequest', [
            '_token' => csrf_token(),
            'technologyId' => $object->id,
        ]);

        if ($ignoreErrors) {
            return;
        }

        // Assert the response status is successful
        $response->assertStatus(200);
    }

    /**
     * Cancel a resource build request on the current users current planet.
     *
     * @param int $objectId
     * @param int $buildQueueId
     * @return void
     */
    protected function cancelResourceBuildRequest(int $objectId, int $buildQueueId): void
    {
        $response = $this->post('/resources/cancel-buildrequest', [
            '_token' => csrf_token(),
            'technologyId' => $objectId,
            'listId' => $buildQueueId,
        ]);

        // Assert the response status is successful
        $response->assertStatus(200);
    }

    /**
     * Add a facilities build request to the current users current planet.
     * @param string $machine_name
     * @return void
     * @throws Exception
     */
    protected function addFacilitiesBuildRequest(string $machine_name): void
    {
        $object = $this->planetService->objects->getObjectByMachineName($machine_name);

        $response = $this->post('/facilities/add-buildrequest', [
            '_token' => csrf_token(),
            'technologyId' => $object->id,
        ]);
        // Assert the response status is successful
        $response->assertStatus(200);
    }

    /**
     * Cancel a facilities build request on the current users current planet.
     *
     * @param int $objectId
     * @param int $buildQueueId
     * @return void
     */
    protected function cancelFacilitiesBuildRequest(int $objectId, int $buildQueueId): void
    {
        $response = $this->post('/facilities/cancel-buildrequest', [
            '_token' => csrf_token(),
            'technologyId' => $objectId,
            'listId' => $buildQueueId,
        ]);

        // Assert the response status is successful
        $response->assertStatus(200);
    }

    /**
     * Add a research build request to the current users current planet.
     * @param string $machine_name
     * @return void
     * @throws Exception
     */
    protected function addResearchBuildRequest(string $machine_name): void
    {
        $object = $this->planetService->objects->getObjectByMachineName($machine_name);

        $response = $this->post('/research/add-buildrequest', [
            '_token' => csrf_token(),
            'technologyId' => $object->id,
        ]);
        // Assert the response status is successful
        $response->assertStatus(200);
    }

    /**
     * Cancel a research build request on the current users current planet.
     *
     * @param int $objectId
     * @param int $buildQueueId
     * @return void
     */
    protected function cancelResearchBuildRequest(int $objectId, int $buildQueueId): void
    {
        $response = $this->post('/research/cancel-buildrequest', [
            '_token' => csrf_token(),
            'technologyId' => $objectId,
            'listId' => $buildQueueId,
        ]);

        // Assert the response status is successful
        $response->assertStatus(200);
    }

    /**
     * Add a shipyard build request to the current users current planet.
     * @param string $machine_name
     * @param int $amount
     * @return void
     * @throws Exception
     */
    protected function addShipyardBuildRequest(string $machine_name, int $amount): void
    {
        $object = $this->planetService->objects->getObjectByMachineName($machine_name);

        $response = $this->post('/shipyard/add-buildrequest', [
            '_token' => csrf_token(),
            'technologyId' => $object->id,
            'amount' => $amount,
        ]);

        // Assert the response status is successful
        $response->assertStatus(200);
    }

    /**
     * Add a defense build request to the current users current planet.
     * @param string $machine_name
     * @param int $amount
     * @return void
     * @throws Exception
     */
    protected function addDefenseBuildRequest(string $machine_name, int $amount): void
    {
        $object = $this->planetService->objects->getObjectByMachineName($machine_name);

        $response = $this->post('/defense/add-buildrequest', [
            '_token' => csrf_token(),
            'technologyId' => $object->id,
            'amount' => $amount,
        ]);

        // Assert the response status is successful
        $response->assertStatus(200);
    }

    /**
     * View the messages page for the current user in order to mark all default system
     * messages as read.
     *
     * @return void
     */
    protected function playerSetAllMessagesRead(): void
    {
        // Access the main messages page where default register message is sent to
        // in order to mark all messages as read.
        $response = $this->get('/ajax/messages?tab=universe');
        // Assert the response status is successful
        $response->assertStatus(200);
    }

    /**
     * Asserts that a message has been received in the frontend on the specified tab/subtab
     * and that it contains the specified text.
     *
     * @param string $tab
     * @param string $subtab
     * @param array<int,string> $must_contain
     * @return void
     */
    protected function assertMessageReceivedAndContains(string $tab, string $subtab, array $must_contain): void
    {
        // Assert that message has been sent to player.
        $response = $this->get('/overview');
        $response->assertStatus(200);
        // Assert that page contains "1 unread message(s)" text.
        $response->assertSee('1 unread message(s)');
        $response = $this->get('/ajax/messages?tab=' . $tab . '&subtab=' . $subtab);
        $response->assertStatus(200);
        foreach ($must_contain as $needle) {
            $response->assertSee($needle, false);
        }
    }

    /**
     * Asserts that a message has been received in the database for a specific player and that it contains the specified text.
     *
     * @param PlayerService $player
     * @param array<int,string> $must_contain
     * @return void
     */
    protected function assertMessageReceivedAndContainsDatabase(PlayerService $player, array $must_contain): void
    {
        $lastMessage = Message::where('user_id', $player->getId())
            ->orderBy('id', 'desc')
            ->first();

        // Get the message body.
        $lastMessageViewModel = new MessageViewModel($lastMessage);

        foreach ($must_contain as $needle) {
            $this->assertStringContainsString($needle, $lastMessageViewModel->getBody());
        }
    }
}
