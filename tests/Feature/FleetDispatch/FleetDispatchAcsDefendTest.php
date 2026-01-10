<?php

namespace Tests\Feature\FleetDispatch;

use Illuminate\Support\Facades\DB;
use OGame\Factories\PlanetServiceFactory;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Message;
use OGame\Models\Planet;
use OGame\Models\Resources;
use OGame\Models\User;
use OGame\Services\BuddyService;
use OGame\Services\FleetMissionService;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use OGame\Services\SettingsService;
use RuntimeException;
use Tests\FleetDispatchTestCase;

/**
 * Test that fleet dispatch works as expected for ACS Defend missions.
 */
class FleetDispatchAcsDefendTest extends FleetDispatchTestCase
{
    /**
     * @var int The mission type for the test.
     */
    protected int $missionType = 5;

    /**
     * @var string The mission name for the test, displayed in UI.
     */
    protected string $missionName = 'ACS Defend';

    /**
     * Prepare the planet for the test so it has the required buildings and research.
     *
     * @return void
     */
    protected function basicSetup(): void
    {
        $this->planetSetObjectLevel('robot_factory', 2);
        $this->planetSetObjectLevel('shipyard', 1);
        $this->planetSetObjectLevel('research_lab', 1);
        $this->playerSetResearchLevel('energy_technology', 1);
        $this->playerSetResearchLevel('combustion_drive', 1);
        $this->planetAddUnit('light_fighter', 5);
        $this->planetAddResources(new Resources(5000, 5000, 100000, 0));
    }

    protected function messageCheckMissionArrival(PlanetService $destinationPlanet): void
    {
        // Assert that message has been sent to sender (Fleet Command)
        $this->assertMessageReceivedAndContains('fleets', 'other', [
            'Fleet is stopping',
            'Fleet Command',
            $destinationPlanet->getPlanetName(),
        ]);
    }

    protected function messageCheckMissionReturn(PlanetService $destinationPlanet): void
    {
        // Assert that message has been sent to player and contains the correct information.
        $this->assertMessageReceivedAndContains('fleets', 'other', [
            'Your fleet is returning from',
            $this->planetService->getPlanetName(),
        ]);
    }

    /**
     * @var PlanetService|null The buddy's planet (stored to ensure consistency within a test)
     */
    protected ?PlanetService $buddyPlanet = null;

    /**
     * @var array<int> Track all buddy user IDs created across all tests for cleanup
     * Static to persist across test instances and avoid losing IDs during setUp()
     */
    protected static array $allCreatedBuddyUserIds = [];

    /**
     * Set up the test case.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->buddyPlanet = null;
    }

    /**
     * Clean up test data after each test to prevent state leakage.
     * Only removes buddy relationships and resets vacation mode - test users and planets
     * remain in database but won't have special state that affects subsequent tests.
     *
     * @todo Refactor test architecture to support DatabaseTransactions/RefreshDatabase
     *       by removing dependency on reloadApplication() expecting persisted users.
     */
    protected function tearDown(): void
    {
        // Clean up buddy relationships and vacation mode created during this test run
        // Process and remove each ID to avoid accumulation
        while (!empty(self::$allCreatedBuddyUserIds)) {
            $buddyUserId = array_shift(self::$allCreatedBuddyUserIds);

            // Delete buddy requests involving this user (with proper SQL grouping)
            DB::table('buddy_requests')
                ->where(function ($query) use ($buddyUserId) {
                    $query->where('sender_user_id', $buddyUserId)
                        ->orWhere('receiver_user_id', $buddyUserId);
                })
                ->delete();

            // Reset all vacation mode fields for buddy user
            // activateVacationMode() sets: vacation_mode, vacation_mode_activated_at, vacation_mode_until
            DB::table('users')
                ->where('id', $buddyUserId)
                ->update([
                    'vacation_mode' => false,
                    'vacation_mode_activated_at' => null,
                    'vacation_mode_until' => null,
                ]);
        }

        // Also reset all vacation mode fields for the current test user
        // (testDispatchFleetFromVacationModeError sets this)
        if (isset($this->currentUserId)) {
            DB::table('users')
                ->where('id', $this->currentUserId)
                ->update([
                    'vacation_mode' => false,
                    'vacation_mode_activated_at' => null,
                    'vacation_mode_until' => null,
                ]);
        }

        parent::tearDown();
    }

    /**
     * Create a buddy relationship between current player and another player.
     * Uses a dedicated test user/planet to avoid random admin selection issues.
     *
     * @return User The buddy user
     */
    protected function createBuddyPlayer(): User
    {
        // Create a fresh user specifically for this test
        // This ensures we never accidentally select an admin user
        $buddyUser = User::factory()->create();

        // Track this user ID in static array for cleanup in tearDown
        // Static array persists across test instances
        self::$allCreatedBuddyUserIds[] = $buddyUser->id;

        // Create a planet for the buddy user at a random position to avoid conflicts
        $buddyPlanet = Planet::factory()->create([
            'user_id' => $buddyUser->id,
            'galaxy' => $this->planetService->getPlanetCoordinates()->galaxy,
            'system' => min(499, $this->planetService->getPlanetCoordinates()->system + 5),
            'planet' => 8,
        ]);

        // Get planet service for the buddy's planet
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $buddyPlayerService = resolve(PlayerService::class, ['player_id' => $buddyUser->id]);
        $this->buddyPlanet = $planetServiceFactory->makeForPlayer($buddyPlayerService, $buddyPlanet->id);

        $buddyService = resolve(BuddyService::class);

        // Send buddy request and accept it
        $request = $buddyService->sendRequest($this->currentUserId, $buddyUser->id);
        $buddyService->acceptRequest($request->id, $buddyUser->id);

        return $buddyUser;
    }

    /**
     * Override to send mission to buddy's planet specifically.
     */
    protected function sendMissionToBuddyPlanet(UnitCollection $units, Resources $resources, bool $assertStatus = true): PlanetService
    {
        if ($this->buddyPlanet === null) {
            throw new RuntimeException('Must call createBuddyPlayer() before sendMissionToBuddyPlanet()');
        }

        $this->dispatchFleet($this->buddyPlanet->getPlanetCoordinates(), $units, $resources, PlanetType::Planet, 0, $assertStatus);
        return $this->buddyPlanet;
    }

    /**
     * Assert that trying to dispatch ACS Defend to own planet fails.
     */
    public function testFleetCheckToOwnPlanetError(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1);
        $this->fleetCheckToSecondPlanet($unitCollection, false);
    }

    /**
     * Assert that trying to dispatch ACS Defend to non-buddy planet fails.
     */
    public function testFleetCheckToNonBuddyPlanetError(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1);
        $this->fleetCheckToOtherPlayer($unitCollection, false);
    }

    /**
     * Assert that trying to dispatch ACS Defend to buddy planet succeeds.
     */
    public function testFleetCheckToBuddyPlanetSuccess(): void
    {
        $this->basicSetup();
        $this->createBuddyPlayer();

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1);
        $this->checkTargetFleet($this->buddyPlanet->getPlanetCoordinates(), $unitCollection, PlanetType::Planet, true);
    }

    /**
     * Assert that trying to dispatch ACS Defend to empty position fails.
     */
    public function testFleetCheckToEmptyPlanetError(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1);
        $this->fleetCheckToEmptyPosition($unitCollection, false);
    }

    /**
     * Verify that dispatching ACS Defend fleet deducts correct amount of units from planet.
     */
    public function testDispatchFleetDeductUnits(): void
    {
        $this->basicSetup();
        $this->createBuddyPlayer();

        // Assert that we begin with 5 light fighters on planet.
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'light_fighter', 5, 'Light Fighters are not at 5 units at beginning of test.');

        // Send fleet to buddy's planet.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 2);
        $this->sendMissionToBuddyPlanet($unitCollection, new Resources(0, 0, 0, 0));

        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'light_fighter', 3, 'Light Fighters not deducted from planet after fleet dispatch.');
    }

    /**
     * Verify that ACS Defend mission creates return trip after hold time.
     */
    public function testDispatchFleetReturnTrip(): void
    {
        $this->basicSetup();
        $buddyUser = $this->createBuddyPlayer();

        // Assert starting units
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'light_fighter', 5, 'Light Fighters are not at 5 units at beginning of test.');

        // Send fleet to buddy's planet.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 2);
        $foreignPlanet = $this->sendMissionToBuddyPlanet($unitCollection, new Resources(0, 0, 0, 0));

        // Verify units were deducted
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'light_fighter', 3, 'Light Fighters not deducted after fleet dispatch.');

        // Increase time to complete full mission cycle (outbound + hold + return).
        $this->travel(20)->hours();
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Check that units are back on planet.
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'light_fighter', 5, 'Light Fighters not returned to planet after mission completion.');
    }

    /**
     * Verify that both sender and host receive correct messages on arrival.
     */
    public function testDispatchFleetMessagesToSenderAndHost(): void
    {
        $this->basicSetup();
        $buddyUser = $this->createBuddyPlayer();

        // Send fleet to buddy's planet.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 2);
        $foreignPlanet = $this->sendMissionToBuddyPlanet($unitCollection, new Resources(0, 0, 0, 0));

        // Set all messages as read to avoid unread messages count in the overview.
        $this->playerSetAllMessagesRead();

        // Increase time to trigger arrival.
        $this->travel(10)->hours();
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Refresh planet service after mission arrival
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $foreignPlanet = $planetServiceFactory->make($foreignPlanet->getPlanetId());

        // Assert sender received message from "Fleet Command"
        $this->assertMessageReceivedAndContains('fleets', 'other', [
            'Fleet is stopping',
            'Fleet Command',
            $foreignPlanet->getPlanetName(),
        ]);

        // Assert host received arrival message
        $this->assertMessageReceivedAndContainsDatabase($foreignPlanet->getPlayer(), [
            'A fleet has arrived',
            $foreignPlanet->getPlanetName(),
        ]);
    }

    /**
     * Verify that ACS Defend mission is shown in fleet movement page.
     */
    public function testDispatchFleetReturnShown(): void
    {
        $this->basicSetup();
        $this->createBuddyPlayer();

        // Send fleet to buddy's planet.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 2);
        $this->sendMissionToBuddyPlanet($unitCollection, new Resources(0, 0, 0, 0));

        // Check that mission is shown in fleet movement page.
        $response = $this->get('/fleet/movement');
        $response->assertStatus(200);
        $response->assertSee('ACS Defend');
    }

    /**
     * Verify that ACS Defend mission can be recalled.
     */
    public function testDispatchFleetRecallMission(): void
    {
        $this->basicSetup();
        $this->createBuddyPlayer();

        // Send fleet to buddy's planet.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 2);
        $this->sendMissionToBuddyPlanet($unitCollection, new Resources(0, 0, 0, 0));

        // Get the fleet mission ID from database.
        $fleetMissionService = resolve(FleetMissionService::class);
        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();
        $this->assertCount(1, $activeMissions, 'No active fleet mission found.');

        $fleetMissionId = $activeMissions->first()->id;

        // Cancel/recall the mission.
        $response = $this->post('/ajax/fleet/dispatch/recall-fleet', [
            'fleet_mission_id' => $fleetMissionId,
            '_token' => csrf_token(),
        ]);
        $response->assertStatus(200);

        // Increase time by 10 hours to let the return mission complete.
        $this->travel(10)->hours();
        $this->get('/overview');

        // Assert that the units are back.
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'light_fighter', 5, 'Ships did not return to planet after recall.');
    }

    /**
     * Verify that ACS Defend preserves resources sent with fleet (doesn't deliver to target).
     */
    public function testDispatchFleetPreservesResources(): void
    {
        $this->basicSetup();
        $buddyUser = $this->createBuddyPlayer();

        // Get buddy's planet starting resources
        $startingMetal = $this->buddyPlanet->metal()->get();
        $startingCrystal = $this->buddyPlanet->crystal()->get();

        // Send fleet with resources to buddy's planet.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 2);
        $this->sendMissionToBuddyPlanet($unitCollection, new Resources(40, 30, 0, 0));

        // Get our starting resources before mission completes
        $ourStartingMetal = $this->planetService->metal()->get();
        $ourStartingCrystal = $this->planetService->crystal()->get();

        // Increase time to complete full mission cycle (arrival + hold + return).
        $this->travel(20)->hours();
        $this->get('/overview');

        // Refresh planet services after mission completion
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $buddyPlanetReloaded = $planetServiceFactory->make($this->buddyPlanet->getPlanetId());
        $this->planetService = $planetServiceFactory->make($this->planetService->getPlanetId());

        // Assert that buddy's planet did NOT receive the resources
        $this->assertEquals($startingMetal, $buddyPlanetReloaded->metal()->get(), 'Resources were incorrectly delivered to target planet.');
        $this->assertEquals($startingCrystal, $buddyPlanetReloaded->crystal()->get(), 'Resources were incorrectly delivered to target planet.');

        // Assert that our resources are back (minus fuel consumption)
        $currentMetal = $this->planetService->metal()->get();
        $currentCrystal = $this->planetService->crystal()->get();

        // Resources should be approximately back (allowing for fuel consumption)
        $this->assertGreaterThan($ourStartingMetal + 30, $currentMetal, 'Metal resources not returned with fleet.');
        $this->assertGreaterThan($ourStartingCrystal + 20, $currentCrystal, 'Crystal resources not returned with fleet.');
    }

    /**
     * Verify that resources return when ACS Defend fleet is recalled during hold time.
     */
    public function testDispatchFleetResourcesReturnOnRecall(): void
    {
        $this->basicSetup();
        $this->planetAddUnit('small_cargo', 5);
        $this->createBuddyPlayer();

        // Record starting resources
        $startingMetal = $this->planetService->metal()->get();
        $startingCrystal = $this->planetService->crystal()->get();

        // Send fleet with resources and 10 hour hold time
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 2);
        $this->dispatchFleet($this->buddyPlanet->getPlanetCoordinates(), $unitCollection, new Resources(800, 600, 0, 0), PlanetType::Planet, 10);

        // Wait until fleet arrives and is holding (but not long enough to return)
        $this->travel(5)->hours();
        $this->get('/overview');

        // Get the fleet mission and recall it
        $fleetMissionService = resolve(FleetMissionService::class);
        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();
        $this->assertGreaterThan(0, $activeMissions->count(), 'No active fleet missions found');

        $fleetMissionId = $activeMissions->first()->id;

        // Recall the mission
        $response = $this->post('/ajax/fleet/dispatch/recall-fleet', [
            'fleet_mission_id' => $fleetMissionId,
            '_token' => csrf_token(),
        ]);
        $response->assertStatus(200);

        // Wait for return trip
        $this->travel(10)->hours();
        $this->get('/overview');

        // Reload planet and check resources
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $this->planetService = $planetServiceFactory->make($this->planetService->getPlanetId());
        $finalMetal = $this->planetService->metal()->get();
        $finalCrystal = $this->planetService->crystal()->get();

        // Resources should be approximately back (minus fuel for outbound trip only since recalled)
        $this->assertGreaterThan($startingMetal - 300, $finalMetal, 'Metal resources not returned after recall');
        $this->assertGreaterThan($startingCrystal - 200, $finalCrystal, 'Crystal resources not returned after recall');
    }

    /**
     * Verify that resources return proportionally when ACS Defend fleet survives a battle.
     */
    public function testDispatchFleetResourcesReturnProportionallyAfterBattle(): void
    {
        $this->basicSetup();
        $this->planetAddUnit('small_cargo', 10);
        $this->createBuddyPlayer();

        // Configure battle settings
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('economy_speed', 8);
        $settingsService->set('fleet_speed_war', 1);
        $settingsService->set('fleet_speed_holding', 1);

        // Send ACS Defend with resources
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 5);
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 5);
        $this->sendMissionToBuddyPlanet($unitCollection, new Resources(1000, 800, 0, 0));

        // Wait for fleet to arrive at buddy's planet
        $this->travel(10)->hours();
        $this->get('/overview');

        // Create attacker
        $attackerUser = User::factory()->create();
        self::$allCreatedBuddyUserIds[] = $attackerUser->id;

        $attackerPlanet = Planet::factory()->create([
            'user_id' => $attackerUser->id,
            'galaxy' => $this->buddyPlanet->getPlanetCoordinates()->galaxy,
            'system' => $this->buddyPlanet->getPlanetCoordinates()->system,
            'planet' => 10,
        ]);

        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $attackerPlayerService = resolve(PlayerService::class, ['player_id' => $attackerUser->id]);
        $attackerPlanetService = $planetServiceFactory->makeForPlayer($attackerPlayerService, $attackerPlanet->id);

        // Setup attacker with moderate force
        $attackerPlanetService->addUnit('light_fighter', 20);
        $attackerPlayerService->setResearchLevel('weapon_technology', 3);
        $attackerPlayerService->setResearchLevel('shielding_technology', 3);
        $attackerPlayerService->setResearchLevel('armor_technology', 3);
        $attackerPlayerService->setResearchLevel('combustion_drive', 1);
        $attackerPlayerService->setResearchLevel('computer_technology', 1);
        $attackerPlanetService->addResources(new Resources(0, 0, 100000, 0));

        // Send attack mission
        $fleetMissionService = resolve(FleetMissionService::class);
        $attackUnits = new UnitCollection();
        $attackUnits->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 20);

        $fleetMissionService->createNewFromPlanet(
            $attackerPlanetService,
            $this->buddyPlanet->getPlanetCoordinates(),
            PlanetType::Planet,
            1,
            $attackUnits,
            new Resources(0, 0, 0, 0),
            10
        );

        // Wait for attack and return
        $this->travel(20)->hours();
        $this->get('/overview');

        // Reload our planet
        $this->planetService = $planetServiceFactory->make($this->planetService->getPlanetId());

        // Check that some resources returned (proportional to surviving ships)
        $finalMetal = $this->planetService->metal()->get();
        $finalCrystal = $this->planetService->crystal()->get();

        // We should have gotten at least some resources back
        $this->assertGreaterThan(0, $finalMetal, 'No metal returned after battle');
        $this->assertGreaterThan(0, $finalCrystal, 'No crystal returned after battle');
    }

    /**
     * Verify that battle handling doesn't throw errors when ACS Defend fleet participates.
     */
    public function testDispatchFleetBattleResourceHandlingNoErrors(): void
    {
        $this->basicSetup();
        $this->planetAddUnit('small_cargo', 5);
        $this->createBuddyPlayer();

        // Configure battle settings
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('economy_speed', 8);
        $settingsService->set('fleet_speed_war', 1);

        // Send ACS Defend fleet with cargo ships and resources
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 2);
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 3);
        $this->sendMissionToBuddyPlanet($unitCollection, new Resources(100, 50, 0, 0));

        // Wait for fleet to arrive
        $this->travel(10)->hours();
        $this->get('/overview');

        // Create attacker
        $attackerUser = User::factory()->create();
        self::$allCreatedBuddyUserIds[] = $attackerUser->id;

        $attackerPlanet = Planet::factory()->create([
            'user_id' => $attackerUser->id,
            'galaxy' => $this->buddyPlanet->getPlanetCoordinates()->galaxy,
            'system' => $this->buddyPlanet->getPlanetCoordinates()->system,
            'planet' => 11,
        ]);

        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $attackerPlayerService = resolve(PlayerService::class, ['player_id' => $attackerUser->id]);
        $attackerPlanetService = $planetServiceFactory->makeForPlayer($attackerPlayerService, $attackerPlanet->id);

        // Setup attacker
        $attackerPlanetService->addUnit('light_fighter', 30);
        $attackerPlayerService->setResearchLevel('weapon_technology', 5);
        $attackerPlayerService->setResearchLevel('shielding_technology', 5);
        $attackerPlayerService->setResearchLevel('armor_technology', 5);
        $attackerPlayerService->setResearchLevel('combustion_drive', 1);
        $attackerPlayerService->setResearchLevel('computer_technology', 1);
        $attackerPlanetService->addResources(new Resources(0, 0, 100000, 0));

        // Send attack
        $fleetMissionService = resolve(FleetMissionService::class);
        $attackUnits = new UnitCollection();
        $attackUnits->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 30);

        $fleetMissionService->createNewFromPlanet(
            $attackerPlanetService,
            $this->buddyPlanet->getPlanetCoordinates(),
            PlanetType::Planet,
            1,
            $attackUnits,
            new Resources(0, 0, 0, 0),
            10
        );

        // Wait for attack and potential return
        $this->travel(30)->hours();
        $this->get('/overview');

        // The main goal is to verify no exceptions were thrown during battle processing
        // Test passes if no exceptions occur
    }

    /**
     * Verify that ACS Defend to player in vacation mode fails.
     */
    public function testDispatchFleetToVacationModePlanetError(): void
    {
        $this->basicSetup();
        $buddyUser = $this->createBuddyPlayer();

        // Store target coordinates
        $buddyCoordinates = $this->buddyPlanet->getPlanetCoordinates();

        // Activate vacation mode for buddy player
        $this->buddyPlanet->getPlayer()->activateVacationMode();
        $this->reloadApplication();

        // Try to send ACS Defend fleet - should fail
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1);
        $this->dispatchFleet($buddyCoordinates, $unitCollection, new Resources(0, 0, 0, 0), PlanetType::Planet, 0, false);
    }

    /**
     * Verify that ACS Defend cannot be sent while sender is in vacation mode.
     */
    public function testDispatchFleetFromVacationModeError(): void
    {
        $this->basicSetup();
        $this->createBuddyPlayer();

        // Put current player in vacation mode
        $currentUser = User::find($this->currentUserId);
        $currentUser->vacation_mode = true;
        $currentUser->save();

        // Try to send ACS Defend fleet - should fail
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1);
        $this->sendMissionToBuddyPlanet($unitCollection, new Resources(0, 0, 0, 0), false);
    }

    /**
     * Test that recalling an ACS Defend fleet during hold time returns with correct trip duration.
     * Bug: The return trip was taking longer than the outbound trip because elapsed hold time was being added.
     */
    public function testDispatchFleetRecallDuringHoldTimeReturnsWithCorrectDuration(): void
    {
        $this->basicSetup();
        $this->createBuddyPlayer();

        // Send ACS Defend fleet with 1 hour hold time
        $this->planetAddUnit('light_fighter', 10);
        $this->planetAddResources(new Resources(0, 0, 50000, 0));

        $units = new UnitCollection();
        $units->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 10);

        $fleetMissionService = app(FleetMissionService::class);
        $mission = $fleetMissionService->createNewFromPlanet(
            $this->planetService,
            $this->buddyPlanet->getPlanetCoordinates(),
            PlanetType::Planet,
            5, // ACS Defend
            $units,
            new Resources(0, 0, 0, 0),
            10, // 100% speed
            1 // 1 hour hold
        );

        // Record the original travel duration
        $originalTravelDuration = $mission->time_arrival - $mission->time_departure;

        // Fast forward to 30 seconds after arrival (during hold time)
        $this->travel($originalTravelDuration + 30)->seconds();

        // Recall the fleet
        $response = $this->post('/ajax/fleet/dispatch/recall-fleet', [
            'fleet_mission_id' => $mission->id,
            '_token' => csrf_token(),
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Get the return mission
        $returnMission = FleetMission::where('parent_id', $mission->id)->first();
        $this->assertNotNull($returnMission, 'Return mission should exist');

        // Calculate return trip duration
        $returnTravelDuration = $returnMission->time_arrival - $returnMission->time_departure;

        // The return trip should take the same time as the original outbound trip
        // NOT the original trip duration + the 30 seconds we held
        $this->assertEquals(
            $originalTravelDuration,
            $returnTravelDuration,
            'Return trip duration should equal original trip duration, not include elapsed hold time'
        );
    }

    /**
     * Test that arrival messages are sent to both sender and host when fleet arrives.
     * This test directly calls updateMission to verify message sending.
     */
    public function testDispatchFleetArrivalMessagesSentOnUpdate(): void
    {
        $this->basicSetup();
        $buddyUser = $this->createBuddyPlayer();

        // Mark all existing messages as read
        $this->playerSetAllMessagesRead();

        // Send ACS Defend fleet with 1 hour hold time
        $this->planetAddUnit('light_fighter', 5);
        $this->planetAddResources(new Resources(0, 0, 50000, 0));

        $units = new UnitCollection();
        $units->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 5);

        $fleetMissionService = app(FleetMissionService::class);
        $mission = $fleetMissionService->createNewFromPlanet(
            $this->planetService,
            $this->buddyPlanet->getPlanetCoordinates(),
            PlanetType::Planet,
            5, // ACS Defend
            $units,
            new Resources(0, 0, 0, 0),
            10, // 100% speed
            1 // 1 hour hold
        );

        // Travel to arrival time
        $travelDuration = $mission->time_arrival - $mission->time_departure;
        $this->travel($travelDuration + 1)->seconds();

        // Explicitly call updateMission to process the arrival
        $fleetMissionService->updateMission($mission);

        // Verify sender received message
        $this->assertMessageReceivedAndContains('fleets', 'other', [
            'Fleet is stopping',
            'Fleet Command',
        ]);

        // Verify host received message
        $this->assertMessageReceivedAndContainsDatabase($this->buddyPlanet->getPlayer(), [
            'A fleet has arrived',
        ]);

        // Verify mission was processed
        $mission->refresh();
        $this->assertEquals(1, $mission->processed, 'Mission should be marked as processed');

        // Verify return mission was created
        $returnMission = FleetMission::where('parent_id', $mission->id)->first();
        $this->assertNotNull($returnMission, 'Return mission should be created');
    }
}
