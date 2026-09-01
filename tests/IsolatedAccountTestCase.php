<?php

namespace Tests;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use LogicException;
use OGame\Factories\PlayerServiceFactory;
use OGame\Models\User;
use OGame\Services\InitialUserDataService;
use OGame\Services\PlanetService;
use OGame\Services\SettingsService;
use Tests\Traits\AssertsMessages;
use Tests\Traits\AssertsPageState;
use Tests\Traits\CreatesForeignFixtures;
use Tests\Traits\ManagesPlanetState;
use Tests\Traits\SubmitsBuildRequests;

/**
 * Base class for tests that require an authenticated account, isolated per test.
 *
 * Wraps each test in a database transaction (DatabaseTransactions) so no rows leak between
 * tests and tests no longer depend on execution order. Creates the user via the standard
 * Eloquent factories (User::factory()) + actingAs() instead of a real HTTP /register +
 * /login round-trip, and starts the session up-front so csrf_token() returns a real token
 * (CSRF stays fully enforced).
 *
 * Shared test helpers are grouped into focused traits in Tests\Traits (planet state,
 * page assertions, build requests, messages and foreign fixtures).
 */
abstract class IsolatedAccountTestCase extends TestCase
{
    use DatabaseTransactions;
    use ManagesPlanetState;
    use AssertsPageState;
    use SubmitsBuildRequests;
    use AssertsMessages;
    use CreatesForeignFixtures;

    protected int $currentUserId = 0;
    protected string $currentUsername = '';
    protected int $userPlanetAmount = 2;

    protected int $currentPlanetId = 0;

    /**
     * Default computer technology level for newly created users.
     * Tests that require a different level can override this property.
     */
    protected int $defaultComputerTechnologyLevel = 5;

    /**
     * Test user main planet.
     */
    protected PlanetService $planetService;

    /**
     * Test user second planet.
     */
    protected ?PlanetService $secondPlanetService = null;

    /**
     * The default test time that is used to start tests with.
     */
    protected Carbon $defaultTestTime;

    /**
     * Set up common test components.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Set default test time to 2024-01-01 00:00:00 to ensure all tests have the same starting point.
        $this->travelTo(Date::create(2024, 1, 1, 0, 0, 0));

        // Set default server settings for all tests.
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('economy_speed', 8);
        // Establish a full speed baseline so settings mutated by earlier tests can't leak
        // into this one. Without this, e.g. ResearchQueueTest sets research_speed=2 which
        // then changes timing in unrelated tests like VacationModeTest (see #1021).
        $settingsService->set('research_speed', 1);

        // Set amount of planets to be created for the user because planet switching
        // is a part of the test suite.
        $settingsService->set('registration_planet_amount', $this->userPlanetAmount);

        // Reset planet assignment to start within valid galaxy bounds.
        // This ensures tests don't fail when the database has planets in galaxies
        // beyond the configured max (e.g., from previous test runs with different settings).
        $maxGalaxies = $settingsService->numberOfGalaxies();
        $lastAssignedGalaxy = (int)$settingsService->get('last_assigned_galaxy', 1);
        if ($lastAssignedGalaxy > $maxGalaxies) {
            $settingsService->set('last_assigned_galaxy', 1);
            $settingsService->set('last_assigned_system', 1);
        }

        // Create a new user and login so we can access ingame features.
        $this->createAndLoginUser();

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

    /**
     * Set default computer technology level for newly created users.
     * Tests that require a different level can override $defaultComputerTechnologyLevel.
     */
    protected function setDefaultComputerTechnology(): void
    {
        if ($this->defaultComputerTechnologyLevel === 0) {
            // Skip setting if level is 0 (default game behavior).
            return;
        }

        $this->playerSetResearchLevel('computer_technology', $this->defaultComputerTechnologyLevel);
    }
}
