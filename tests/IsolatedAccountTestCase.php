<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use OGame\Factories\PlanetServiceFactory;
use OGame\Factories\PlayerServiceFactory;
use OGame\Models\User;
use OGame\Models\UserTech;
use OGame\Services\SettingsService;

/**
 * Isolated variant of AccountTestCase.
 *
 * Differences from AccountTestCase:
 *  - Wraps each test in a database transaction (DatabaseTransactions) so no rows leak
 *    between tests and tests no longer depend on execution order.
 *  - Creates the user via the standard Eloquent factories (User::factory()) + actingAs(),
 *    instead of a real HTTP /register + /login round-trip.
 *  - Starts the session up-front so csrf_token() returns a real token, avoiding a
 *    session-initializing GET request; CSRF stays fully enforced.
 *  - Resets the stateful singleton services (SettingsService + service factories) on
 *    teardown, because their in-memory caches are NOT rolled back by DatabaseTransactions.
 *
 * Test intent (assertions) is identical to AccountTestCase-based tests; only the setup
 * mechanism changes. See docs/test-isolation-migration-plan.md (Phase 1).
 */
abstract class IsolatedAccountTestCase extends AccountTestCase
{
    use DatabaseTransactions;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Start the session so csrf_token() returns a real token. Controllers verify the
        // token themselves (e.g. AbstractUnitsController::addBuildRequest compares
        // session()->token() to the posted _token), so this avoids needing a
        // session-initializing GET request while keeping CSRF fully enforced.
        $this->app['session']->driver()->start();
    }

    /**
     * Reset stateful singletons between tests. Their in-memory caches (settings values,
     * cached PlanetService/PlayerService instances) survive across tests because the app
     * container is not rebuilt, so we clear them explicitly here.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        $this->app->forgetInstance(SettingsService::class);
        $this->app->forgetInstance(PlayerServiceFactory::class);
        $this->app->forgetInstance(PlanetServiceFactory::class);

        parent::tearDown();
    }

    /**
     * Create a user and authenticate without HTTP round-trips.
     *
     * Reuses the production CreateNewUser action (creates User + UserTech + initial planets
     * + welcome message), then authenticates via actingAs() and wires up the planet services
     * from the real factories — mirroring retrieveMetaFields() without the HTML parsing.
     *
     * @return void
     */
    protected function createAndLoginUser(): void
    {
        $user = $this->createUser();

        $this->actingAs($user);

        $this->currentUserId = $user->id;
        $this->currentUsername = $user->username;

        // Wire up planet services from the real factory.
        $playerServiceFactory = resolve(PlayerServiceFactory::class);
        $playerService = $playerServiceFactory->make($user->id, true);

        $this->planetService = $playerService->planets->current();
        $this->currentPlanetId = $this->planetService->getPlanetId();

        $allPlanets = $playerService->planets->allPlanets();
        if (isset($allPlanets[1])) {
            $this->secondPlanetService = $allPlanets[1];
        }

        // Set default computer technology level for newly created users.
        $this->setDefaultComputerTechnology();
    }

    /**
     * Create a normal (non-admin) user using the standard Eloquent factories.
     *
     * Creates the user via User::factory() plus the game data a registered account has
     * (UserTech record + initial planets via the PlanetServiceFactory), without HTTP and
     * without the CreateNewUser action's first-user/admin side effects.
     *
     * @return User
     */
    protected function createUser(): User
    {
        // Create the user through the standard Eloquent factory.
        $user = User::factory()->create();

        // Create the initial tech record a registered account has.
        UserTech::factory()->create(['user_id' => $user->id]);

        // Create the initial planet(s) via the production planet factory.
        $playerServiceFactory = resolve(PlayerServiceFactory::class);
        $playerService = $playerServiceFactory->make($user->id);

        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $planetNames = ['Homeworld', 'Colony'];
        $registrationPlanetAmount = resolve(SettingsService::class)->registrationPlanetAmount();
        for ($i = 0; $i < $registrationPlanetAmount; $i++) {
            $planetServiceFactory->createInitialPlanetForPlayer($playerService, $planetNames[$i === 0 ? 0 : 1]);
        }

        return $user;
    }
}
