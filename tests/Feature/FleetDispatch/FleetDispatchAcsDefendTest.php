<?php

namespace Tests\Feature\FleetDispatch;

use Illuminate\Support\Facades\DB;
use OGame\Factories\PlanetServiceFactory;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\AllianceMember;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Message;
use OGame\Models\Planet;
use OGame\Models\Resources;
use OGame\Models\User;
use OGame\Services\AllianceService;
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
     * @var PlanetService|null The alliance member's planet (stored to ensure consistency within a test)
     */
    protected ?PlanetService $allianceMemberPlanet = null;

    /**
     * @var PlanetService|null The non-affiliated player's planet (stored to ensure consistency within a test)
     */
    protected ?PlanetService $otherPlanet = null;

    /**
     * @var array<int> Track all buddy user IDs created across all tests for cleanup
     * Static to persist across test instances and avoid losing IDs during setUp()
     */
    protected static array $allCreatedBuddyUserIds = [];

    /**
     * @var int|null Track the current user's alliance ID for cleanup
     */
    protected ?int $createdAllianceId = null;

    /**
     * Set up the test case.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->buddyPlanet = null;
        $this->allianceMemberPlanet = null;
        $this->otherPlanet = null;
        $this->createdAllianceId = null;
    }

    /**
     * Clean up test data after each test to prevent state leakage.
     * Only removes buddy relationships, alliance data, and resets vacation mode - test users and planets
     * remain in database but won't have special state that affects subsequent tests.
     *
     * @todo Refactor test architecture to support DatabaseTransactions/RefreshDatabase
     *       by removing dependency on reloadApplication() expecting persisted users.
     */
    protected function tearDown(): void
    {
        // Clean up alliance data created during this test
        if ($this->createdAllianceId !== null) {
            // Delete alliance members
            DB::table('alliance_members')
                ->where('alliance_id', $this->createdAllianceId)
                ->delete();

            // Delete alliance applications
            DB::table('alliance_applications')
                ->where('alliance_id', $this->createdAllianceId)
                ->delete();

            // Delete alliance
            DB::table('alliances')
                ->where('id', $this->createdAllianceId)
                ->delete();

            // Reset current user's alliance_id
            if (isset($this->currentUserId)) {
                DB::table('users')
                    ->where('id', $this->currentUserId)
                    ->update([
                        'alliance_id' => null,
                        'alliance_left_at' => null,
                    ]);
            }
        }

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

            // Reset alliance_id for this user (in case they were added as alliance member)
            DB::table('users')
                ->where('id', $buddyUserId)
                ->update([
                    'alliance_id' => null,
                    'alliance_left_at' => null,
                ]);

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

        // Record the original travel duration (physical arrival, not including hold time)
        $physicalArrivalTime = $mission->time_arrival - $mission->time_holding;
        $originalTravelDuration = $physicalArrivalTime - $mission->time_departure;

        // Fast forward to 30 seconds after physical arrival (during hold time)
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
     * Test that arrival messages are sent to both sender and host at physical arrival time,
     * not delayed until hold expiry.
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

        // Calculate physical arrival time (when fleet actually arrives at target)
        $physicalArrivalTime = $mission->time_arrival - $mission->time_holding;
        $travelDuration = $physicalArrivalTime - $mission->time_departure;

        // Travel to physical arrival time and trigger a page load
        $this->travel($travelDuration + 1)->seconds();
        $this->get('/overview');

        // Messages should be sent at physical arrival, not delayed until hold expiry
        $this->assertMessageReceivedAndContains('fleets', 'other', [
            'Fleet is stopping',
            'Fleet Command',
        ]);
        $this->assertMessageReceivedAndContainsDatabase($this->buddyPlanet->getPlayer(), [
            'A fleet has arrived',
        ]);

        // Mission should still be unprocessed (hold not expired)
        $mission->refresh();
        $this->assertEquals(0, $mission->processed, 'Mission should NOT be marked as processed during hold time');
        $this->assertEquals(1, $mission->processed_hold, 'Mission should be marked processed_hold after physical arrival');

        // No return mission yet
        $returnMission = FleetMission::where('parent_id', $mission->id)->first();
        $this->assertNull($returnMission, 'Return mission should NOT exist during hold time');

        // Travel past hold expiry and trigger processing
        $remainingHoldTime = $mission->time_arrival - $physicalArrivalTime;
        $this->travel($remainingHoldTime + 1)->seconds();
        $this->get('/overview');

        // Mission should now be fully processed and return mission created
        $mission->refresh();
        $this->assertEquals(1, $mission->processed, 'Mission should be marked as processed after hold expires');
        $returnMission = FleetMission::where('parent_id', $mission->id)->first();
        $this->assertNotNull($returnMission, 'Return mission should be created after hold time expires');
    }

    /**
     * Test that arrival messages are sent to sender and host even when the fleet is recalled
     * during hold time before any page load has triggered normal message dispatch.
     */
    public function testDispatchFleetArrivalMessagesSentOnRecallDuringHoldTime(): void
    {
        $this->basicSetup();
        $this->createBuddyPlayer();
        $this->playerSetAllMessagesRead();

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

        // Advance into the hold period without triggering any page load
        $physicalArrivalTime = $mission->time_arrival - $mission->time_holding;
        $travelDuration = $physicalArrivalTime - $mission->time_departure;
        $this->travel($travelDuration + 10)->seconds();

        // Recall immediately (no page load since physical arrival, so processed_hold is still 0)
        $mission->refresh();
        $this->assertEquals(0, $mission->processed_hold, 'processed_hold should be 0 before any page load');

        $response = $this->post('/ajax/fleet/dispatch/recall-fleet', [
            'fleet_mission_id' => $mission->id,
            '_token' => csrf_token(),
        ]);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Arrival messages should have been sent by cancelMission() itself
        $this->assertMessageReceivedAndContains('fleets', 'other', [
            'Fleet is stopping',
            'Fleet Command',
        ]);
        $this->assertMessageReceivedAndContainsDatabase($this->buddyPlanet->getPlayer(), [
            'A fleet has arrived',
        ]);
    }

    /**
     * Test that ships are not duplicated when missions complete after extended time periods.
     * Verifies that return missions process correctly even when the complete round trip
     * (outbound + hold + return) has elapsed.
     */
    public function testDispatchFleetNoShipDuplicationWithHighSpeedMultiplier(): void
    {
        $this->basicSetup();
        $buddyUser = $this->createBuddyPlayer();

        // Add 1 battlecruiser to the planet
        $this->planetAddUnit('battlecruiser', 1);

        // Record initial ship count
        $initialBattlecruiserCount = $this->planetService->getObjectAmount('battlecruiser');
        $this->assertEquals(1, $initialBattlecruiserCount, 'Should start with 1 battlecruiser');

        // Dispatch the fleet with 1 hour hold time
        $this->planetAddResources(new Resources(0, 0, 50000, 0));

        $units = new UnitCollection();
        $units->addUnit(ObjectService::getUnitObjectByMachineName('battlecruiser'), 1);

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

        // Verify ship was deducted
        $this->planetService->reloadPlanet();
        $this->assertEquals(0, $this->planetService->getObjectAmount('battlecruiser'), 'Battlecruiser should be deducted after dispatch');

        // Get mission timings
        $fleetMissionDuration = $mission->time_arrival - $mission->time_departure;
        $settingsService = app(SettingsService::class);
        $actualHoldingTime = (int)($mission->time_holding / $settingsService->fleetSpeedHolding());

        // Travel to arrival + hold time + return trip time (complete round trip)
        $totalMissionTime = $fleetMissionDuration + $actualHoldingTime + $fleetMissionDuration;
        $this->travel($totalMissionTime + 1)->seconds();

        // Trigger fleet mission processing by calling overview
        // This will process the expired hold time and create/process the return mission
        $this->get('/overview');

        // Verify return mission was created and processed
        $returnMission = FleetMission::where('parent_id', $mission->id)->first();
        $this->assertNotNull($returnMission, 'Return mission should be created');

        // Reload planet to get latest state
        $this->planetService->reloadPlanet();

        // Verify exactly 1 battlecruiser is back (no duplication)
        $finalBattlecruiserCount = $this->planetService->getObjectAmount('battlecruiser');
        $this->assertEquals(1, $finalBattlecruiserCount, 'Should have exactly 1 battlecruiser after mission completes (no duplication)');

        // Verify all missions are processed
        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();
        $this->assertCount(0, $activeMissions, 'There should be no active missions after complete round trip');
    }

    /**
     * Test that ACS Defend return missions are hidden from the fleet overview until departure.
     * With the new architecture, return missions don't exist during hold time, so ships
     * are not duplicated in the fleet list.
     */
    public function testDispatchFleetAcsDefendReturnMissionHiddenUntilDeparture(): void
    {
        $this->basicSetup();
        $buddyUser = $this->createBuddyPlayer();

        // Add 1 battlecruiser to the planet
        $this->planetAddUnit('battlecruiser', 1);
        $this->planetAddResources(new Resources(0, 0, 50000, 0));

        $units = new UnitCollection();
        $units->addUnit(ObjectService::getUnitObjectByMachineName('battlecruiser'), 1);

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

        // With the new architecture, time_arrival includes hold time (as raw game time)
        // Calculate physical arrival time (when fleet actually arrives at target)
        $physicalArrivalTime = $mission->time_arrival - $mission->time_holding;

        // Travel to physical arrival time (when fleet actually arrives)
        $travelDuration = $physicalArrivalTime - $mission->time_departure;
        $this->travel($travelDuration + 1)->seconds();

        // With the new architecture, NO return mission exists during hold time
        // This is the key difference from the old behavior
        $returnMission = FleetMission::where('parent_id', $mission->id)->first();
        $this->assertNull($returnMission, 'Return mission should NOT exist during hold time');

        // Get active missions
        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();

        // Debug: Show all missions
        $debugInfo = [];
        foreach ($activeMissions as $activeMission) {
            $debugInfo[] = sprintf(
                "ID=%d type=%d parent=%s proc=%d depart=%d now=%d bc=%d",
                $activeMission->id,
                $activeMission->mission_type,
                $activeMission->parent_id ?? 'null',
                $activeMission->processed,
                $activeMission->time_departure,
                time(),
                $activeMission->battlecruiser
            );
        }

        // Count battlecruisers visible in active missions
        $totalBattlecruisersInMissions = 0;
        foreach ($activeMissions as $activeMission) {
            $totalBattlecruisersInMissions += $activeMission->battlecruiser;
        }

        // Should only see 1 battlecruiser (from outbound/holding mission)
        // No return mission exists during hold time, so no duplication possible
        $this->assertEquals(
            1,
            $totalBattlecruisersInMissions,
            'Should only see 1 battlecruiser in active missions (no return mission during hold time). Found: ' . implode(', ', $debugInfo)
        );
    }

    /**
     * Verify that recalling an ACS Defend fleet during hold time via the fleet event list widget
     * works correctly — the widget recall button must use the real mission ID, not the synthetic
     * DOM offset ID (real_id + 888888), which would fail for large real IDs.
     */
    public function testDispatchFleetRecallDuringHoldTimeViaEventListWidget(): void
    {
        $this->basicSetup();
        $this->createBuddyPlayer();

        // Send ACS Defend with 1 hour hold time
        $units = new UnitCollection();
        $units->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 2);
        $this->dispatchFleet($this->buddyPlanet->getPlanetCoordinates(), $units, new Resources(0, 0, 0, 0), PlanetType::Planet, 1);

        // Get the real mission record
        $fleetMissionService = resolve(FleetMissionService::class);
        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();
        $this->assertCount(1, $activeMissions, 'No active fleet mission found.');
        $mission = $activeMissions->first();
        $realMissionId = $mission->id;

        // Advance time into the hold period (physical arrival + 10 seconds)
        $physicalArrivalTime = $mission->time_arrival - $mission->time_holding;
        $travelDuration = $physicalArrivalTime - $mission->time_departure;
        $this->travel($travelDuration + 10)->seconds();
        $this->get('/overview');

        // Fetch the fleet event list — this is the HTML the widget loads
        $response = $this->get('/ajax/fleet/eventlist/fetch');
        $response->assertStatus(200);

        // The recall button for the hold-period waitEndRow must carry the real mission ID,
        // not real_id + 888888 (which overflows into the 999999 range for large IDs and
        // causes the recall endpoint to derive the wrong mission ID, leading to HTTP 500).
        $html = (string)$response->getContent();
        $this->assertStringContainsString(
            'data-fleet-id="' . $realMissionId . '"',
            $html,
            'Fleet event list recall button should use real mission ID in data-fleet-id'
        );

        // Simulate clicking the recall button (sends the real mission ID, as the widget now does)
        $response = $this->post('/ajax/fleet/dispatch/recall-fleet', [
            'fleet_mission_id' => $realMissionId,
            '_token' => csrf_token(),
        ]);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Wait for the return trip to complete
        $this->travel(10)->hours();
        $this->get('/overview');

        // Ships should be back on planet
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'light_fighter', 5, 'Ships did not return after widget recall during hold time.');
    }

    /**
     * Assert that trying to dispatch ACS Defend to alliance member planet succeeds.
     */
    public function testFleetCheckToAllianceMemberPlanetSuccess(): void
    {
        $this->basicSetup();
        $allianceMemberUser = $this->createAllianceMemberPlayer();

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1);
        $this->checkTargetFleet($this->allianceMemberPlanet->getPlanetCoordinates(), $unitCollection, PlanetType::Planet, true);
    }

    /**
     * Assert that trying to dispatch ACS Defend to non-alliance, non-buddy planet fails.
     */
    public function testFleetCheckToNonAllianceNonBuddyPlanetError(): void
    {
        $this->basicSetup();
        $otherUser = $this->createNonAffiliatedPlayer();

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1);
        $this->checkTargetFleet($this->otherPlanet->getPlanetCoordinates(), $unitCollection, PlanetType::Planet, false);
    }

    /**
     * Verify that dispatching ACS Defend to alliance member works and returns correctly.
     */
    public function testDispatchFleetReturnTripToAllianceMember(): void
    {
        $this->basicSetup();
        $allianceMemberUser = $this->createAllianceMemberPlayer();

        // Assert starting units
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'light_fighter', 5, 'Light Fighters are not at 5 units at beginning of test.');

        // Send fleet to alliance member's planet.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 2);
        $this->dispatchFleet($this->allianceMemberPlanet->getPlanetCoordinates(), $unitCollection, new Resources(0, 0, 0, 0), PlanetType::Planet, 2);

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
     * Create an alliance relationship between current player and another player.
     * Both players are added to the same alliance.
     *
     * @return User The alliance member user
     */
    protected function createAllianceMemberPlayer(): User
    {
        $allianceService = resolve(AllianceService::class);

        // Create alliance for current user
        $alliance = $allianceService->createAlliance($this->currentUserId, 'TAG', 'Test Alliance');
        $this->createdAllianceId = $alliance->id;

        // Create a fresh user for alliance member
        $allianceMemberUser = User::factory()->create();
        self::$allCreatedBuddyUserIds[] = $allianceMemberUser->id;

        // Create a planet for the alliance member
        $allianceMemberPlanet = Planet::factory()->create([
            'user_id' => $allianceMemberUser->id,
            'galaxy' => $this->planetService->getPlanetCoordinates()->galaxy,
            'system' => min(499, $this->planetService->getPlanetCoordinates()->system + 6),
            'planet' => 13,
        ]);

        // Get planet service for the alliance member's planet
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $allianceMemberPlayerService = resolve(PlayerService::class, ['player_id' => $allianceMemberUser->id]);
        $this->allianceMemberPlanet = $planetServiceFactory->makeForPlayer($allianceMemberPlayerService, $allianceMemberPlanet->id);

        // Add new member to alliance (bypass cooldown for testing)
        /** @phpstan-ignore assign.propertyType */
        $allianceMemberUser->alliance_id = $alliance->id;
        $allianceMemberUser->alliance_left_at = null;
        $allianceMemberUser->save();

        AllianceMember::create([
            'alliance_id' => $alliance->id,
            'user_id' => $allianceMemberUser->id,
            'rank_id' => null,
            'joined_at' => now(),
        ]);

        return $allianceMemberUser;
    }

    /**
     * Create a player that is neither buddy nor alliance member.
     *
     * @return User The non-affiliated user
     */
    protected function createNonAffiliatedPlayer(): User
    {
        // Create a fresh user that is not a buddy or alliance member
        $otherUser = User::factory()->create();
        self::$allCreatedBuddyUserIds[] = $otherUser->id;

        // Create a planet for the other user
        $otherPlanet = Planet::factory()->create([
            'user_id' => $otherUser->id,
            'galaxy' => $this->planetService->getPlanetCoordinates()->galaxy,
            'system' => min(499, $this->planetService->getPlanetCoordinates()->system + 7),
            'planet' => 14,
        ]);

        // Get planet service for the other user's planet
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $otherPlayerService = resolve(PlayerService::class, ['player_id' => $otherUser->id]);
        $this->otherPlanet = $planetServiceFactory->makeForPlayer($otherPlayerService, $otherPlanet->id);

        return $otherUser;
    }
}
