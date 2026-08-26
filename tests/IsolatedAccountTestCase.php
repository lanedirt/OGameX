<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use LogicException;
use OGame\Factories\PlanetServiceFactory;
use OGame\Factories\PlayerServiceFactory;
use OGame\Models\User;
use OGame\Services\InitialUserDataService;
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
     * Refreshing the application creates a connection outside this test's transaction,
     * making its fixtures unavailable. Remove the underlying cache dependency before
     * converting a test that needs this behavior.
     */
    final public function reloadApplication(): void
    {
        throw new LogicException('reloadApplication() is incompatible with IsolatedAccountTestCase.');
    }

    /**
     * Create a user and authenticate without HTTP round-trips.
     *
        * Authenticates via actingAs() and wires up the planet services from the real factories,
        * mirroring retrieveMetaFields() without the HTML parsing.
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
        * Creates a normal user with a collision-safe username, then delegates initial game data
        * setup to the production service used during registration.
     *
     * @return User
     */
    protected function createUser(): User
    {
        $user = User::factory()->create(['username' => 'test_' . Str::random(16)]);

        // User::factory() skips CreateNewUser, but the User model's `created` hook still
        // promotes each transaction's first user to admin. Revert that for a normal player.
        if ($user->hasRole('admin')) {
            $user->removeRole('admin');
            $user->username = 'test_' . Str::random(16);
            $user->save();
        }

        resolve(InitialUserDataService::class)->createFor($user);

        return $user;
    }
}
