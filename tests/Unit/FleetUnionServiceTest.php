<?php

namespace Tests\Unit;

use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use OGame\Models\Alliance;
use OGame\Models\AllianceMember;
use OGame\Models\BuddyRequest;
use OGame\Models\FleetMission;
use OGame\Models\FleetUnion;
use OGame\Models\Planet;
use OGame\Models\User;
use OGame\Services\AllianceService;
use OGame\Services\FleetUnionService;
use Tests\TestCase;

class FleetUnionServiceTest extends TestCase
{
    use DatabaseTransactions;

    private FleetUnionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(FleetUnionService::class);
    }

    /**
     * Test getMaxDelayTime returns 30% of remaining time.
     */
    public function testGetMaxDelayTimeCalculation(): void
    {
        $union = new FleetUnion();
        $union->time_arrival = time() + 1000;

        $maxDelay = $this->service->getMaxDelayTime($union);

        $this->assertEquals(300, $maxDelay);
    }

    /**
     * Test getMaxDelayTime with zero remaining time.
     */
    public function testGetMaxDelayTimeZeroRemaining(): void
    {
        $union = new FleetUnion();
        $union->time_arrival = time() - 100; // Already passed

        $maxDelay = $this->service->getMaxDelayTime($union);

        $this->assertEquals(0, $maxDelay);
    }

    /**
     * Test getMaxDelayTime with very short remaining time.
     */
    public function testGetMaxDelayTimeShortDuration(): void
    {
        $union = new FleetUnion();
        $union->time_arrival = time() + 10;

        $maxDelay = $this->service->getMaxDelayTime($union);

        $this->assertEquals(3, $maxDelay); // 30% of 10
    }

    /**
     * Test getMaxDelayTime with various remaining times.
     */
    public function testGetMaxDelayTimeVariousValues(): void
    {
        $testCases = [
            ['remaining' => 3600, 'expected' => 1080],  // 1 hour -> 18 minutes
            ['remaining' => 7200, 'expected' => 2160],  // 2 hours -> 36 minutes
            ['remaining' => 100, 'expected' => 30],     // 100 seconds -> 30 seconds
            ['remaining' => 1, 'expected' => 0],        // 1 second -> 0 seconds (floor)
        ];

        foreach ($testCases as $case) {
            $union = new FleetUnion();
            $union->time_arrival = time() + $case['remaining'];

            $maxDelay = $this->service->getMaxDelayTime($union);

            $this->assertEquals(
                $case['expected'],
                $maxDelay,
                "Failed for remaining time: {$case['remaining']}"
            );
        }
    }

    /**
     * Test that alliance members can join a union.
     */
    public function testAllianceMembersCanJoinUnion(): void
    {
        // Create two users
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create planets for both users
        $planet1 = Planet::factory()->create([
            'user_id' => $user1->id,
            'galaxy' => 1,
            'system' => 10,
            'planet' => 1,
        ]);
        $planet2 = Planet::factory()->create([
            'user_id' => $user2->id,
            'galaxy' => 1,
            'system' => 10,
            'planet' => 2,
        ]);

        // Create alliance and add both users
        $allianceService = app(AllianceService::class);
        $alliance = $allianceService->createAlliance($user1->id, 'TEST', 'Test Alliance');

        // Add user2 to alliance
        /** @var int<1, max> $allianceId */
        $allianceId = $alliance->id;
        $user2->alliance_id = $allianceId;
        $user2->save();
        AllianceMember::create([
            'alliance_id' => $alliance->id,
            'user_id' => $user2->id,
            'rank_id' => null,
            'joined_at' => now(),
        ]);

        // Create attack mission from user1
        $mission1 = FleetMission::forceCreate([
            'user_id' => $user1->id,
            'planet_id_from' => $planet1->id,
            'planet_id_to' => $planet2->id,
            'mission_type' => 1, // Attack
            'time_departure' => time(),
            'time_arrival' => time() + 1000,
            'galaxy_to' => 1,
            'system_to' => 1,
            'position_to' => 1,
            'type_to' => 1, // Planet
            'light_fighter' => 10,
        ]);

        // Create union from mission1
        $union = $this->service->createUnion($mission1);

        // Create another attack mission from user2 (alliance member)
        $mission2 = FleetMission::forceCreate([
            'user_id' => $user2->id,
            'planet_id_from' => $planet2->id,
            'planet_id_to' => $planet1->id,
            'mission_type' => 1, // Attack
            'time_departure' => time(),
            'time_arrival' => time() + 1000,
            'galaxy_to' => 1,
            'system_to' => 1,
            'position_to' => 2,
            'type_to' => 1, // Planet
            'cruiser' => 5,
        ]);

        // Should be able to join as alliance member
        $this->service->joinUnion($union, $mission2);

        // Assert mission2 is now in the union
        $mission2->refresh();
        $this->assertEquals($union->id, $mission2->union_id);
        $this->assertEquals(2, $mission2->union_slot);
        $this->assertEquals(2, $mission2->mission_type); // Converted to ACS Attack
    }

    /**
     * Test that buddies can join a union.
     */
    public function testBuddiesCanJoinUnion(): void
    {
        // Create two users
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create planets for both users
        $planet1 = Planet::factory()->create([
            'user_id' => $user1->id,
            'galaxy' => 1,
            'system' => 20,
            'planet' => 1,
        ]);
        $planet2 = Planet::factory()->create([
            'user_id' => $user2->id,
            'galaxy' => 1,
            'system' => 20,
            'planet' => 2,
        ]);

        // Create buddy relationship
        BuddyRequest::create([
            'sender_user_id' => $user1->id,
            'receiver_user_id' => $user2->id,
            'status' => BuddyRequest::STATUS_ACCEPTED,
        ]);

        // Create attack mission from user1
        $mission1 = FleetMission::forceCreate([
            'user_id' => $user1->id,
            'planet_id_from' => $planet1->id,
            'planet_id_to' => $planet2->id,
            'mission_type' => 1, // Attack
            'time_departure' => time(),
            'time_arrival' => time() + 1000,
            'galaxy_to' => 1,
            'system_to' => 1,
            'position_to' => 1,
            'type_to' => 1, // Planet
            'light_fighter' => 10,
        ]);

        // Create union from mission1
        $union = $this->service->createUnion($mission1);

        // Create another attack mission from user2 (buddy)
        $mission2 = FleetMission::forceCreate([
            'user_id' => $user2->id,
            'planet_id_from' => $planet2->id,
            'planet_id_to' => $planet1->id,
            'mission_type' => 1, // Attack
            'time_departure' => time(),
            'time_arrival' => time() + 1000,
            'galaxy_to' => 1,
            'system_to' => 1,
            'position_to' => 2,
            'type_to' => 1, // Planet
            'cruiser' => 5,
        ]);

        // Should be able to join as buddy
        $this->service->joinUnion($union, $mission2);

        // Assert mission2 is now in the union
        $mission2->refresh();
        $this->assertEquals($union->id, $mission2->union_id);
        $this->assertEquals(2, $mission2->union_slot);
        $this->assertEquals(2, $mission2->mission_type); // Converted to ACS Attack
    }

    /**
     * Test that non-affiliated players cannot join a union.
     */
    public function testNonAffiliatedPlayersCannotJoinUnion(): void
    {
        // Create two users with NO relationship
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create planets for both users
        $planet1 = Planet::factory()->create([
            'user_id' => $user1->id,
            'galaxy' => 1,
            'system' => 30,
            'planet' => 1,
        ]);
        $planet2 = Planet::factory()->create([
            'user_id' => $user2->id,
            'galaxy' => 1,
            'system' => 30,
            'planet' => 2,
        ]);

        // Create attack mission from user1
        $mission1 = FleetMission::forceCreate([
            'user_id' => $user1->id,
            'planet_id_from' => $planet1->id,
            'planet_id_to' => $planet2->id,
            'mission_type' => 1, // Attack
            'time_departure' => time(),
            'time_arrival' => time() + 1000,
            'galaxy_to' => 1,
            'system_to' => 1,
            'position_to' => 1,
            'type_to' => 1, // Planet
            'light_fighter' => 10,
        ]);

        // Create union from mission1
        $union = $this->service->createUnion($mission1);

        // Create another attack mission from user2 (NOT buddy or alliance member)
        $mission2 = FleetMission::forceCreate([
            'user_id' => $user2->id,
            'planet_id_from' => $planet2->id,
            'planet_id_to' => $planet1->id,
            'mission_type' => 1, // Attack
            'time_departure' => time(),
            'time_arrival' => time() + 1000,
            'galaxy_to' => 1,
            'system_to' => 1,
            'position_to' => 2,
            'type_to' => 1, // Planet
            'cruiser' => 5,
        ]);

        // Should throw exception
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(__('t_acs.error_not_buddy_or_ally'));

        $this->service->joinUnion($union, $mission2);
    }

    /**
     * Test that max fleets limit is enforced.
     */
    public function testMaxFleetsLimitEnforced(): void
    {
        // Create user and alliance
        $user1 = User::factory()->create();
        $planet1 = Planet::factory()->create([
            'user_id' => $user1->id,
            'galaxy' => 1,
            'system' => 40,
            'planet' => 1,
        ]);

        // Create attack mission
        $mission1 = FleetMission::forceCreate([
            'user_id' => $user1->id,
            'planet_id_from' => $planet1->id,
            'mission_type' => 1,
            'time_departure' => time(),
            'time_arrival' => time() + 1000,
            'galaxy_to' => 1,
            'system_to' => 1,
            'position_to' => 1,
            'type_to' => 1, // Planet
            'light_fighter' => 10,
        ]);

        // Create union
        $union = $this->service->createUnion($mission1);

        // Create 15 more missions to fill up the union (total 16)
        for ($i = 0; $i < 15; $i++) {
            $mission = FleetMission::forceCreate([
                'user_id' => $user1->id,
                'planet_id_from' => $planet1->id,
                'mission_type' => 1,
                'time_departure' => time(),
                'time_arrival' => time() + 1000,
                'galaxy_to' => 1,
                'system_to' => 1,
                'position_to' => 1,
                'type_to' => 1, // Planet
                'cruiser' => 1,
            ]);
            $this->service->joinUnion($union, $mission);
        }

        // Try to add 17th mission - should fail
        $mission17 = FleetMission::forceCreate([
            'user_id' => $user1->id,
            'planet_id_from' => $planet1->id,
            'mission_type' => 1,
            'time_departure' => time(),
            'time_arrival' => time() + 1000,
            'galaxy_to' => 1,
            'system_to' => 1,
            'position_to' => 1,
            'type_to' => 1, // Planet
            'battle_ship' => 1,
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(__('t_acs.error_max_fleets_reached'));

        $this->service->joinUnion($union, $mission17);
    }

    /**
     * Test that max players limit is enforced.
     */
    public function testMaxPlayersLimitEnforced(): void
    {
        // Create 5 users with buddy relationships to user1
        $user1 = User::factory()->create();
        $planet1 = Planet::factory()->create([
            'user_id' => $user1->id,
            'galaxy' => 1,
            'system' => 50,
            'planet' => 1,
        ]);

        // Create attack mission from user1
        $mission1 = FleetMission::forceCreate([
            'user_id' => $user1->id,
            'planet_id_from' => $planet1->id,
            'mission_type' => 1,
            'time_departure' => time(),
            'time_arrival' => time() + 1000,
            'galaxy_to' => 1,
            'system_to' => 1,
            'position_to' => 1,
            'type_to' => 1, // Planet
            'light_fighter' => 10,
        ]);

        // Create union
        $union = $this->service->createUnion($mission1);

        // Add 4 more players (total 5)
        for ($i = 0; $i < 4; $i++) {
            $user = User::factory()->create();
            $planet = Planet::factory()->create([
                'user_id' => $user->id,
                'galaxy' => 1,
                'system' => 50,
                'planet' => 2 + $i,
            ]);

            // Make buddy with user1
            BuddyRequest::create([
                'sender_user_id' => $user1->id,
                'receiver_user_id' => $user->id,
                'status' => BuddyRequest::STATUS_ACCEPTED,
            ]);

            $mission = FleetMission::forceCreate([
                'user_id' => $user->id,
                'planet_id_from' => $planet->id,
                'mission_type' => 1,
                'time_departure' => time(),
                'time_arrival' => time() + 1000,
                'galaxy_to' => 1,
                'system_to' => 1,
                'position_to' => 1,
                'type_to' => 1, // Planet
                'cruiser' => 1,
            ]);
            $this->service->joinUnion($union, $mission);
        }

        // Try to add 6th player - should fail
        $user6 = User::factory()->create();
        $planet6 = Planet::factory()->create([
            'user_id' => $user6->id,
            'galaxy' => 1,
            'system' => 50,
            'planet' => 6,
        ]);

        BuddyRequest::create([
            'sender_user_id' => $user1->id,
            'receiver_user_id' => $user6->id,
            'status' => BuddyRequest::STATUS_ACCEPTED,
        ]);

        $mission6 = FleetMission::forceCreate([
            'user_id' => $user6->id,
            'planet_id_from' => $planet6->id,
            'mission_type' => 1,
            'time_departure' => time(),
            'time_arrival' => time() + 1000,
            'galaxy_to' => 1,
            'system_to' => 1,
            'position_to' => 1,
            'type_to' => 1, // Planet
            'battle_ship' => 1,
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(__('t_acs.error_max_players_reached'));

        $this->service->joinUnion($union, $mission6);
    }
}
