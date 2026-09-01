<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use LogicException;
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

        // Start the session up-front so csrf_token() returns a real token.
        $this->app['session']->driver()->start();
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
     * @return void
     */
    protected function createAndLoginUser(): void
    {
        // Seed the planet allocator so the homeworld lands at a fixed, collision-safe
        // system far from both the seeded Legor admin account (1:1:2) and any planets
        // leaked by non-transactional tests (Admin/Ban/Buddy populate systems 1..N).
        // Without this, position-based colonisation tests collide with Legor's 1:1:2, and
        // the distance between the two home planets drifts (raising deuterium fuel past
        // the budget in FleetDispatchLargeResourcesTest).
        resolve(SettingsService::class)->set('last_assigned_system', 400);

        $user = $this->createUser();

        $this->actingAs($user);

        $this->currentUserId = $user->id;
        $this->currentUsername = $user->username;

        // Wire up planet services from the real factory.
        $playerServiceFactory = resolve(PlayerServiceFactory::class);
        $playerService = $playerServiceFactory->make($user->id, true);

        $this->planetService = $playerService->planets->current();
        $this->currentPlanetId = $this->planetService->getPlanetId();

        // Reproduce the page-load side effects real registration triggers.
        $playerService->update();
        $this->planetService->update();

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
     * @return User
     */
    protected function createUser(): User
    {
        // Create without model events to skip the `created` hook that promotes the first
        // user to admin. That hook issues a cross-row `update users`, which contends with
        // the scheduler/queue worker and deadlocks (1205 lock wait) in tests.
        $user = User::withoutEvents(fn (): User => User::factory()->create([
            'username' => 'test_' . Str::random(16),
        ]));

        resolve(InitialUserDataService::class)->createFor($user);

        return $user;
    }
}
