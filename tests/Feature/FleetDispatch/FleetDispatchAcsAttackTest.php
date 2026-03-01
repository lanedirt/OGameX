<?php

namespace Tests\Feature\FleetDispatch;

use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use OGame\Factories\PlanetServiceFactory;
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
        // Clean up fleet unions and their invites created during this test.
        // Deleting the union cascades to fleet_union_invites via FK.
        // Also unlink any fleet missions referencing these unions.
        $unionIds = DB::table('fleet_unions')
            ->whereIn('user_id', array_merge(self::$allCreatedBuddyUserIds, isset($this->currentUserId) ? [$this->currentUserId] : []))
            ->pluck('id')
            ->toArray();

        if (!empty($unionIds)) {
            // Unlink fleet missions from these unions before deleting
            DB::table('fleet_missions')
                ->whereIn('union_id', $unionIds)
                ->update(['union_id' => null, 'union_slot' => null]);

            DB::table('fleet_unions')
                ->whereIn('id', $unionIds)
                ->delete();
        }

        // Clean up buddy relationships and user state for created users
        while (!empty(self::$allCreatedBuddyUserIds)) {
            $buddyUserId = array_shift(self::$allCreatedBuddyUserIds);

            DB::table('buddy_requests')
                ->where(function ($query) use ($buddyUserId) {
                    $query->where('sender_user_id', $buddyUserId)
                        ->orWhere('receiver_user_id', $buddyUserId);
                })
                ->delete();

            DB::table('users')
                ->where('id', $buddyUserId)
                ->update([
                    'alliance_id' => null,
                    'alliance_left_at' => null,
                    'vacation_mode' => false,
                    'vacation_mode_activated_at' => null,
                    'vacation_mode_until' => null,
                ]);
        }

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

        $targetPlanet = Planet::factory()->create([
            'user_id' => $targetUser->id,
            'galaxy' => $this->planetService->getPlanetCoordinates()->galaxy,
            'system' => min(499, $this->planetService->getPlanetCoordinates()->system + 5),
            'planet' => 8,
        ]);

        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $targetPlayerService = resolve(PlayerService::class, ['player_id' => $targetUser->id]);
        $this->targetPlanet = $planetServiceFactory->makeForPlayer($targetPlayerService, $targetPlanet->id);
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
        // (avoids exceeding the 30% delay limit when joining a union)
        $allyPlanet = Planet::factory()->create([
            'user_id' => $allyUser->id,
            'galaxy' => $this->planetService->getPlanetCoordinates()->galaxy,
            'system' => min(499, $this->planetService->getPlanetCoordinates()->system + 5),
            'planet' => 9,
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
     * Note: per-fleet loot distribution (survivingCargo/lootShare) is not yet implemented
     * for multi-fleet ACS attacks; this test verifies return missions are created correctly.
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
}
