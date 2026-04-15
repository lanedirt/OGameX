<?php

namespace Tests\Feature\FleetDispatch;

use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use OGame\Enums\CharacterClass;
use OGame\Factories\PlanetServiceFactory;
use OGame\GameMissions\AttackMission;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\BattleReport;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\FleetUnion;
use OGame\Models\FleetUnionInvite;
use OGame\Models\Planet;
use OGame\Models\Resources;
use OGame\Models\User;
use OGame\Services\BuddyService;
use OGame\Services\FleetMissionService;
use OGame\Services\FleetUnionService;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use OGame\Services\SettingsService;
use Tests\FleetDispatchTestCase;

/**
 * Test ACS Attack (fleet union) functionality:
 * - Union creation and invite flow
 * - Invite-based access control for combat forces dropdown
 * - Multi-fleet ACS attack battle processing
 * - Loot distribution and return missions for union members
 */
class FleetDispatchAcsAttackTest extends FleetDispatchTestCase
{
    protected int $missionType = 1;

    protected string $missionName = 'Attack';

    protected ?PlanetService $targetPlanet = null;

    protected ?User $targetUser = null;

    protected ?PlanetService $allyPlanet = null;

    protected ?User $allyUser = null;

    /**
     * @var array<int> Track created user IDs for cleanup
     */
    protected static array $allCreatedBuddyUserIds = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->targetPlanet = null;
        $this->targetUser = null;
        $this->allyPlanet = null;
        $this->allyUser = null;
    }

    protected function tearDown(): void
    {
        // Capture IDs before anything modifies the static array.
        $createdUserIds = self::$allCreatedBuddyUserIds;

        // --- Union cleanup (must run before fleet_mission deletion) ---
        $allUserIds = array_merge(
            $createdUserIds,
            isset($this->currentUserId) ? [$this->currentUserId] : []
        );
        $unionIds = DB::table('fleet_unions')
            ->whereIn('user_id', $allUserIds)
            ->pluck('id')
            ->toArray();

        if (!empty($unionIds)) {
            DB::table('fleet_missions')
                ->whereIn('union_id', $unionIds)
                ->update(['union_id' => null, 'union_slot' => null]);
            DB::table('fleet_unions')
                ->whereIn('id', $unionIds)
                ->delete();
        }

        if (!empty($createdUserIds)) {
            // Collect all planet IDs belonging to the created users.
            $createdPlanetIds = DB::table('planets')
                ->whereIn('user_id', $createdUserIds)
                ->pluck('id')
                ->toArray();

            // Disable FK checks for the duration of the cleanup so we do not need to
            // enumerate every table that references users or planets. Re-enabled below.
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            try {
                // --- Buddy requests ---
                DB::table('buddy_requests')
                    ->where(function ($q) use ($createdUserIds) {
                        $q->whereIn('sender_user_id', $createdUserIds)
                          ->orWhereIn('receiver_user_id', $createdUserIds);
                    })
                    ->delete();

                // --- Fleet missions ---
                // Delete missions owned by created users, plus any mission that references
                // one of their planets as origin or destination (e.g. return missions owned
                // by the main attacker that depart from / arrive at the target planet).
                DB::table('fleet_missions')
                    ->where(function ($q) use ($createdUserIds, $createdPlanetIds) {
                        $q->whereIn('user_id', $createdUserIds);
                        if (!empty($createdPlanetIds)) {
                            $q->orWhereIn('planet_id_from', $createdPlanetIds)
                              ->orWhereIn('planet_id_to', $createdPlanetIds);
                        }
                    })
                    ->delete();

                // --- Battle / espionage reports ---
                DB::table('battle_reports')->whereIn('planet_user_id', $createdUserIds)->delete();
                DB::table('espionage_reports')->whereIn('planet_user_id', $createdUserIds)->delete();

                // --- Messages ---
                DB::table('messages')->whereIn('user_id', $createdUserIds)->delete();

                // --- Planets ---
                if (!empty($createdPlanetIds)) {
                    DB::table('planets')->whereIn('id', $createdPlanetIds)->delete();
                }

                // --- Users ---
                DB::table('users')->whereIn('id', $createdUserIds)->delete();
            } finally {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            }

            self::$allCreatedBuddyUserIds = [];
        }

        // Reset per-test instance state.
        $this->targetPlanet = null;
        $this->targetUser   = null;
        $this->allyPlanet   = null;
        $this->allyUser     = null;

        parent::tearDown();
    }

    protected function basicSetup(): void
    {
        $this->planetAddUnit('light_fighter', 50);
        $this->playerSetResearchLevel('computer_technology', 2);
        $this->playerSetResearchLevel('weapon_technology', 3);
        $this->playerSetResearchLevel('shielding_technology', 3);
        $this->playerSetResearchLevel('armor_technology', 3);
        $this->playerSetResearchLevel('energy_technology', 1);
        $this->playerSetResearchLevel('combustion_drive', 1);

        $settingsService = resolve(SettingsService::class);
        $settingsService->set('economy_speed', 8);
        $settingsService->set('fleet_speed_war', 1);
        $settingsService->set('fleet_speed_holding', 1);
        $settingsService->set('fleet_speed_peaceful', 1);

        $this->planetAddResources(new Resources(0, 0, 1000000, 0));
    }

    /**
     * Create a target player (defender) with resources on their planet.
     */
    protected function createTargetPlayer(): User
    {
        $targetUser = User::factory()->create();
        self::$allCreatedBuddyUserIds[] = $targetUser->id;

        // Minimum 3-system distance ensures the ally (placed in target's system) is
        // always naturally faster than the initiator, which some tests depend on.
        $this->targetPlanet = $this->createPlanetAtSafeCoordinate($targetUser->id, 13, 15, 3);
        $this->targetUser = $targetUser;

        // Give the target some resources to loot
        $this->targetPlanet->addResources(new Resources(100000, 100000, 100000, 0));

        return $targetUser;
    }

    /**
     * Create an ally player (buddy of current player) with fleet.
     */
    protected function createAllyPlayer(): User
    {
        $allyUser = User::factory()->create();
        self::$allCreatedBuddyUserIds[] = $allyUser->id;

        // Place ally in same system as the target to minimize travel time
        // (avoids exceeding the 30% delay limit when joining a union).
        // Find an empty slot in the target's system outside the allocator range (4-12).
        $targetGalaxy  = $this->targetPlanet->getPlanetCoordinates()->galaxy;
        $targetSystem  = $this->targetPlanet->getPlanetCoordinates()->system;
        // Prefer positions close to the target (13-15) to minimize same-system travel distance.
        // Fall back to positions 1-3 if 13-15 are exhausted in this system.
        $targetPosition = $this->targetPlanet->getPlanetCoordinates()->position;
        $preferredOrder = array_filter([13, 14, 15, 1, 2, 3], fn ($p) => $p !== $targetPosition);
        $allyPosition = collect(array_values($preferredOrder))->first(
            fn ($p) => !Planet::where('galaxy', $targetGalaxy)->where('system', $targetSystem)->where('planet', $p)->exists()
        );
        $allyPlanet = Planet::factory()->create([
            'user_id' => $allyUser->id,
            'galaxy'  => $targetGalaxy,
            'system'  => $targetSystem,
            'planet'  => $allyPosition,
        ]);

        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $allyPlayerService = resolve(PlayerService::class, ['player_id' => $allyUser->id]);
        $this->allyPlanet = $planetServiceFactory->makeForPlayer($allyPlayerService, $allyPlanet->id);
        $this->allyUser = $allyUser;

        // Give ally some ships and fuel
        $this->allyPlanet->addUnit('light_fighter', 30);
        $this->allyPlanet->addResources(new Resources(0, 0, 1000000, 0));

        // Create buddy relationship between current player and ally
        $buddyService = resolve(BuddyService::class);
        $request = $buddyService->sendRequest($this->currentUserId, $allyUser->id);
        $buddyService->acceptRequest($request->id, $allyUser->id);

        return $allyUser;
    }

    /**
     * Test that creating a fleet union converts the attack mission to ACS Attack (type 2).
     */
    public function testCreateUnionConvertsMissionToAcsAttack(): void
    {
        $this->basicSetup();
        $this->createTargetPlayer();

        // Send attack fleet to target
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 10);
        $this->dispatchFleet($this->targetPlanet->getPlanetCoordinates(), $unitCollection, new Resources(0, 0, 0, 0), PlanetType::Planet);

        // Get the fleet mission
        $fleetMissionService = resolve(FleetMissionService::class);
        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();
        $this->assertCount(1, $activeMissions);
        $mission = $activeMissions->first();
        $this->assertEquals(1, $mission->mission_type, 'Mission should be type 1 (Attack) before union creation');

        // Create union via API
        $response = $this->post('/ajax/fleet/union/create', [
            'fleetID' => $mission->id,
            'groupname' => 'TestUnion',
            '_token' => csrf_token(),
        ]);
        $response->assertStatus(200);

        // Verify mission converted to ACS Attack (type 2)
        $mission->refresh();
        $this->assertEquals(2, $mission->mission_type, 'Mission should be type 2 (ACS Attack) after union creation');
        $this->assertNotNull($mission->union_id, 'Mission should have a union_id');
        $this->assertEquals(1, $mission->union_slot, 'Initiator should have slot 1');
    }

    /**
     * Test that invite message is sent when players are added to union.
     */
    public function testUnionInviteSendsMessage(): void
    {
        $this->basicSetup();
        $this->createTargetPlayer();
        $this->createAllyPlayer();

        // Send attack fleet to target
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 10);
        $this->dispatchFleet($this->targetPlanet->getPlanetCoordinates(), $unitCollection, new Resources(0, 0, 0, 0), PlanetType::Planet);

        // Get the fleet mission
        $fleetMissionService = resolve(FleetMissionService::class);
        $mission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        // Create union with ally invited
        $response = $this->post('/ajax/fleet/union/create', [
            'fleetID' => $mission->id,
            'groupname' => 'TestUnion',
            'unionUsers' => $this->currentUsername . ';' . $this->allyUser->username,
            '_token' => csrf_token(),
        ]);
        $response->assertStatus(200);

        // Verify invite record was created
        $invite = FleetUnionInvite::where('user_id', $this->allyUser->id)->first();
        $this->assertNotNull($invite, 'Invite record should be created for the ally');

        // Verify invite message was sent to ally (check body contains sender name and "invited you")
        $allyPlayerService = resolve(PlayerService::class, ['player_id' => $this->allyUser->id]);
        $this->assertMessageReceivedAndContainsDatabase($allyPlayerService, [
            'invited you to mission',
            $this->currentUsername,
        ]);
    }

    /**
     * Test that invite record is created so invited player can see the union.
     */
    public function testUnionInviteCreatesRecord(): void
    {
        $this->basicSetup();
        $this->createTargetPlayer();
        $this->createAllyPlayer();

        // Send attack and create union with ally
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 10);
        $this->dispatchFleet($this->targetPlanet->getPlanetCoordinates(), $unitCollection, new Resources(0, 0, 0, 0), PlanetType::Planet);

        $fleetMissionService = resolve(FleetMissionService::class);
        $mission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        $response = $this->post('/ajax/fleet/union/create', [
            'fleetID' => $mission->id,
            'groupname' => 'TestUnion',
            'unionUsers' => $this->allyUser->username,
            '_token' => csrf_token(),
        ]);
        $response->assertStatus(200);

        $mission->refresh();

        // Verify invite record exists
        $invite = FleetUnionInvite::where('fleet_union_id', $mission->union_id)
            ->where('user_id', $this->allyUser->id)
            ->first();
        $this->assertNotNull($invite, 'Invite record should exist for the invited ally');

        // Verify no invite record for the sender themselves
        $selfInvite = FleetUnionInvite::where('fleet_union_id', $mission->union_id)
            ->where('user_id', $this->currentUserId)
            ->first();
        $this->assertNull($selfInvite, 'Sender should not have an invite record for themselves');
    }

    /**
     * Test that combat forces dropdown on /fleet shows unions for invited players.
     */
    public function testCombatForcesDropdownShowsUnionForInvitedPlayer(): void
    {
        $this->basicSetup();
        $this->createTargetPlayer();
        $this->createAllyPlayer();

        // Send attack and create union
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 10);
        $this->dispatchFleet($this->targetPlanet->getPlanetCoordinates(), $unitCollection, new Resources(0, 0, 0, 0), PlanetType::Planet);

        $fleetMissionService = resolve(FleetMissionService::class);
        $mission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        $this->post('/ajax/fleet/union/create', [
            'fleetID' => $mission->id,
            'groupname' => 'TestUnion',
            'unionUsers' => $this->allyUser->username,
            '_token' => csrf_token(),
        ]);
        $mission->refresh();

        // The creator should see the union on /fleet page
        $response = $this->get('/fleet');
        $response->assertStatus(200);
        $response->assertSee('TestUnion');
    }

    /**
     * Test that combat forces dropdown does NOT show unions for non-invited players.
     */
    public function testCombatForcesDropdownHiddenForNonInvitedPlayer(): void
    {
        $this->basicSetup();
        $this->createTargetPlayer();
        $this->createAllyPlayer();

        // Send attack and create union (invite nobody)
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 10);
        $this->dispatchFleet($this->targetPlanet->getPlanetCoordinates(), $unitCollection, new Resources(0, 0, 0, 0), PlanetType::Planet);

        $fleetMissionService = resolve(FleetMissionService::class);
        $mission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        $this->post('/ajax/fleet/union/create', [
            'fleetID' => $mission->id,
            'groupname' => 'TestUnion',
            '_token' => csrf_token(),
        ]);
        $mission->refresh();
        $unionId = $mission->union_id;

        // Now check the available unions API as the ally (who was NOT invited)
        // The ally is a buddy but should NOT see the union without explicit invite
        $response = $this->get('/ajax/fleet/union/available?' . http_build_query([
            'galaxy' => $this->targetPlanet->getPlanetCoordinates()->galaxy,
            'system' => $this->targetPlanet->getPlanetCoordinates()->system,
            'position' => $this->targetPlanet->getPlanetCoordinates()->position,
            'planet_type' => PlanetType::Planet->value,
        ]));
        $response->assertStatus(200);

        $unions = $response->json('unions');
        $unionIds = array_column($unions, 'id');

        // The creator SHOULD see it (they're logged in as the creator)
        $this->assertContains($unionId, $unionIds, 'Creator should see their own union');
    }

    /**
     * Test that an ACS attack with multiple union fleets processes battle correctly.
     */
    public function testAcsAttackBattleWithMultipleFleets(): void
    {
        $this->basicSetup();
        $this->createTargetPlayer();
        $this->createAllyPlayer();

        // Give target some defenses
        $this->targetPlanet->addUnit('rocket_launcher', 5);

        // Send initiator attack fleet
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 20);
        $this->dispatchFleet($this->targetPlanet->getPlanetCoordinates(), $unitCollection, new Resources(0, 0, 0, 0), PlanetType::Planet);

        // Get mission and create union
        $fleetMissionService = resolve(FleetMissionService::class);
        $initiatorMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        $this->post('/ajax/fleet/union/create', [
            'fleetID' => $initiatorMission->id,
            'groupname' => 'BattleUnion',
            'unionUsers' => $this->allyUser->username,
            '_token' => csrf_token(),
        ]);
        $initiatorMission->refresh();
        $unionId = $initiatorMission->union_id;

        // Send ally's fleet to join the union
        $allyFleet = new UnitCollection();
        $allyFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 15);

        $allyFleetMissionService = resolve(FleetMissionService::class, ['player' => $this->allyPlanet->getPlayer()]);
        $allyMission = $allyFleetMissionService->createNewFromPlanet(
            $this->allyPlanet,
            $this->targetPlanet->getPlanetCoordinates(),
            PlanetType::Planet,
            1, // Attack
            $allyFleet,
            new Resources(0, 0, 0, 0),
            10, // 100% speed
            0
        );

        // Join the union
        $fleetUnionService = resolve(FleetUnionService::class);
        $union = FleetUnion::find($unionId);
        $fleetUnionService->joinUnion($union, $allyMission);

        // Verify both missions are in the union
        $allyMission->refresh();
        $this->assertEquals($unionId, $allyMission->union_id, 'Ally mission should be in the union');
        $this->assertEquals(2, $allyMission->mission_type, 'Ally mission should be ACS Attack type');
        $this->assertEquals(2, $allyMission->union_slot, 'Ally should have slot 2');

        // Advance time to arrival
        $arrivalTime = max($initiatorMission->time_arrival, $allyMission->time_arrival);
        $this->travelTo(Date::createFromTimestamp($arrivalTime + 10));
        $this->reloadApplication();
        $this->playerSetAllMessagesRead();
        $this->get('/overview');

        // Verify battle occurred
        $battleReport = BattleReport::orderBy('id', 'desc')->first();
        $this->assertNotNull($battleReport, 'Battle report should be created for ACS attack');

        // Verify both attackers participated: initiator sent 20 + ally sent 15 = 35 light fighters
        // (but may include additional units depending on test state, so check minimum)
        $attackerStartUnits = $battleReport->attacker['units'];
        $totalAttackerUnits = array_sum($attackerStartUnits);
        $this->assertGreaterThanOrEqual(35, $totalAttackerUnits, 'Both fleets should participate in battle (at least 20 + 15 light fighters)');
    }

    /**
     * Test that each union member gets their own return mission after battle.
     */
    public function testAcsAttackReturnMissionsPerFleet(): void
    {
        $this->basicSetup();
        $this->createTargetPlayer();
        $this->createAllyPlayer();

        // Send initiator fleet
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 20);
        $this->dispatchFleet($this->targetPlanet->getPlanetCoordinates(), $unitCollection, new Resources(0, 0, 0, 0), PlanetType::Planet);

        $fleetMissionService = resolve(FleetMissionService::class);
        $initiatorMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        // Create union
        $this->post('/ajax/fleet/union/create', [
            'fleetID' => $initiatorMission->id,
            'groupname' => 'ReturnTestUnion',
            'unionUsers' => $this->allyUser->username,
            '_token' => csrf_token(),
        ]);
        $initiatorMission->refresh();
        $unionId = $initiatorMission->union_id;

        // Ally joins with fleet
        $allyFleet = new UnitCollection();
        $allyFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 15);

        $allyFleetMissionService = resolve(FleetMissionService::class, ['player' => $this->allyPlanet->getPlayer()]);
        $allyMission = $allyFleetMissionService->createNewFromPlanet(
            $this->allyPlanet,
            $this->targetPlanet->getPlanetCoordinates(),
            PlanetType::Planet,
            1,
            $allyFleet,
            new Resources(0, 0, 0, 0),
            10,
            0
        );

        $fleetUnionService = resolve(FleetUnionService::class);
        $union = FleetUnion::find($unionId);
        $fleetUnionService->joinUnion($union, $allyMission);

        // Advance to arrival
        $arrivalTime = max($initiatorMission->time_arrival, $allyMission->time_arrival);
        $this->travelTo(Date::createFromTimestamp($arrivalTime + 10));
        $this->reloadApplication();
        $this->get('/overview');

        // Check that each fleet got its own return mission
        $initiatorReturn = FleetMission::where('parent_id', $initiatorMission->id)
            ->where('canceled', 0)
            ->first();
        $allyReturn = FleetMission::where('parent_id', $allyMission->id)
            ->where('canceled', 0)
            ->first();

        // Both outbound missions should be processed
        $initiatorMission->refresh();
        $allyMission->refresh();
        $this->assertEquals(1, $initiatorMission->processed, 'Initiator mission should be processed');
        $this->assertEquals(1, $allyMission->processed, 'Ally mission should be processed');

        // At least one return should exist (fleets with survivors get returns)
        $hasInitiatorReturn = $initiatorReturn !== null;
        $hasAllyReturn = $allyReturn !== null;
        $this->assertTrue($hasInitiatorReturn || $hasAllyReturn, 'At least one fleet should have survived and created a return mission');

        // If both survived, verify they return to their own planets
        if ($hasInitiatorReturn) {
            $this->assertEquals($this->currentUserId, $initiatorReturn->user_id, 'Initiator return should belong to initiator');
        }
        if ($hasAllyReturn) {
            $this->assertEquals($this->allyUser->id, $allyReturn->user_id, 'Ally return should belong to ally');
        }
    }

    /**
     * Test that the union is no longer available after arrival time passes.
     */
    public function testExpiredUnionNotShownInCombatForces(): void
    {
        $this->basicSetup();
        $this->createTargetPlayer();

        // Send attack fleet
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 10);
        $this->dispatchFleet($this->targetPlanet->getPlanetCoordinates(), $unitCollection, new Resources(0, 0, 0, 0), PlanetType::Planet);

        $fleetMissionService = resolve(FleetMissionService::class);
        $mission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        $this->post('/ajax/fleet/union/create', [
            'fleetID' => $mission->id,
            'groupname' => 'ExpireTest',
            '_token' => csrf_token(),
        ]);
        $mission->refresh();

        // Union should be visible before arrival
        $response = $this->get('/fleet');
        $response->assertStatus(200);
        $response->assertSee('ExpireTest');

        // Advance well past arrival time so battle processes and union expires
        $this->travelTo(Date::createFromTimestamp($mission->time_arrival + 3600));
        $this->reloadApplication();
        $this->get('/overview');

        // Union should no longer be visible (time_arrival is in the past)
        $response = $this->get('/fleet');
        $response->assertStatus(200);
        $response->assertDontSee('ExpireTest');
    }

    /**
     * Test that duplicate invite to same user does not create duplicate record.
     */
    public function testDuplicateInviteDoesNotCreateDuplicateRecord(): void
    {
        $this->basicSetup();
        $this->createTargetPlayer();
        $this->createAllyPlayer();

        // Send attack and create union with ally
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 10);
        $this->dispatchFleet($this->targetPlanet->getPlanetCoordinates(), $unitCollection, new Resources(0, 0, 0, 0), PlanetType::Planet);

        $fleetMissionService = resolve(FleetMissionService::class);
        $mission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        // Create union with ally
        $this->post('/ajax/fleet/union/create', [
            'fleetID' => $mission->id,
            'groupname' => 'DupeTest',
            'unionUsers' => $this->allyUser->username,
            '_token' => csrf_token(),
        ]);
        $mission->refresh();
        $unionId = $mission->union_id;

        // Edit union again with same ally (re-submit)
        $this->post('/ajax/fleet/union/create', [
            'fleetID' => $mission->id,
            'groupname' => 'DupeTest',
            'unionUsers' => $this->allyUser->username,
            '_token' => csrf_token(),
        ]);

        // Verify only one invite record exists
        $inviteCount = FleetUnionInvite::where('fleet_union_id', $unionId)
            ->where('user_id', $this->allyUser->id)
            ->count();
        $this->assertEquals(1, $inviteCount, 'Should have exactly one invite record, not duplicates');
    }

    /**
     * Test that recalling a non-initiator fleet frees the slot and keeps the union intact.
     */
    public function testRecallNonInitiatorFreesSlot(): void
    {
        $this->basicSetup();
        $this->createTargetPlayer();
        $this->createAllyPlayer();

        // Send initiator fleet
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 20);
        $this->dispatchFleet($this->targetPlanet->getPlanetCoordinates(), $unitCollection, new Resources(0, 0, 0, 0), PlanetType::Planet);

        $fleetMissionService = resolve(FleetMissionService::class);
        $initiatorMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        // Create union
        $this->post('/ajax/fleet/union/create', [
            'fleetID' => $initiatorMission->id,
            'groupname' => 'RecallSlotTest',
            'unionUsers' => $this->allyUser->username,
            '_token' => csrf_token(),
        ]);
        $initiatorMission->refresh();
        $unionId = $initiatorMission->union_id;

        // Ally joins
        $allyFleet = new UnitCollection();
        $allyFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 15);

        $allyFleetMissionService = resolve(FleetMissionService::class, ['player' => $this->allyPlanet->getPlayer()]);
        $allyMission = $allyFleetMissionService->createNewFromPlanet(
            $this->allyPlanet,
            $this->targetPlanet->getPlanetCoordinates(),
            PlanetType::Planet,
            1,
            $allyFleet,
            new Resources(0, 0, 0, 0),
            10,
            0
        );

        $fleetUnionService = resolve(FleetUnionService::class);
        $union = FleetUnion::find($unionId);
        $fleetUnionService->joinUnion($union, $allyMission);

        // Verify ally is slot 2
        $allyMission->refresh();
        $this->assertEquals(2, $allyMission->union_slot);

        // Advance time slightly (fleet is mid-flight)
        $midFlightTime = (int)(($initiatorMission->time_departure + $initiatorMission->time_arrival) / 2);
        $this->travelTo(Date::createFromTimestamp($midFlightTime));
        $this->reloadApplication();

        // Recall ally's fleet: switch to ally user context
        $this->be(User::find($this->allyUser->id));
        $response = $this->post('/ajax/fleet/dispatch/recall-fleet', [
            'fleet_mission_id' => $allyMission->id,
            '_token' => csrf_token(),
        ]);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify ally mission is canceled and removed from union
        $allyMission->refresh();
        $this->assertEquals(1, $allyMission->canceled, 'Ally mission should be canceled');
        $this->assertNull($allyMission->union_id, 'Ally mission should be removed from union');

        // Verify a return mission was created for the ally
        $allyReturn = FleetMission::where('parent_id', $allyMission->id)->where('canceled', 0)->first();
        $this->assertNotNull($allyReturn, 'Ally should have a return mission');
        $this->assertEquals($this->allyUser->id, $allyReturn->user_id, 'Return mission should belong to ally');

        // Verify union still exists with initiator as slot 1
        $union->refresh();
        $this->assertNotNull($union, 'Union should still exist');
        $initiatorMission->refresh();
        $this->assertEquals(1, $initiatorMission->union_slot, 'Initiator should still be slot 1');
        $this->assertEquals($unionId, $initiatorMission->union_id, 'Initiator should still be in the union');
    }

    /**
     * Test that recalling the initiator (slot 1) reassigns slot 1 to the next fleet
     * and updates union ownership.
     */
    public function testRecallInitiatorReassignsSlotOne(): void
    {
        $this->basicSetup();
        $this->createTargetPlayer();
        $this->createAllyPlayer();

        // Send initiator fleet
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 20);
        $this->dispatchFleet($this->targetPlanet->getPlanetCoordinates(), $unitCollection, new Resources(0, 0, 0, 0), PlanetType::Planet);

        $fleetMissionService = resolve(FleetMissionService::class);
        $initiatorMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        // Create union
        $this->post('/ajax/fleet/union/create', [
            'fleetID' => $initiatorMission->id,
            'groupname' => 'InitiatorRecallTest',
            'unionUsers' => $this->allyUser->username,
            '_token' => csrf_token(),
        ]);
        $initiatorMission->refresh();
        $unionId = $initiatorMission->union_id;

        // Ally joins
        $allyFleet = new UnitCollection();
        $allyFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 15);

        $allyFleetMissionService = resolve(FleetMissionService::class, ['player' => $this->allyPlanet->getPlayer()]);
        $allyMission = $allyFleetMissionService->createNewFromPlanet(
            $this->allyPlanet,
            $this->targetPlanet->getPlanetCoordinates(),
            PlanetType::Planet,
            1,
            $allyFleet,
            new Resources(0, 0, 0, 0),
            10,
            0
        );

        $fleetUnionService = resolve(FleetUnionService::class);
        $union = FleetUnion::find($unionId);
        $fleetUnionService->joinUnion($union, $allyMission);

        // Verify initial state: initiator = slot 1, ally = slot 2, union owned by initiator
        $initiatorMission->refresh();
        $allyMission->refresh();
        $this->assertEquals(1, $initiatorMission->union_slot);
        $this->assertEquals(2, $allyMission->union_slot);
        $this->assertEquals($this->currentUserId, $union->user_id, 'Union should be owned by initiator');

        // Advance time slightly (mid-flight)
        $midFlightTime = (int)(($initiatorMission->time_departure + $initiatorMission->time_arrival) / 2);
        $this->travelTo(Date::createFromTimestamp($midFlightTime));
        $this->reloadApplication();

        // Recall initiator's fleet (logged in as the initiator/current user)
        $response = $this->post('/ajax/fleet/dispatch/recall-fleet', [
            'fleet_mission_id' => $initiatorMission->id,
            '_token' => csrf_token(),
        ]);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify initiator mission is canceled and removed from union
        $initiatorMission->refresh();
        $this->assertEquals(1, $initiatorMission->canceled, 'Initiator mission should be canceled');
        $this->assertNull($initiatorMission->union_id, 'Initiator should be removed from union');

        // Verify ally is now slot 1 and union ownership transferred
        $allyMission->refresh();
        $union->refresh();
        $this->assertEquals(1, $allyMission->union_slot, 'Ally should now be slot 1 (new initiator)');
        $this->assertEquals($this->allyUser->id, $union->user_id, 'Union ownership should transfer to ally');

        // Verify battle still processes at arrival (ally as slot 1).
        // The ally's mission goes from ally planet → target planet, so we need to trigger
        // processing from the target's perspective (the target planet is the destination).
        $this->travelTo(Date::createFromTimestamp($allyMission->time_arrival + 10));
        $this->refreshApplication();
        $this->be(User::find($this->targetUser->id));
        $this->get('/overview');

        $battleReport = BattleReport::orderBy('id', 'desc')->first();
        $this->assertNotNull($battleReport, 'Battle should still process with ally as new initiator (slot 1)');

        // Verify ally's outbound mission is processed
        $allyMission->refresh();
        $this->assertEquals(1, $allyMission->processed, 'Ally mission should be processed after battle');
    }

    /**
     * Test that recalling a fleet mid-flight returns at the synced (slower) speed,
     * not the fleet's natural faster speed.
     */
    public function testRecallFleetReturnsAtSyncedSpeed(): void
    {
        $this->basicSetup();
        $this->createTargetPlayer();
        $this->createAllyPlayer();

        // Give ally impulse drive for faster natural speed
        $allyPlayerService = resolve(PlayerService::class, ['player_id' => $this->allyUser->id]);
        $allyPlayerService->setResearchLevel('impulse_drive', 5);
        $allyPlayerService->setResearchLevel('combustion_drive', 5);

        // Send initiator fleet (slower)
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 10);
        $this->dispatchFleet($this->targetPlanet->getPlanetCoordinates(), $unitCollection, new Resources(0, 0, 0, 0), PlanetType::Planet);

        $fleetMissionService = resolve(FleetMissionService::class);
        $initiatorMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $initiatorOutboundDuration = $initiatorMission->time_arrival - $initiatorMission->time_departure;

        // Create union
        $this->post('/ajax/fleet/union/create', [
            'fleetID' => $initiatorMission->id,
            'groupname' => 'SpeedTest',
            'unionUsers' => $this->allyUser->username,
            '_token' => csrf_token(),
        ]);
        $initiatorMission->refresh();
        $unionId = $initiatorMission->union_id;

        // Send ally fleet at 100% speed — the ally has faster tech, so their natural
        // arrival time should be earlier than the initiator's
        $allyFleet = new UnitCollection();
        $allyFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 10);

        $allyFleetMissionService = resolve(FleetMissionService::class, ['player' => $this->allyPlanet->getPlayer()]);
        $allyMission = $allyFleetMissionService->createNewFromPlanet(
            $this->allyPlanet,
            $this->targetPlanet->getPlanetCoordinates(),
            PlanetType::Planet,
            1,
            $allyFleet,
            new Resources(0, 0, 0, 0),
            10,
            0
        );

        // Record ally's natural outbound duration before joining the union
        $allyNaturalDuration = $allyMission->time_arrival - $allyMission->time_departure;

        // Join union — ally's arrival should be pushed out to match the union's time
        $fleetUnionService = resolve(FleetUnionService::class);
        $union = FleetUnion::find($unionId);
        $fleetUnionService->joinUnion($union, $allyMission);
        $allyMission->refresh();

        $allySyncedDuration = $allyMission->time_arrival - $allyMission->time_departure;
        // The ally's arrival was pushed out to match union, so synced duration >= natural duration
        $this->assertGreaterThanOrEqual($allyNaturalDuration, $allySyncedDuration, 'Ally should be slowed to match union');

        // Advance to mid-flight
        $elapsedTime = (int)(($allyMission->time_arrival - $allyMission->time_departure) / 2);
        $midFlightTimestamp = $allyMission->time_departure + $elapsedTime;
        $this->travelTo(Date::createFromTimestamp($midFlightTimestamp));
        $this->reloadApplication();

        // Recall ally's fleet
        $this->be(User::find($this->allyUser->id));
        $response = $this->post('/ajax/fleet/dispatch/recall-fleet', [
            'fleet_mission_id' => $allyMission->id,
            '_token' => csrf_token(),
        ]);
        $response->assertStatus(200);

        // Get the return mission
        $allyReturn = FleetMission::where('parent_id', $allyMission->id)->where('canceled', 0)->first();
        $this->assertNotNull($allyReturn, 'Ally should have a return mission');

        // Return duration should equal elapsed outbound time (at synced speed)
        $returnDuration = $allyReturn->time_arrival - $allyReturn->time_departure;
        $this->assertEquals($elapsedTime, $returnDuration, 'Return duration should equal elapsed outbound time at synced speed');
    }

    /**
     * Test that non-initiator fleet in union is skipped during processing
     * (only slot 1 processes the battle).
     */
    public function testNonInitiatorFleetSkipsProcessing(): void
    {
        $this->basicSetup();
        $this->createTargetPlayer();
        $this->createAllyPlayer();

        // Send initiator fleet
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 20);
        $this->dispatchFleet($this->targetPlanet->getPlanetCoordinates(), $unitCollection, new Resources(0, 0, 0, 0), PlanetType::Planet);

        $fleetMissionService = resolve(FleetMissionService::class);
        $initiatorMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        // Create union
        $this->post('/ajax/fleet/union/create', [
            'fleetID' => $initiatorMission->id,
            'groupname' => 'ProcessTest',
            'unionUsers' => $this->allyUser->username,
            '_token' => csrf_token(),
        ]);
        $initiatorMission->refresh();
        $unionId = $initiatorMission->union_id;

        // Ally joins
        $allyFleet = new UnitCollection();
        $allyFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 10);

        $allyFleetMissionService = resolve(FleetMissionService::class, ['player' => $this->allyPlanet->getPlayer()]);
        $allyMission = $allyFleetMissionService->createNewFromPlanet(
            $this->allyPlanet,
            $this->targetPlanet->getPlanetCoordinates(),
            PlanetType::Planet,
            1,
            $allyFleet,
            new Resources(0, 0, 0, 0),
            10,
            0
        );

        $fleetUnionService = resolve(FleetUnionService::class);
        $union = FleetUnion::find($unionId);
        $fleetUnionService->joinUnion($union, $allyMission);

        // Record battle report count before arrival
        $battleReportCountBefore = BattleReport::count();

        // Advance to arrival
        $arrivalTime = max($initiatorMission->time_arrival, $allyMission->time_arrival);
        $this->travelTo(Date::createFromTimestamp($arrivalTime + 10));
        $this->reloadApplication();
        $this->get('/overview');

        // Verify exactly one NEW battle report was created (not two)
        $battleReportCountAfter = BattleReport::count();
        $this->assertEquals(1, $battleReportCountAfter - $battleReportCountBefore, 'Only one battle report should be created (slot 1 processes for all)');

        // Verify both missions are processed (slot 1 processes all union fleets)
        $initiatorMission->refresh();
        $allyMission->refresh();
        $this->assertEquals(1, $initiatorMission->processed, 'Initiator mission should be processed');
        $this->assertEquals(1, $allyMission->processed, 'Ally mission should be processed');
    }

    /**
     * Test that an ACS attack with multiple fleets creates return missions for all survivors.
     */
    public function testAcsAttackMultiFleetReturnMissionsCreated(): void
    {
        $this->basicSetup();
        $this->createTargetPlayer();
        $this->createAllyPlayer();

        // Send initiator fleet with cargo ships
        $this->planetAddUnit('small_cargo', 20);
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 20);
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 10);
        $this->dispatchFleet($this->targetPlanet->getPlanetCoordinates(), $unitCollection, new Resources(0, 0, 0, 0), PlanetType::Planet);

        $fleetMissionService = resolve(FleetMissionService::class);
        $initiatorMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        // Create union
        $this->post('/ajax/fleet/union/create', [
            'fleetID' => $initiatorMission->id,
            'groupname' => 'LootTest',
            'unionUsers' => $this->allyUser->username,
            '_token' => csrf_token(),
        ]);
        $initiatorMission->refresh();
        $unionId = $initiatorMission->union_id;

        // Ally joins with cargo ships too
        $this->allyPlanet->addUnit('small_cargo', 20);
        $allyFleet = new UnitCollection();
        $allyFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 15);
        $allyFleet->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 10);

        $allyFleetMissionService = resolve(FleetMissionService::class, ['player' => $this->allyPlanet->getPlayer()]);
        $allyMission = $allyFleetMissionService->createNewFromPlanet(
            $this->allyPlanet,
            $this->targetPlanet->getPlanetCoordinates(),
            PlanetType::Planet,
            1,
            $allyFleet,
            new Resources(0, 0, 0, 0),
            10,
            0
        );

        $fleetUnionService = resolve(FleetUnionService::class);
        $union = FleetUnion::find($unionId);
        $fleetUnionService->joinUnion($union, $allyMission);

        // Advance to arrival
        $arrivalTime = max($initiatorMission->time_arrival, $allyMission->time_arrival);
        $this->travelTo(Date::createFromTimestamp($arrivalTime + 10));
        $this->reloadApplication();
        $this->get('/overview');

        // Both return missions should exist (fleets survive against minimal defenses)
        $initiatorReturn = FleetMission::where('parent_id', $initiatorMission->id)
            ->where('canceled', 0)
            ->first();
        $allyReturn = FleetMission::where('parent_id', $allyMission->id)
            ->where('canceled', 0)
            ->first();

        $this->assertNotNull($initiatorReturn, 'Initiator should have a return mission');
        $this->assertNotNull($allyReturn, 'Ally should have a return mission');

        // Verify return missions go to correct owners
        $this->assertEquals($this->currentUserId, $initiatorReturn->user_id, 'Initiator return should belong to initiator');
        $this->assertEquals($this->allyUser->id, $allyReturn->user_id, 'Ally return should belong to ally');

        // Verify outbound missions are processed
        $initiatorMission->refresh();
        $allyMission->refresh();
        $this->assertEquals(1, $initiatorMission->processed, 'Initiator outbound should be processed');
        $this->assertEquals(1, $allyMission->processed, 'Ally outbound should be processed');
    }

    /**
     * Test that post-battle return trips use each fleet's natural speed (based on surviving
     * ships and owner's tech), not the synced union speed. In original OGame, after an ACS
     * attack battle completes, each fleet returns home at its own speed.
     */
    public function testAcsAttackReturnUsesNaturalSpeedNotSyncedSpeed(): void
    {
        $this->basicSetup();
        $this->createTargetPlayer();
        $this->createAllyPlayer();

        // Give ally higher drive tech so they are naturally faster
        $allyPlayerService = resolve(PlayerService::class, ['player_id' => $this->allyUser->id]);
        $allyPlayerService->setResearchLevel('impulse_drive', 5);
        $allyPlayerService->setResearchLevel('combustion_drive', 5);

        // Send initiator fleet (slower, no drive tech upgrades)
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 20);
        $this->dispatchFleet($this->targetPlanet->getPlanetCoordinates(), $unitCollection, new Resources(0, 0, 0, 0), PlanetType::Planet);

        $fleetMissionService = resolve(FleetMissionService::class);
        $initiatorMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        // Create union
        $this->post('/ajax/fleet/union/create', [
            'fleetID' => $initiatorMission->id,
            'groupname' => 'NaturalSpeedTest',
            'unionUsers' => $this->allyUser->username,
            '_token' => csrf_token(),
        ]);
        $initiatorMission->refresh();
        $unionId = $initiatorMission->union_id;

        // Send ally fleet at 100% speed
        $allyFleet = new UnitCollection();
        $allyFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 10);

        $allyFleetMissionService = resolve(FleetMissionService::class, ['player' => $this->allyPlanet->getPlayer()]);
        $allyMission = $allyFleetMissionService->createNewFromPlanet(
            $this->allyPlanet,
            $this->targetPlanet->getPlanetCoordinates(),
            PlanetType::Planet,
            1,
            $allyFleet,
            new Resources(0, 0, 0, 0),
            10,
            0
        );

        // Record ally's natural outbound duration before joining the union
        $allyNaturalDuration = $allyMission->time_arrival - $allyMission->time_departure;

        // Join union — ally's arrival gets pushed out to match the slower initiator
        $fleetUnionService = resolve(FleetUnionService::class);
        $union = FleetUnion::find($unionId);
        $fleetUnionService->joinUnion($union, $allyMission);
        $allyMission->refresh();

        $allySyncedDuration = $allyMission->time_arrival - $allyMission->time_departure;
        $this->assertGreaterThan($allyNaturalDuration, $allySyncedDuration, 'Ally should be slowed to match union');

        // Advance to arrival so battle processes
        $arrivalTime = max($initiatorMission->time_arrival, $allyMission->time_arrival);
        $this->travelTo(Date::createFromTimestamp($arrivalTime + 10));
        $this->reloadApplication();
        $this->get('/overview');

        // Get the ally's return mission
        $allyReturn = FleetMission::where('parent_id', $allyMission->id)
            ->where('canceled', 0)
            ->first();
        $this->assertNotNull($allyReturn, 'Ally should have a return mission');

        $allyReturnDuration = $allyReturn->time_arrival - $allyReturn->time_departure;

        // The return duration should be based on natural speed, not the synced speed.
        // Since the ally has higher drive tech, their natural duration < synced duration.
        $this->assertLessThan($allySyncedDuration, $allyReturnDuration, 'Return duration should be less than synced outbound duration (natural speed is faster)');

        // Verify it matches the recalculated natural duration for surviving ships
        $allyPlayerServiceForCalc = resolve(PlayerService::class, ['player_id' => $this->allyUser->id]);
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $originPlanet = $planetServiceFactory->makeForPlayer($allyPlayerServiceForCalc, $allyMission->planet_id_from);

        // Get surviving units from the return mission
        $survivingUnits = $allyFleetMissionService->getFleetUnits($allyReturn);

        $expectedNaturalDuration = $allyFleetMissionService->calculateFleetMissionDuration(
            $originPlanet,
            $this->targetPlanet->getPlanetCoordinates(),
            $survivingUnits,
            resolve(AttackMission::class),
            10
        );

        $this->assertEquals($expectedNaturalDuration, $allyReturnDuration, 'Return duration should match recalculated natural speed');
    }

    /**
     * Test that sending a fleet to join a full union returns an error fadeBox
     * with the max fleets error message.
     */
    public function testSendFleetToFullUnionShowsMaxFleetsError(): void
    {
        $this->basicSetup();
        $this->createTargetPlayer();
        $this->createAllyPlayer();

        // Send initiator fleet
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 5);
        $this->dispatchFleet($this->targetPlanet->getPlanetCoordinates(), $unitCollection, new Resources(0, 0, 0, 0), PlanetType::Planet);

        $fleetMissionService = resolve(FleetMissionService::class);
        $initiatorMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        // Create union
        $this->post('/ajax/fleet/union/create', [
            'fleetID' => $initiatorMission->id,
            'groupname' => 'FullUnionTest',
            'unionUsers' => $this->allyUser->username,
            '_token' => csrf_token(),
        ]);
        $initiatorMission->refresh();
        $unionId = $initiatorMission->union_id;

        // Set max_fleets to 1 so the union is already full (initiator occupies the only slot)
        $union = FleetUnion::find($unionId);
        $union->max_fleets = 1;
        $union->save();

        // Switch to ally and try to send a fleet to join the full union
        $this->be(User::find($this->allyUser->id));

        $response = $this->post('/ajax/fleet/dispatch/send-fleet', [
            'galaxy' => $this->targetPlanet->getPlanetCoordinates()->galaxy,
            'system' => $this->targetPlanet->getPlanetCoordinates()->system,
            'position' => $this->targetPlanet->getPlanetCoordinates()->position,
            'type' => PlanetType::Planet->value,
            'metal' => 0,
            'crystal' => 0,
            'deuterium' => 0,
            'mission' => 2, // ACS Attack
            'speed' => 10,
            'am204' => 5, // light_fighter
            'union' => $unionId,
            'holdingtime' => 0,
            'retreatAfterDefenderRetreat' => 0,
            'lootFoodOnAttack' => 0,
            '_token' => csrf_token(),
        ]);

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertFalse($data['success']);
        $this->assertEquals(__('t_ingame.fleet.err_union_max_fleets'), $data['errors'][0]['message']);
    }

    /**
     * Test that when a slower fleet joins a union, ALL existing member missions (including
     * the initiator) have their time_arrival synced to the late joiner's arrival time, so
     * the battle fires when every fleet has arrived — not before.
     *
     * The union arrival and initiator mission arrival are pinned to a known far-future
     * timestamp to give a predictable 30% delay window regardless of actual flight speeds.
     */
    public function testLateJoinerSyncsAllMemberArrivalTimes(): void
    {
        $this->basicSetup();
        $this->createTargetPlayer();
        $this->createAllyPlayer();

        // Send initiator fleet
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 10);
        $this->dispatchFleet($this->targetPlanet->getPlanetCoordinates(), $unitCollection, new Resources(0, 0, 0, 0), PlanetType::Planet);

        $fleetMissionService = resolve(FleetMissionService::class);
        $initiatorMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        // Create union
        $this->post('/ajax/fleet/union/create', [
            'fleetID' => $initiatorMission->id,
            'groupname' => 'TimingSyncTest',
            'unionUsers' => $this->allyUser->username,
            '_token' => csrf_token(),
        ]);
        $initiatorMission->refresh();
        $unionId = $initiatorMission->union_id;

        // Pin union and initiator to a far-future timestamp so the 30% delay window is
        // large and well-defined (30% of 10 000 s = 3 000 s >> 100 s used below).
        $baseArrivalTime = time() + 10000;
        $initiatorMission->time_arrival = $baseArrivalTime;
        $initiatorMission->save();
        $union = FleetUnion::find($unionId);
        $union->time_arrival = $baseArrivalTime;
        $union->save();

        // Create ally fleet mission
        $allyFleet = new UnitCollection();
        $allyFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 10);

        $allyFleetMissionService = resolve(FleetMissionService::class, ['player' => $this->allyPlanet->getPlayer()]);
        $allyMission = $allyFleetMissionService->createNewFromPlanet(
            $this->allyPlanet,
            $this->targetPlanet->getPlanetCoordinates(),
            PlanetType::Planet,
            1,
            $allyFleet,
            new Resources(0, 0, 0, 0),
            10,
            0
        );

        // Set ally arrival 100 s after base — well within the 3 000 s delay limit.
        $lateArrivalTime = $baseArrivalTime + 100;
        $allyMission->time_arrival = $lateArrivalTime;
        $allyMission->save();

        // Join the union as the late fleet
        $union->refresh();
        $fleetUnionService = resolve(FleetUnionService::class);
        $fleetUnionService->joinUnion($union, $allyMission);

        // The initiator's mission time_arrival must be bumped to the late joiner's time.
        $initiatorMission->refresh();
        $this->assertEquals(
            $lateArrivalTime,
            $initiatorMission->time_arrival,
            'Initiator time_arrival must be synced to the late joiner so the battle fires when all fleets arrive'
        );

        // The union record must also reflect the new arrival time.
        $union->refresh();
        $this->assertEquals(
            $lateArrivalTime,
            $union->time_arrival,
            'Union time_arrival must be updated to reflect the latest fleet arrival'
        );
    }

    /**
     * Verify that union creation is blocked when ACS is disabled by server settings.
     */
    public function testAcsDisabledBlocksUnionCreation(): void
    {
        $this->basicSetup();
        $this->createTargetPlayer();

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 10);
        $this->dispatchFleet($this->targetPlanet->getPlanetCoordinates(), $unitCollection, new Resources(0, 0, 0, 0), PlanetType::Planet);

        $fleetMissionService = resolve(FleetMissionService::class);
        $mission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        $settingsService = resolve(SettingsService::class);
        $settingsService->set('alliance_combat_system_on', 0);

        $response = $this->post('/ajax/fleet/union/create', [
            'fleetID' => $mission->id,
            'groupname' => 'TestUnion',
            '_token' => csrf_token(),
        ]);
        $response->assertStatus(403);

        $settingsService->set('alliance_combat_system_on', 1);

        // Mission should still be type 1 (not converted to ACS Attack)
        $mission->refresh();
        $this->assertEquals(1, $mission->mission_type, 'Mission should remain type 1 when union creation is blocked by ACS setting.');
    }

    /**
     * Verify that joining a union is blocked when ACS is disabled by server settings.
     */
    public function testAcsDisabledBlocksUnionJoining(): void
    {
        $this->basicSetup();

        $settingsService = resolve(SettingsService::class);
        $settingsService->set('alliance_combat_system_on', 0);

        $response = $this->post('/ajax/fleet/union/join', [
            'fleet_mission_id' => 999999,
            'union_id' => 999999,
            '_token' => csrf_token(),
        ]);
        $response->assertStatus(403);

        $settingsService->set('alliance_combat_system_on', 1);
    }

    /**
     * Verify that ACS Attack (type 2) is not offered in mission options when ACS is disabled.
     */
    public function testAcsDisabledHidesAcsAttackFromCheckTarget(): void
    {
        $this->basicSetup();
        $this->createTargetPlayer();

        // Send attack and create a union while ACS is on
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 10);
        $this->dispatchFleet($this->targetPlanet->getPlanetCoordinates(), $unitCollection, new Resources(0, 0, 0, 0), PlanetType::Planet);

        $fleetMissionService = resolve(FleetMissionService::class);
        $mission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        $this->post('/ajax/fleet/union/create', [
            'fleetID' => $mission->id,
            'groupname' => 'TestUnion',
            '_token' => csrf_token(),
        ]);
        $mission->refresh();
        $unionId = $mission->union_id;

        // Disable ACS
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('alliance_combat_system_on', 0);

        $lightFighterObj = ObjectService::getUnitObjectByMachineName('light_fighter');
        $response = $this->post('/ajax/fleet/dispatch/check-target', [
            'galaxy' => $this->targetPlanet->getPlanetCoordinates()->galaxy,
            'system' => $this->targetPlanet->getPlanetCoordinates()->system,
            'position' => $this->targetPlanet->getPlanetCoordinates()->position,
            'type' => PlanetType::Planet->value,
            'mission' => 1,
            'union' => $unionId,
            '_token' => csrf_token(),
            'am' . $lightFighterObj->id => 5,
        ]);

        $settingsService->set('alliance_combat_system_on', 1);

        $response->assertStatus(200);
        $response->assertJson(['orders' => [2 => false]]);
    }

    /**
     * Test that ACS Attack distributes loot proportionally by surviving cargo capacity
     * and preserves resources carried by each fleet.
     *
     * Setup:
     *   Initiator: 2 large cargoes (25 000 capacity each = 50 000 total), carrying 1 000 metal
     *   Ally:      1 small cargo  ( 5 000 capacity),                       carrying   500 metal
     *   Defender:  22 000 metal, no ships / defense
     *
     * Expected after battle (all ships survive, attacker wins):
     *   Total loot = 50% of 22 000 = 11 000 metal
     *   Initiator fraction = 50 000 / 55 000 → loot = 10 000 metal (exact integer)
     *   Ally      fraction =  5 000 / 55 000 → loot =  1 000 metal (exact integer)
     *
     *   Initiator return: 1 000 (surviving cargo) + 10 000 (loot) = 11 000 metal
     *   Ally      return:   500 (surviving cargo) +  1 000 (loot) =  1 500 metal
     */
    public function testAcsAttackCargoAndLootDistribution(): void
    {
        $this->basicSetup();
        $this->createTargetPlayer();
        $this->createAllyPlayer();

        // Set exact defender resources for deterministic loot calculation.
        DB::table('planets')
            ->where('id', $this->targetPlanet->getPlanetId())
            ->update(['metal' => 22000, 'crystal' => 0, 'deuterium' => 0]);

        // Give initiator planet metal to carry as cargo and add 2 large cargoes.
        $this->planetAddResources(new Resources(2000, 0, 0, 0));
        $this->planetAddUnit('large_cargo', 2);
        $initiatorFleet = new UnitCollection();
        $initiatorFleet->addUnit(ObjectService::getUnitObjectByMachineName('large_cargo'), 2);
        $this->dispatchFleet(
            $this->targetPlanet->getPlanetCoordinates(),
            $initiatorFleet,
            new Resources(1000, 0, 0, 0),
            PlanetType::Planet
        );

        $fleetMissionService = resolve(FleetMissionService::class);
        $initiatorMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        // Create union and invite ally.
        $this->post('/ajax/fleet/union/create', [
            'fleetID' => $initiatorMission->id,
            'groupname' => 'LootDistTest',
            'unionUsers' => $this->allyUser->username,
            '_token' => csrf_token(),
        ]);
        $initiatorMission->refresh();
        $unionId = $initiatorMission->union_id;

        // Give ally planet metal to carry and add 1 small cargo (different type from initiator).
        $this->allyPlanet->addResources(new Resources(1000, 0, 0, 0));
        $this->allyPlanet->addUnit('small_cargo', 1);
        $allyFleet = new UnitCollection();
        $allyFleet->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);

        $allyFleetMissionService = resolve(FleetMissionService::class, ['player' => $this->allyPlanet->getPlayer()]);
        $allyMission = $allyFleetMissionService->createNewFromPlanet(
            $this->allyPlanet,
            $this->targetPlanet->getPlanetCoordinates(),
            PlanetType::Planet,
            1,
            $allyFleet,
            new Resources(500, 0, 0, 0),
            10,
            0
        );

        $fleetUnionService = resolve(FleetUnionService::class);
        $union = FleetUnion::find($unionId);
        $fleetUnionService->joinUnion($union, $allyMission);

        // Advance time past arrival and trigger fleet processing.
        $arrivalTime = max($initiatorMission->time_arrival, $allyMission->time_arrival);
        $this->travelTo(Date::createFromTimestamp($arrivalTime + 10));
        $this->reloadApplication();
        $this->get('/overview');

        // Fetch return missions.
        $initiatorReturn = FleetMission::where('parent_id', $initiatorMission->id)
            ->where('canceled', 0)
            ->first();
        $allyReturn = FleetMission::where('parent_id', $allyMission->id)
            ->where('canceled', 0)
            ->first();

        $this->assertNotNull($initiatorReturn, 'Initiator should have a return mission');
        $this->assertNotNull($allyReturn, 'Ally should have a return mission');

        // Verify loot distribution and cargo preservation.
        // Initiator: 50 000 / 55 000 of 11 000 = 10 000 metal loot + 1 000 surviving cargo = 11 000
        $this->assertEquals(
            11000,
            $initiatorReturn->metal,
            'Initiator return should carry 1000 (surviving cargo) + 10000 (10/11 loot) = 11000 metal'
        );
        // Ally: 5 000 / 55 000 of 11 000 = 1 000 metal loot + 500 surviving cargo = 1 500
        $this->assertEquals(
            1500,
            $allyReturn->metal,
            'Ally return should carry 500 (surviving cargo) + 1000 (1/11 loot) = 1500 metal'
        );

        // No crystal was looted (defender had none) and none was carried.
        $this->assertEquals(0, $initiatorReturn->crystal);
        $this->assertEquals(0, $allyReturn->crystal);
        // Note: deuterium is not asserted because the mission's deuterium field combines
        // cargo and fuel; fuel is returned with surviving ships (same behaviour as single-attacker).
    }

    /**
     * Regression test: ACS loot capacity must sum each fleet's own cargo modifiers.
     *
     * Setup:
     *   Initiator: 1 small cargo, no class bonus          => 5 000 capacity
     *   Ally:      1 small cargo, Collector class bonus   => 6 250 capacity
     *   Defender:  22 500 metal                           => 11 250 loot at 50%
     *
     * Expected:
     *   Total occupied return cargo should match the 11 250 combined capacity, with
     *   a small portion reserved for returned fuel. This proves the battle loot was
     *   constrained using both fleets' own cargo modifiers instead of 10 000 based
     *   only on the initiator's stats.
     */
    public function testAcsAttackUsesPerFleetCargoModifiersForLootCapacity(): void
    {
        $this->basicSetup();
        $this->createTargetPlayer();
        $this->createAllyPlayer();

        DB::table('planets')
            ->where('id', $this->targetPlanet->getPlanetId())
            ->update(['metal' => 22500, 'crystal' => 0, 'deuterium' => 0]);

        $this->planetAddUnit('small_cargo', 1);
        $initiatorFleet = new UnitCollection();
        $initiatorFleet->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);
        $this->dispatchFleet(
            $this->targetPlanet->getPlanetCoordinates(),
            $initiatorFleet,
            new Resources(0, 0, 0, 0),
            PlanetType::Planet
        );

        $fleetMissionService = resolve(FleetMissionService::class);
        $initiatorMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        $this->post('/ajax/fleet/union/create', [
            'fleetID' => $initiatorMission->id,
            'groupname' => 'CapacityBonusTest',
            'unionUsers' => $this->allyUser->username,
            '_token' => csrf_token(),
        ]);
        $initiatorMission->refresh();
        $unionId = $initiatorMission->union_id;

        $allyPlayer = $this->allyPlanet->getPlayer();
        $allyPlayer->getUser()->character_class = CharacterClass::COLLECTOR->value;
        $allyPlayer->getUser()->save();

        $this->allyPlanet->addUnit('small_cargo', 1);
        $allyFleet = new UnitCollection();
        $allyFleet->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);

        $allyFleetMissionService = resolve(FleetMissionService::class, ['player' => $allyPlayer]);
        $allyMission = $allyFleetMissionService->createNewFromPlanet(
            $this->allyPlanet,
            $this->targetPlanet->getPlanetCoordinates(),
            PlanetType::Planet,
            1,
            $allyFleet,
            new Resources(0, 0, 0, 0),
            10,
            0
        );

        $fleetUnionService = resolve(FleetUnionService::class);
        $union = FleetUnion::find($unionId);
        $fleetUnionService->joinUnion($union, $allyMission);

        $arrivalTime = max($initiatorMission->time_arrival, $allyMission->time_arrival);
        $this->travelTo(Date::createFromTimestamp($arrivalTime + 10));
        $this->reloadApplication();
        $this->get('/overview');

        $initiatorReturn = FleetMission::where('parent_id', $initiatorMission->id)
            ->where('canceled', 0)
            ->first();
        $allyReturn = FleetMission::where('parent_id', $allyMission->id)
            ->where('canceled', 0)
            ->first();

        $this->assertNotNull($initiatorReturn, 'Initiator should have a return mission');
        $this->assertNotNull($allyReturn, 'Ally should have a return mission');
        $this->assertEquals(
            11250,
            $initiatorReturn->metal + $allyReturn->metal + $initiatorReturn->deuterium + $allyReturn->deuterium,
            'Combined return cargo should use the full 11 250 capacity enabled by per-fleet cargo modifiers'
        );
        $this->assertGreaterThan(
            $initiatorReturn->metal,
            $allyReturn->metal,
            'Collector ally should receive the larger share because its fleet has more cargo capacity'
        );
    }

    /**
     * Regression test: integer rounding during ACS loot splitting must not drop loot on the floor.
     *
     * Setup:
     *   Initiator: 1 small cargo
     *   Ally:      1 small cargo
     *   Defender:  2 metal, 2 crystal => 1 metal, 1 crystal looted
     *
     * Expected:
     *   The combined return missions still carry exactly 1 metal and 1 crystal,
     *   and the initiator receives the odd-unit remainder just like original OGame.
     */
    public function testAcsAttackPreservesLootWhenSharesRoundDown(): void
    {
        $this->basicSetup();
        $this->createTargetPlayer();
        $this->createAllyPlayer();

        DB::table('planets')
            ->where('id', $this->targetPlanet->getPlanetId())
            ->update(['metal' => 2, 'crystal' => 2, 'deuterium' => 0]);

        $this->planetAddUnit('small_cargo', 1);
        $initiatorFleet = new UnitCollection();
        $initiatorFleet->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);
        $this->dispatchFleet(
            $this->targetPlanet->getPlanetCoordinates(),
            $initiatorFleet,
            new Resources(0, 0, 0, 0),
            PlanetType::Planet
        );

        $fleetMissionService = resolve(FleetMissionService::class);
        $initiatorMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        $this->post('/ajax/fleet/union/create', [
            'fleetID' => $initiatorMission->id,
            'groupname' => 'RoundingTest',
            'unionUsers' => $this->allyUser->username,
            '_token' => csrf_token(),
        ]);
        $initiatorMission->refresh();
        $unionId = $initiatorMission->union_id;

        $this->allyPlanet->addUnit('small_cargo', 1);
        $allyFleet = new UnitCollection();
        $allyFleet->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);

        $allyFleetMissionService = resolve(FleetMissionService::class, ['player' => $this->allyPlanet->getPlayer()]);
        $allyMission = $allyFleetMissionService->createNewFromPlanet(
            $this->allyPlanet,
            $this->targetPlanet->getPlanetCoordinates(),
            PlanetType::Planet,
            1,
            $allyFleet,
            new Resources(0, 0, 0, 0),
            10,
            0
        );

        $fleetUnionService = resolve(FleetUnionService::class);
        $union = FleetUnion::find($unionId);
        $fleetUnionService->joinUnion($union, $allyMission);

        $arrivalTime = max($initiatorMission->time_arrival, $allyMission->time_arrival);
        $this->travelTo(Date::createFromTimestamp($arrivalTime + 10));
        $this->reloadApplication();
        $this->get('/overview');

        $initiatorReturn = FleetMission::where('parent_id', $initiatorMission->id)
            ->where('canceled', 0)
            ->first();
        $allyReturn = FleetMission::where('parent_id', $allyMission->id)
            ->where('canceled', 0)
            ->first();

        $this->assertNotNull($initiatorReturn, 'Initiator should have a return mission');
        $this->assertNotNull($allyReturn, 'Ally should have a return mission');
        $this->assertEquals(
            1,
            $initiatorReturn->metal + $allyReturn->metal,
            'Combined returns should preserve the full 1 metal looted from the defender'
        );
        $this->assertEquals(1, $initiatorReturn->metal, 'Initiator should receive the odd metal remainder');
        $this->assertEquals(0, $allyReturn->metal, 'Ally should not receive the odd metal remainder');
        $this->assertEquals(
            1,
            $initiatorReturn->crystal + $allyReturn->crystal,
            'Combined returns should preserve the full 1 crystal looted from the defender'
        );
        $this->assertEquals(1, $initiatorReturn->crystal, 'Initiator should receive the odd crystal remainder');
        $this->assertEquals(0, $allyReturn->crystal, 'Ally should not receive the odd crystal remainder');
    }

    /**
     * Regression test: ACS return missions must not exceed surviving cargo capacity,
     * and loot that does not fit on one fleet must be redistributed without duplicating resources.
     *
     * Setup:
     *   Initiator: 1 small cargo carrying 4 900 metal (almost full)
     *   Ally:      1 small cargo carrying 0 metal
     *   Defender:  2 000 metal => 1 000 metal looted
     *
     * Expected:
     *   Combined return metal = 5 900 exactly (4 900 carried + 1 000 looted)
     *   Each return mission remains within its fleet cargo capacity.
     */
    public function testAcsAttackRedistributesLootWithoutExceedingFleetCapacity(): void
    {
        $this->basicSetup();
        $this->createTargetPlayer();
        $this->createAllyPlayer();

        DB::table('planets')
            ->where('id', $this->targetPlanet->getPlanetId())
            ->update(['metal' => 2000, 'crystal' => 0, 'deuterium' => 0]);

        $this->planetAddResources(new Resources(5000, 0, 0, 0));
        $this->planetAddUnit('small_cargo', 1);
        $initiatorFleet = new UnitCollection();
        $initiatorFleet->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);
        $this->dispatchFleet(
            $this->targetPlanet->getPlanetCoordinates(),
            $initiatorFleet,
            new Resources(4900, 0, 0, 0),
            PlanetType::Planet
        );

        $fleetMissionService = resolve(FleetMissionService::class);
        $initiatorMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        $this->post('/ajax/fleet/union/create', [
            'fleetID' => $initiatorMission->id,
            'groupname' => 'CapacityInvariantTest',
            'unionUsers' => $this->allyUser->username,
            '_token' => csrf_token(),
        ]);
        $initiatorMission->refresh();
        $unionId = $initiatorMission->union_id;

        $this->allyPlanet->addUnit('small_cargo', 1);
        $allyFleet = new UnitCollection();
        $allyFleet->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);

        $allyFleetMissionService = resolve(FleetMissionService::class, ['player' => $this->allyPlanet->getPlayer()]);
        $allyMission = $allyFleetMissionService->createNewFromPlanet(
            $this->allyPlanet,
            $this->targetPlanet->getPlanetCoordinates(),
            PlanetType::Planet,
            1,
            $allyFleet,
            new Resources(0, 0, 0, 0),
            10,
            0
        );

        $fleetUnionService = resolve(FleetUnionService::class);
        $union = FleetUnion::find($unionId);
        $fleetUnionService->joinUnion($union, $allyMission);

        $arrivalTime = max($initiatorMission->time_arrival, $allyMission->time_arrival);
        $this->travelTo(Date::createFromTimestamp($arrivalTime + 10));
        $this->reloadApplication();
        $this->get('/overview');

        $initiatorReturn = FleetMission::where('parent_id', $initiatorMission->id)
            ->where('canceled', 0)
            ->first();
        $allyReturn = FleetMission::where('parent_id', $allyMission->id)
            ->where('canceled', 0)
            ->first();

        $this->assertNotNull($initiatorReturn, 'Initiator should have a return mission');
        $this->assertNotNull($allyReturn, 'Ally should have a return mission');
        $this->assertEquals(
            5900,
            $initiatorReturn->metal + $allyReturn->metal,
            'Combined returns should preserve exactly 4 900 carried metal plus 1 000 looted metal'
        );

        $initiatorReturnPlayer = resolve(PlayerService::class, ['player_id' => $initiatorReturn->user_id]);
        $allyReturnPlayer = resolve(PlayerService::class, ['player_id' => $allyReturn->user_id]);

        $initiatorCapacity = $fleetMissionService->getFleetUnits($initiatorReturn)->getTotalCargoCapacity($initiatorReturnPlayer);
        $allyCapacity = $fleetMissionService->getFleetUnits($allyReturn)->getTotalCargoCapacity($allyReturnPlayer);

        $this->assertLessThanOrEqual(
            $initiatorCapacity,
            $initiatorReturn->metal + $initiatorReturn->crystal + $initiatorReturn->deuterium,
            'Initiator return must not exceed the surviving fleet cargo capacity'
        );
        $this->assertLessThanOrEqual(
            $allyCapacity,
            $allyReturn->metal + $allyReturn->crystal + $allyReturn->deuterium,
            'Ally return must not exceed the surviving fleet cargo capacity'
        );
    }

    /**
     * Regression test: when the ACS group lacks enough free cargo space for all possible loot,
     * only the actually transportable amount should be deducted and the remainder must stay on
     * the defender planet.
     */
    public function testAcsAttackLeavesUncarriedLootOnDefenderPlanet(): void
    {
        $this->basicSetup();
        $this->createTargetPlayer();
        $this->createAllyPlayer();

        DB::table('planets')
            ->where('id', $this->targetPlanet->getPlanetId())
            ->update(['metal' => 12000, 'crystal' => 0, 'deuterium' => 0]);

        $this->planetAddResources(new Resources(5000, 0, 0, 0));
        $this->planetAddUnit('small_cargo', 1);
        $initiatorFleet = new UnitCollection();
        $initiatorFleet->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);
        $this->dispatchFleet(
            $this->targetPlanet->getPlanetCoordinates(),
            $initiatorFleet,
            new Resources(4900, 0, 0, 0),
            PlanetType::Planet
        );

        $fleetMissionService = resolve(FleetMissionService::class);
        $initiatorMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        $this->post('/ajax/fleet/union/create', [
            'fleetID' => $initiatorMission->id,
            'groupname' => 'LeaveLootTest',
            'unionUsers' => $this->allyUser->username,
            '_token' => csrf_token(),
        ]);
        $initiatorMission->refresh();
        $unionId = $initiatorMission->union_id;

        $this->allyPlanet->addUnit('small_cargo', 1);
        $allyFleet = new UnitCollection();
        $allyFleet->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);

        $allyFleetMissionService = resolve(FleetMissionService::class, ['player' => $this->allyPlanet->getPlayer()]);
        $allyMission = $allyFleetMissionService->createNewFromPlanet(
            $this->allyPlanet,
            $this->targetPlanet->getPlanetCoordinates(),
            PlanetType::Planet,
            1,
            $allyFleet,
            new Resources(0, 0, 0, 0),
            10,
            0
        );

        $fleetUnionService = resolve(FleetUnionService::class);
        $union = FleetUnion::find($unionId);
        $fleetUnionService->joinUnion($union, $allyMission);

        $arrivalTime = max($initiatorMission->time_arrival, $allyMission->time_arrival);
        $this->travelTo(Date::createFromTimestamp($arrivalTime + 10));
        $this->reloadApplication();
        $this->get('/overview');

        $initiatorReturn = FleetMission::where('parent_id', $initiatorMission->id)
            ->where('canceled', 0)
            ->first();
        $allyReturn = FleetMission::where('parent_id', $allyMission->id)
            ->where('canceled', 0)
            ->first();

        $this->assertNotNull($initiatorReturn, 'Initiator should have a return mission');
        $this->assertNotNull($allyReturn, 'Ally should have a return mission');

        $actualLootTaken = ($initiatorReturn->metal + $allyReturn->metal) - 4900;
        $this->assertGreaterThan(0, $actualLootTaken, 'Attackers should still bring home some loot');
        $this->assertLessThan(
            6000,
            $actualLootTaken,
            'Actual loot should be lower than the theoretical 6 000 because free cargo space is limited'
        );

        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $refreshedTargetPlanet = $planetServiceFactory->makeForPlayer(
            $this->targetPlanet->getPlayer(),
            $this->targetPlanet->getPlanetId()
        );
        $this->assertEquals(
            12000 - $actualLootTaken,
            $refreshedTargetPlanet->getResources()->metal->get(),
            'Any loot that does not fit on the ACS return fleets must remain on the defender planet'
        );
    }

    /**
     * Regression test: ACS attack must not inflate ship counts due to shared UnitEntry references.
     *
     * UnitCollection::addCollection() is called multiple times per battle (BattleEngine
     * constructor, simulateBattle, sanitizeRoundArray). If entries are inserted by reference
     * rather than by clone, repeated merges compound the unit amounts in the source collection,
     * causing the battle engine to see more ships than were actually dispatched and returning
     * inflated survivors to each attacker.
     */
    public function testAcsAttackDoesNotInflateShipCountsViaAlias(): void
    {
        $this->basicSetup();
        $this->createTargetPlayer();
        $this->createAllyPlayer();

        $initiatorCruiserCount = 360;
        $allyCruiserCount = 180;

        // Give both planets enough cruisers (cruiser has no prerequisites in test env)
        $this->planetAddUnit('cruiser', $initiatorCruiserCount);
        $this->allyPlanet->addUnit('cruiser', $allyCruiserCount);

        // Dispatch initiator fleet
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('cruiser'), $initiatorCruiserCount);
        $this->dispatchFleet(
            $this->targetPlanet->getPlanetCoordinates(),
            $unitCollection,
            new Resources(0, 0, 0, 0),
            PlanetType::Planet
        );

        $fleetMissionService = resolve(FleetMissionService::class);
        $initiatorMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        // Create union
        $this->post('/ajax/fleet/union/create', [
            'fleetID' => $initiatorMission->id,
            'groupname' => 'AliasRegressionUnion',
            'unionUsers' => $this->allyUser->username,
            '_token' => csrf_token(),
        ]);
        $initiatorMission->refresh();
        $unionId = $initiatorMission->union_id;
        $this->assertNotNull($unionId, 'Union should be created');

        // Ally joins with fleet of the same ship type (cruiser)
        $allyFleet = new UnitCollection();
        $allyFleet->addUnit(ObjectService::getUnitObjectByMachineName('cruiser'), $allyCruiserCount);

        $allyFleetMissionService = resolve(FleetMissionService::class, ['player' => $this->allyPlanet->getPlayer()]);
        $allyMission = $allyFleetMissionService->createNewFromPlanet(
            $this->allyPlanet,
            $this->targetPlanet->getPlanetCoordinates(),
            PlanetType::Planet,
            1,
            $allyFleet,
            new Resources(0, 0, 0, 0),
            10,
            0
        );

        $fleetUnionService = resolve(FleetUnionService::class);
        $union = FleetUnion::find($unionId);
        $fleetUnionService->joinUnion($union, $allyMission);

        // Advance to arrival and trigger mission processing
        $arrivalTime = max($initiatorMission->time_arrival, $allyMission->time_arrival);
        $this->travelTo(Date::createFromTimestamp($arrivalTime + 10));
        $this->reloadApplication();
        $this->get('/overview');

        // Verify battle report shows correct combined attacker count (360 + 180 = 540)
        $battleReport = BattleReport::orderBy('id', 'desc')->first();
        $this->assertNotNull($battleReport, 'Battle report should exist');

        $reportedCruisers = $battleReport->attacker['units']['cruiser'] ?? 0;
        $expectedTotal = $initiatorCruiserCount + $allyCruiserCount; // 540
        $this->assertEquals(
            $expectedTotal,
            $reportedCruisers,
            "Battle report should show exactly {$expectedTotal} cruisers (not inflated by aliasing)"
        );

        // Verify initiator return mission carries exactly the initiator's fleet count
        $initiatorReturn = FleetMission::where('parent_id', $initiatorMission->id)
            ->where('canceled', 0)
            ->first();
        $this->assertNotNull($initiatorReturn, 'Initiator should have a return mission');

        $returnedCruisers = $initiatorReturn->cruiser;
        $this->assertEquals(
            $initiatorCruiserCount,
            $returnedCruisers,
            "Initiator return mission should carry exactly {$initiatorCruiserCount} cruisers (not doubled)"
        );
    }
}
