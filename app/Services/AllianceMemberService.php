<?php

namespace OGame\Services;

use Exception;
use Illuminate\Support\Carbon;
use OGame\Models\Alliance;
use OGame\Models\AllianceApplication;
use OGame\Models\AllianceMember;
use OGame\Models\User;

/**
 * Class AllianceMemberService.
 *
 * Alliance member management service.
 *
 * @package OGame\Services
 */
class AllianceMemberService
{
    /**
     * Apply to join an alliance.
     *
     * @param Alliance $alliance
     * @param User $user
     * @param string|null $applicationText
     * @return AllianceApplication
     * @throws Exception
     */
    public static function applyToAlliance(Alliance $alliance, User $user, ?string $applicationText = null): AllianceApplication
    {
        // Check if user is already in an alliance
        if (AllianceMember::where('user_id', $user->id)->exists()) {
            throw new Exception('You are already a member of an alliance.');
        }

        // Check if user already has a pending application
        if (AllianceApplication::where('user_id', $user->id)
            ->where('alliance_id', $alliance->id)
            ->where('status', 'pending')
            ->exists()) {
            throw new Exception('You already have a pending application to this alliance.');
        }

        // Check if alliance is open for applications
        if (!$alliance->open_for_applications) {
            throw new Exception('This alliance is not accepting applications.');
        }

        // Create application
        return AllianceApplication::create([
            'alliance_id' => $alliance->id,
            'user_id' => $user->id,
            'application_text' => $applicationText,
            'status' => 'pending',
        ]);
    }

    /**
     * Accept an application.
     *
     * @param AllianceApplication $application
     * @param User $reviewer
     * @param int|null $rankId
     * @return AllianceMember
     * @throws Exception
     */
    public static function acceptApplication(AllianceApplication $application, User $reviewer, ?int $rankId = null): AllianceMember
    {
        if ($application->status !== 'pending') {
            throw new Exception('This application has already been processed.');
        }

        // Check if user is already in an alliance
        if (AllianceMember::where('user_id', $application->user_id)->exists()) {
            throw new Exception('User is already a member of an alliance.');
        }

        // If no rank specified, assign the default "Member" rank
        if (!$rankId) {
            $memberRank = $application->alliance->ranks()
                ->where('name', 'Member')
                ->orderBy('sort_order', 'desc')
                ->first();
            $rankId = $memberRank?->id;
        }

        // Create member
        $member = AllianceMember::create([
            'alliance_id' => $application->alliance_id,
            'user_id' => $application->user_id,
            'rank_id' => $rankId,
            'application_text' => $application->application_text,
            'joined_at' => Carbon::now(),
        ]);

        // Update application status
        $application->update([
            'status' => 'accepted',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => Carbon::now(),
        ]);

        return $member;
    }

    /**
     * Reject an application.
     *
     * @param AllianceApplication $application
     * @param User $reviewer
     * @return void
     * @throws Exception
     */
    public static function rejectApplication(AllianceApplication $application, User $reviewer): void
    {
        if ($application->status !== 'pending') {
            throw new Exception('This application has already been processed.');
        }

        $application->update([
            'status' => 'rejected',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => Carbon::now(),
        ]);
    }

    /**
     * Invite a user to the alliance.
     *
     * @param Alliance $alliance
     * @param User $user
     * @param int|null $rankId
     * @return AllianceMember
     * @throws Exception
     */
    public static function inviteUser(Alliance $alliance, User $user, ?int $rankId = null): AllianceMember
    {
        // Check if user is already in an alliance
        if (AllianceMember::where('user_id', $user->id)->exists()) {
            throw new Exception('User is already a member of an alliance.');
        }

        // If no rank specified, assign the default "Member" rank
        if (!$rankId) {
            $memberRank = $alliance->ranks()
                ->where('name', 'Member')
                ->orderBy('sort_order', 'desc')
                ->first();
            $rankId = $memberRank?->id;
        }

        // Create member directly
        return AllianceMember::create([
            'alliance_id' => $alliance->id,
            'user_id' => $user->id,
            'rank_id' => $rankId,
            'joined_at' => Carbon::now(),
        ]);
    }

    /**
     * Remove a member from the alliance.
     *
     * @param AllianceMember $member
     * @return void
     * @throws Exception
     */
    public static function kickMember(AllianceMember $member): void
    {
        // Don't allow kicking the founder
        if ($member->alliance->founder_id === $member->user_id) {
            throw new Exception('Cannot kick the alliance founder.');
        }

        $member->delete();
    }

    /**
     * Leave the alliance voluntarily.
     *
     * @param User $user
     * @return void
     * @throws Exception
     */
    public static function leaveAlliance(User $user): void
    {
        $membership = AllianceMember::where('user_id', $user->id)->first();

        if (!$membership) {
            throw new Exception('You are not a member of any alliance.');
        }

        // Check if user is the founder
        if ($membership->alliance->founder_id === $user->id) {
            // If founder is leaving and there are other members, transfer leadership or disband
            $memberCount = $membership->alliance->members()->count();
            if ($memberCount > 1) {
                throw new Exception('As the founder, you must either disband the alliance or transfer leadership before leaving.');
            }
            // If founder is the only member, delete the alliance
            $membership->alliance->delete();
        } else {
            $membership->delete();
        }
    }

    /**
     * Change a member's rank.
     *
     * @param AllianceMember $member
     * @param int $newRankId
     * @return void
     * @throws Exception
     */
    public static function changeMemberRank(AllianceMember $member, int $newRankId): void
    {
        // Don't allow changing the founder's rank
        if ($member->alliance->founder_id === $member->user_id) {
            throw new Exception('Cannot change the founder\'s rank.');
        }

        // Verify the rank belongs to the same alliance
        $rank = $member->alliance->ranks()->where('id', $newRankId)->first();
        if (!$rank) {
            throw new Exception('Invalid rank for this alliance.');
        }

        $member->update(['rank_id' => $newRankId]);
    }

    /**
     * Transfer alliance leadership to another member.
     *
     * @param Alliance $alliance
     * @param User $newFounder
     * @return void
     * @throws Exception
     */
    public static function transferLeadership(Alliance $alliance, User $newFounder): void
    {
        // Check if new founder is a member
        $newFounderMembership = AllianceMember::where('alliance_id', $alliance->id)
            ->where('user_id', $newFounder->id)
            ->first();

        if (!$newFounderMembership) {
            throw new Exception('New founder must be a member of the alliance.');
        }

        // Update alliance founder
        $alliance->update(['founder_id' => $newFounder->id]);

        // Update new founder's rank to Leader
        $leaderRank = $alliance->ranks()->where('name', 'Leader')->first();
        if ($leaderRank) {
            $newFounderMembership->update(['rank_id' => $leaderRank->id]);
        }
    }

    /**
     * Get member's alliance.
     *
     * @param User $user
     * @return Alliance|null
     */
    public static function getUserAlliance(User $user): ?Alliance
    {
        $membership = AllianceMember::where('user_id', $user->id)->first();
        return $membership?->alliance;
    }

    /**
     * Check if user is in any alliance.
     *
     * @param User $user
     * @return bool
     */
    public static function isInAlliance(User $user): bool
    {
        return AllianceMember::where('user_id', $user->id)->exists();
    }
}
