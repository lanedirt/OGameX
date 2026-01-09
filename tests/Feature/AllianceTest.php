<?php

namespace Tests\Feature;

use Exception;
use OGame\Models\Alliance;
use OGame\Models\AllianceApplication;
use OGame\Models\AllianceMember;
use OGame\Models\AllianceRank;
use OGame\Models\User;
use OGame\Services\AllianceService;
use Tests\AccountTestCase;

/**
 * Test alliance system functionality.
 */
class AllianceTest extends AccountTestCase
{
    /**
     * Generate a unique alliance tag for testing.
     */
    private function uniqueTag(string $prefix = 'TST'): string
    {
        return $prefix . substr(md5(uniqid((string) mt_rand(), true)), 0, 5);
    }

    /**
     * Generate a unique alliance name for testing.
     */
    private function uniqueName(string $prefix = 'Test Alliance'): string
    {
        return $prefix . ' ' . substr(md5(uniqid((string) mt_rand(), true)), 0, 8);
    }

    /**
     * Test that the alliance index page loads correctly.
     */
    public function testAlliancePageLoads(): void
    {
        $response = $this->get('/alliance');
        $response->assertStatus(200);
        $response->assertSee('Alliance');
    }

    /**
     * Test creating a new alliance.
     */
    public function testCreateAlliance(): void
    {
        $allianceService = resolve(AllianceService::class);

        $tag = $this->uniqueTag();
        $name = $this->uniqueName();

        $alliance = $allianceService->createAlliance(
            $this->currentUserId,
            $tag,
            $name
        );

        $this->assertNotNull($alliance);
        $this->assertEquals($tag, $alliance->alliance_tag);
        $this->assertEquals($name, $alliance->alliance_name);
        $this->assertEquals($this->currentUserId, $alliance->founder_user_id);

        // Verify founder is added as member
        $member = AllianceMember::where('alliance_id', $alliance->id)
            ->where('user_id', $this->currentUserId)
            ->first();
        $this->assertNotNull($member);
        $this->assertNull($member->rank_id); // Founder has no rank

        // Verify user's alliance_id is updated
        $user = User::find($this->currentUserId);
        $this->assertEquals($alliance->id, $user->alliance_id);
    }

    /**
     * Test that alliance tag must be unique.
     */
    public function testAllianceTagMustBeUnique(): void
    {
        $allianceService = resolve(AllianceService::class);

        $tag = $this->uniqueTag();
        $name1 = $this->uniqueName();
        $name2 = $this->uniqueName();

        // Create first alliance
        $allianceService->createAlliance($this->currentUserId, $tag, $name1);

        // Try to create second alliance with same tag
        $otherUser = User::factory()->create();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Alliance tag is already taken');
        $allianceService->createAlliance($otherUser->id, $tag, $name2);
    }

    /**
     * Test that user cannot create alliance if already in one.
     */
    public function testUserCannotCreateAllianceIfAlreadyInOne(): void
    {
        $allianceService = resolve(AllianceService::class);

        // Create first alliance
        $allianceService->createAlliance($this->currentUserId, $this->uniqueTag(), $this->uniqueName());

        // Try to create second alliance
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('User is already in an alliance');
        $allianceService->createAlliance($this->currentUserId, $this->uniqueTag(), $this->uniqueName());
    }

    /**
     * Test applying to join an alliance.
     */
    public function testApplyToAlliance(): void
    {
        $allianceService = resolve(AllianceService::class);

        // Create alliance with another user
        $founderUser = User::factory()->create();
        $alliance = $allianceService->createAlliance($founderUser->id, $this->uniqueTag(), $this->uniqueName());

        // Apply to join
        $application = $allianceService->applyToAlliance(
            $this->currentUserId,
            $alliance->id,
            'I want to join!'
        );

        $this->assertNotNull($application);
        $this->assertEquals($alliance->id, $application->alliance_id);
        $this->assertEquals($this->currentUserId, $application->user_id);
        $this->assertEquals('I want to join!', $application->application_message);
        $this->assertEquals(AllianceApplication::STATUS_PENDING, $application->status);
        $this->assertFalse((bool) $application->viewed);
    }

    /**
     * Test that user cannot apply if already in an alliance.
     */
    public function testCannotApplyIfAlreadyInAlliance(): void
    {
        $allianceService = resolve(AllianceService::class);

        // Create first alliance and join
        $alliance1 = $allianceService->createAlliance($this->currentUserId, $this->uniqueTag(), $this->uniqueName());

        // Create second alliance
        $otherUser = User::factory()->create();
        $alliance2 = $allianceService->createAlliance($otherUser->id, $this->uniqueTag(), $this->uniqueName());

        // Try to apply to second alliance
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('User is already in an alliance');
        $allianceService->applyToAlliance($this->currentUserId, $alliance2->id);
    }

    /**
     * Test accepting an alliance application.
     */
    public function testAcceptApplication(): void
    {
        $allianceService = resolve(AllianceService::class);

        // Create alliance
        $alliance = $allianceService->createAlliance($this->currentUserId, $this->uniqueTag(), $this->uniqueName());

        // Create applicant
        $applicant = User::factory()->create();
        $application = $allianceService->applyToAlliance($applicant->id, $alliance->id, 'Please accept me');

        // Accept application
        $allianceService->acceptApplication($application->id, $this->currentUserId);

        // Verify application status changed
        $application->refresh();
        $this->assertEquals(AllianceApplication::STATUS_ACCEPTED, $application->status);

        // Verify applicant is now a member
        $member = AllianceMember::where('alliance_id', $alliance->id)
            ->where('user_id', $applicant->id)
            ->first();
        $this->assertNotNull($member);

        // Verify applicant's user record updated
        $applicant->refresh();
        $this->assertEquals($alliance->id, $applicant->alliance_id);
    }

    /**
     * Test rejecting an alliance application.
     */
    public function testRejectApplication(): void
    {
        $allianceService = resolve(AllianceService::class);

        // Create alliance
        $alliance = $allianceService->createAlliance($this->currentUserId, $this->uniqueTag(), $this->uniqueName());

        // Create applicant
        $applicant = User::factory()->create();
        $application = $allianceService->applyToAlliance($applicant->id, $alliance->id);

        // Reject application
        $allianceService->rejectApplication($application->id, $this->currentUserId);

        // Verify application status changed
        $application->refresh();
        $this->assertEquals(AllianceApplication::STATUS_REJECTED, $application->status);

        // Verify applicant is NOT a member
        $member = AllianceMember::where('alliance_id', $alliance->id)
            ->where('user_id', $applicant->id)
            ->first();
        $this->assertNull($member);
    }

    /**
     * Test creating a rank with permissions.
     */
    public function testCreateRank(): void
    {
        $allianceService = resolve(AllianceService::class);

        // Create alliance
        $alliance = $allianceService->createAlliance($this->currentUserId, $this->uniqueTag(), $this->uniqueName());

        // Create rank
        $permissions = [
            AllianceRank::PERMISSION_SEE_MEMBERS,
            AllianceRank::PERMISSION_SEE_APPLICATIONS,
        ];
        $rank = $allianceService->createRank($alliance->id, 'Officer', $permissions, $this->currentUserId);

        $this->assertNotNull($rank);
        $this->assertEquals('Officer', $rank->rank_name);
        $this->assertEquals($permissions, $rank->permissions);
        $this->assertEquals(1, $rank->sort_order);
    }

    /**
     * Test assigning a rank to a member.
     */
    public function testAssignRank(): void
    {
        $allianceService = resolve(AllianceService::class);

        // Create alliance
        $alliance = $allianceService->createAlliance($this->currentUserId, $this->uniqueTag(), $this->uniqueName());

        // Create rank
        $rank = $allianceService->createRank($alliance->id, 'Member', [], $this->currentUserId);

        // Add another member
        $newMember = User::factory()->create();
        $application = $allianceService->applyToAlliance($newMember->id, $alliance->id);
        $allianceService->acceptApplication($application->id, $this->currentUserId);

        // Assign rank
        $allianceService->assignRank($alliance->id, $newMember->id, $rank->id, $this->currentUserId);

        // Verify rank assigned
        $member = AllianceMember::where('alliance_id', $alliance->id)
            ->where('user_id', $newMember->id)
            ->first();
        $this->assertEquals($rank->id, $member->rank_id);
    }

    /**
     * Test that founder cannot be assigned a rank.
     */
    public function testCannotAssignRankToFounder(): void
    {
        $allianceService = resolve(AllianceService::class);

        // Create alliance
        $alliance = $allianceService->createAlliance($this->currentUserId, $this->uniqueTag(), $this->uniqueName());

        // Create rank
        $rank = $allianceService->createRank($alliance->id, 'Member', [], $this->currentUserId);

        // Try to assign rank to founder
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Cannot assign rank to founder');
        $allianceService->assignRank($alliance->id, $this->currentUserId, $rank->id, $this->currentUserId);
    }

    /**
     * Test kicking a member from alliance.
     */
    public function testKickMember(): void
    {
        $allianceService = resolve(AllianceService::class);

        // Create alliance
        $alliance = $allianceService->createAlliance($this->currentUserId, $this->uniqueTag(), $this->uniqueName());

        // Add member
        $member = User::factory()->create();
        $application = $allianceService->applyToAlliance($member->id, $alliance->id);
        $allianceService->acceptApplication($application->id, $this->currentUserId);

        // Kick member
        $allianceService->kickMember($alliance->id, $member->id, $this->currentUserId);

        // Verify member removed
        $allianceMember = AllianceMember::where('alliance_id', $alliance->id)
            ->where('user_id', $member->id)
            ->first();
        $this->assertNull($allianceMember);

        // Verify user's alliance_id cleared
        $member->refresh();
        $this->assertNull($member->alliance_id);
    }

    /**
     * Test that founder cannot be kicked.
     */
    public function testCannotKickFounder(): void
    {
        $allianceService = resolve(AllianceService::class);

        // Create alliance
        $alliance = $allianceService->createAlliance($this->currentUserId, $this->uniqueTag(), $this->uniqueName());

        // Try to kick founder (this should fail)
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Cannot kick the alliance founder');
        $allianceService->kickMember($alliance->id, $this->currentUserId, $this->currentUserId);
    }

    /**
     * Test leaving an alliance.
     */
    public function testLeaveAlliance(): void
    {
        $allianceService = resolve(AllianceService::class);

        // Create alliance with another user
        $founder = User::factory()->create();
        $alliance = $allianceService->createAlliance($founder->id, $this->uniqueTag(), $this->uniqueName());

        // Join alliance
        $application = $allianceService->applyToAlliance($this->currentUserId, $alliance->id);
        $allianceService->acceptApplication($application->id, $founder->id);

        // Leave alliance
        $allianceService->leaveAlliance($this->currentUserId);

        // Verify membership removed
        $member = AllianceMember::where('alliance_id', $alliance->id)
            ->where('user_id', $this->currentUserId)
            ->first();
        $this->assertNull($member);

        // Verify user's alliance_id cleared
        $user = User::find($this->currentUserId);
        $this->assertNull($user->alliance_id);
    }

    /**
     * Test that founder cannot leave alliance.
     */
    public function testFounderCannotLeave(): void
    {
        $allianceService = resolve(AllianceService::class);

        // Create alliance
        $alliance = $allianceService->createAlliance($this->currentUserId, $this->uniqueTag(), $this->uniqueName());

        // Try to leave as founder
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Founder cannot leave alliance');
        $allianceService->leaveAlliance($this->currentUserId);
    }

    /**
     * Test updating alliance texts.
     */
    public function testUpdateTexts(): void
    {
        $allianceService = resolve(AllianceService::class);

        // Create alliance
        $alliance = $allianceService->createAlliance($this->currentUserId, $this->uniqueTag(), $this->uniqueName());

        // Update texts
        $allianceService->updateTexts(
            $alliance->id,
            $this->currentUserId,
            'Internal info',
            'External info',
            'Application info'
        );

        // Verify texts updated
        $alliance->refresh();
        $this->assertEquals('Internal info', $alliance->internal_text);
        $this->assertEquals('External info', $alliance->external_text);
        $this->assertEquals('Application info', $alliance->application_text);
    }

    /**
     * Test disbanding an alliance.
     */
    public function testDisbandAlliance(): void
    {
        $allianceService = resolve(AllianceService::class);

        // Create alliance
        $alliance = $allianceService->createAlliance($this->currentUserId, $this->uniqueTag(), $this->uniqueName());

        // Add a member
        $member = User::factory()->create();
        $application = $allianceService->applyToAlliance($member->id, $alliance->id);
        $allianceService->acceptApplication($application->id, $this->currentUserId);

        // Disband alliance
        $allianceService->disbandAlliance($alliance->id, $this->currentUserId);

        // Verify alliance deleted
        $allianceExists = Alliance::find($alliance->id);
        $this->assertNull($allianceExists);

        // Verify members' alliance_id cleared
        $user = User::find($this->currentUserId);
        $this->assertNull($user->alliance_id);
        $member->refresh();
        $this->assertNull($member->alliance_id);
    }

    /**
     * Test that founder has all permissions.
     */
    public function testFounderHasAllPermissions(): void
    {
        $allianceService = resolve(AllianceService::class);

        // Create alliance
        $alliance = $allianceService->createAlliance($this->currentUserId, $this->uniqueTag(), $this->uniqueName());

        // Get founder member
        $founderMember = AllianceMember::where('alliance_id', $alliance->id)
            ->where('user_id', $this->currentUserId)
            ->first();

        // Test all permissions
        $this->assertTrue($founderMember->hasPermission(AllianceRank::PERMISSION_SEE_APPLICATIONS));
        $this->assertTrue($founderMember->hasPermission(AllianceRank::PERMISSION_EDIT_APPLICATIONS));
        $this->assertTrue($founderMember->hasPermission(AllianceRank::PERMISSION_SEE_MEMBERS));
        $this->assertTrue($founderMember->hasPermission(AllianceRank::PERMISSION_KICK_USER));
        $this->assertTrue($founderMember->hasPermission(AllianceRank::PERMISSION_MANAGE_ALLY));
        $this->assertTrue($founderMember->hasPermission(AllianceRank::PERMISSION_DELETE_ALLY));
    }

    /**
     * Test permission checking for regular members.
     */
    public function testMemberPermissions(): void
    {
        $allianceService = resolve(AllianceService::class);

        // Create alliance
        $alliance = $allianceService->createAlliance($this->currentUserId, $this->uniqueTag(), $this->uniqueName());

        // Create rank with specific permissions
        $rank = $allianceService->createRank(
            $alliance->id,
            'Limited',
            [AllianceRank::PERMISSION_SEE_MEMBERS],
            $this->currentUserId
        );

        // Add member with rank
        $member = User::factory()->create();
        $application = $allianceService->applyToAlliance($member->id, $alliance->id);
        $allianceService->acceptApplication($application->id, $this->currentUserId);
        $allianceService->assignRank($alliance->id, $member->id, $rank->id, $this->currentUserId);

        // Get member
        $allianceMember = AllianceMember::where('alliance_id', $alliance->id)
            ->where('user_id', $member->id)
            ->first();

        // Test permissions
        $this->assertTrue($allianceMember->hasPermission(AllianceRank::PERMISSION_SEE_MEMBERS));
        $this->assertFalse($allianceMember->hasPermission(AllianceRank::PERMISSION_KICK_USER));
        $this->assertFalse($allianceMember->hasPermission(AllianceRank::PERMISSION_MANAGE_ALLY));
    }
}
