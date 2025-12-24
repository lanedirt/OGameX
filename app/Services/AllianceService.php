<?php

namespace OGame\Services;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use OGame\Models\Alliance;
use OGame\Models\AllianceApplication;
use OGame\Models\AllianceMember;
use OGame\Models\AllianceRank;
use OGame\Models\User;

/**
 * @phpstan-type AllianceId int<1, max>
 */

/**
 * Class AllianceService.
 *
 * Alliance Service - handles all alliance related logic.
 *
 * @package OGame\Services
 */
class AllianceService
{

    /**
     * Create a new alliance.
     *
     * @param int $userId
     * @param string $tag
     * @param string $name
     * @return Alliance
     * @throws Exception
     */
    public function createAlliance(int $userId, string $tag, string $name): Alliance
    {
        // Validate that user is not already in an alliance
        $user = User::findOrFail($userId);
        if ($user->alliance_id !== null) {
            throw new Exception('User is already in an alliance');
        }

        // Validate tag length (3-8 characters)
        if (strlen($tag) < 3 || strlen($tag) > 8) {
            throw new Exception('Alliance tag must be between 3 and 8 characters');
        }

        // Validate name length (3-30 characters)
        if (strlen($name) < 3 || strlen($name) > 30) {
            throw new Exception('Alliance name must be between 3 and 30 characters');
        }

        // Check if tag is unique
        if (Alliance::where('alliance_tag', $tag)->exists()) {
            throw new Exception('Alliance tag is already taken');
        }

        DB::beginTransaction();
        try {
            // Create the alliance
            $alliance = Alliance::create([
                'alliance_tag' => $tag,
                'alliance_name' => $name,
                'founder_user_id' => $userId,
            ]);

            // Add founder as member (with no rank, founder has all permissions)
            AllianceMember::create([
                'alliance_id' => $alliance->id,
                'user_id' => $userId,
                'rank_id' => null,
                'joined_at' => now(),
            ]);

            // Update user's alliance_id
            /** @phpstan-ignore assign.propertyType */
            $user->alliance_id = $alliance->id;
            $user->save();

            DB::commit();

            return $alliance;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get alliance by ID.
     *
     * @param int $allianceId
     * @return Alliance|null
     */
    public function getAllianceById(int $allianceId): Alliance|null
    {
        return Alliance::find($allianceId);
    }

    /**
     * Get alliance by tag.
     *
     * @param string $tag
     * @return Alliance|null
     */
    public function getAllianceByTag(string $tag): Alliance|null
    {
        return Alliance::where('alliance_tag', $tag)->first();
    }

    /**
     * Get all members of an alliance.
     *
     * @param int $allianceId
     * @return Collection<int, AllianceMember>
     */
    public function getAllianceMembers(int $allianceId): Collection
    {
        return AllianceMember::where('alliance_id', $allianceId)
            ->with(['user', 'rank'])
            ->orderBy('joined_at', 'asc')
            ->get();
    }

    /**
     * Get alliance member for a user.
     *
     * @param int $allianceId
     * @param int $userId
     * @return AllianceMember|null
     */
    public function getAllianceMember(int $allianceId, int $userId): AllianceMember|null
    {
        return AllianceMember::where('alliance_id', $allianceId)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Apply to join an alliance.
     *
     * @param int $userId
     * @param int $allianceId
     * @param string|null $message
     * @return AllianceApplication
     * @throws Exception
     */
    public function applyToAlliance(int $userId, int $allianceId, string|null $message = null): AllianceApplication
    {
        // Validate that user is not already in an alliance
        $user = User::findOrFail($userId);
        if ($user->alliance_id !== null) {
            throw new Exception('User is already in an alliance');
        }

        // Validate that alliance exists and is open
        $alliance = Alliance::findOrFail($allianceId);
        if (!$alliance->is_open) {
            throw new Exception('Alliance is not accepting applications');
        }

        // Check if user already has a pending application
        $existingApplication = AllianceApplication::where('alliance_id', $allianceId)
            ->where('user_id', $userId)
            ->where('status', AllianceApplication::STATUS_PENDING)
            ->first();

        if ($existingApplication) {
            throw new Exception('You already have a pending application to this alliance');
        }

        // Create the application
        return AllianceApplication::create([
            'alliance_id' => $allianceId,
            'user_id' => $userId,
            'application_message' => $message,
            'status' => AllianceApplication::STATUS_PENDING,
        ]);
    }

    /**
     * Get pending applications for an alliance.
     *
     * @param int $allianceId
     * @return Collection<int, AllianceApplication>
     */
    public function getPendingApplications(int $allianceId): Collection
    {
        return AllianceApplication::where('alliance_id', $allianceId)
            ->where('status', AllianceApplication::STATUS_PENDING)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Accept an alliance application.
     *
     * @param int $applicationId
     * @param int $acceptingUserId
     * @return void
     * @throws Exception
     */
    public function acceptApplication(int $applicationId, int $acceptingUserId): void
    {
        $application = AllianceApplication::findOrFail($applicationId);

        // Verify the accepting user has permission
        $member = $this->getAllianceMember($application->alliance_id, $acceptingUserId);
        if (!$member || !$member->hasPermission(AllianceRank::PERMISSION_EDIT_APPLICATIONS)) {
            throw new Exception('You do not have permission to accept applications');
        }

        // Verify applicant is not already in an alliance
        $applicant = User::findOrFail($application->user_id);
        if ($applicant->alliance_id !== null) {
            throw new Exception('Applicant is already in an alliance');
        }

        DB::beginTransaction();
        try {
            // Accept the application
            $application->accept();
            $application->save();

            // Add user to alliance as member with newcomer rank (if exists)
            $newcomerRank = AllianceRank::where('alliance_id', $application->alliance_id)
                ->orderBy('sort_order', 'desc')
                ->first();

            AllianceMember::create([
                'alliance_id' => $application->alliance_id,
                'user_id' => $application->user_id,
                'rank_id' => $newcomerRank?->id,
                'joined_at' => now(),
            ]);

            // Update user's alliance_id
            /** @phpstan-ignore assign.propertyType */
            $applicant->alliance_id = $application->alliance_id;
            $applicant->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reject an alliance application.
     *
     * @param int $applicationId
     * @param int $rejectingUserId
     * @return void
     * @throws Exception
     */
    public function rejectApplication(int $applicationId, int $rejectingUserId): void
    {
        $application = AllianceApplication::findOrFail($applicationId);

        // Verify the rejecting user has permission
        $member = $this->getAllianceMember($application->alliance_id, $rejectingUserId);
        if (!$member || !$member->hasPermission(AllianceRank::PERMISSION_EDIT_APPLICATIONS)) {
            throw new Exception('You do not have permission to reject applications');
        }

        // Reject the application
        $application->reject();
        $application->save();
    }

    /**
     * Kick a member from the alliance.
     *
     * @param int $allianceId
     * @param int $memberUserId
     * @param int $kickingUserId
     * @return void
     * @throws Exception
     */
    public function kickMember(int $allianceId, int $memberUserId, int $kickingUserId): void
    {
        // Verify the kicking user has permission
        $kickingMember = $this->getAllianceMember($allianceId, $kickingUserId);
        if (!$kickingMember || !$kickingMember->hasPermission(AllianceRank::PERMISSION_KICK_USER)) {
            throw new Exception('You do not have permission to kick members');
        }

        // Get the member to kick
        $memberToKick = $this->getAllianceMember($allianceId, $memberUserId);
        if (!$memberToKick) {
            throw new Exception('Member not found in alliance');
        }

        // Cannot kick the founder
        if ($memberToKick->isFounder()) {
            throw new Exception('Cannot kick the alliance founder');
        }

        DB::beginTransaction();
        try {
            // Remove member
            $memberToKick->delete();

            // Update user's alliance_id
            $user = User::findOrFail($memberUserId);
            $user->alliance_id = null;
            $user->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Leave an alliance.
     *
     * @param int $userId
     * @return void
     * @throws Exception
     */
    public function leaveAlliance(int $userId): void
    {
        $user = User::findOrFail($userId);

        if ($user->alliance_id === null) {
            throw new Exception('User is not in an alliance');
        }

        $member = $this->getAllianceMember($user->alliance_id, $userId);
        if (!$member) {
            throw new Exception('Member not found in alliance');
        }

        // Founder cannot leave, must disband or transfer ownership
        if ($member->isFounder()) {
            throw new Exception('Founder cannot leave alliance. You must disband the alliance or transfer ownership.');
        }

        DB::beginTransaction();
        try {
            // Remove member
            $member->delete();

            // Update user's alliance_id
            $user->alliance_id = null;
            $user->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Create a new rank in the alliance.
     *
     * @param int $allianceId
     * @param string $rankName
     * @param array<int, string> $permissions
     * @param int $creatingUserId
     * @return AllianceRank
     * @throws Exception
     */
    public function createRank(int $allianceId, string $rankName, array $permissions, int $creatingUserId): AllianceRank
    {
        // Verify the creating user has permission
        $member = $this->getAllianceMember($allianceId, $creatingUserId);
        if (!$member || !$member->hasPermission(AllianceRank::PERMISSION_MANAGE_ALLY)) {
            throw new Exception('You do not have permission to create ranks');
        }

        // Get next sort order
        $maxSortOrder = AllianceRank::where('alliance_id', $allianceId)->max('sort_order') ?? 0;

        return AllianceRank::create([
            'alliance_id' => $allianceId,
            'rank_name' => $rankName,
            'permissions' => $permissions,
            'sort_order' => $maxSortOrder + 1,
        ]);
    }

    /**
     * Assign a rank to a member.
     *
     * @param int $allianceId
     * @param int $memberUserId
     * @param int|null $rankId
     * @param int $assigningUserId
     * @return void
     * @throws Exception
     */
    public function assignRank(int $allianceId, int $memberUserId, int|null $rankId, int $assigningUserId): void
    {
        // Verify the assigning user has permission
        $assigningMember = $this->getAllianceMember($allianceId, $assigningUserId);
        if (!$assigningMember || !$assigningMember->hasPermission(AllianceRank::PERMISSION_MANAGE_ALLY)) {
            throw new Exception('You do not have permission to assign ranks');
        }

        // Get the member
        $member = $this->getAllianceMember($allianceId, $memberUserId);
        if (!$member) {
            throw new Exception('Member not found in alliance');
        }

        // Cannot assign rank to founder
        if ($member->isFounder()) {
            throw new Exception('Cannot assign rank to founder');
        }

        // Update rank
        $member->rank_id = $rankId;
        $member->save();
    }

    /**
     * Update alliance texts.
     *
     * @param int $allianceId
     * @param int $updatingUserId
     * @param string|null $internalText
     * @param string|null $externalText
     * @param string|null $applicationText
     * @return void
     * @throws Exception
     */
    public function updateTexts(int $allianceId, int $updatingUserId, string|null $internalText, string|null $externalText, string|null $applicationText): void
    {
        // Verify the updating user has permission
        $member = $this->getAllianceMember($allianceId, $updatingUserId);
        if (!$member || !$member->hasPermission(AllianceRank::PERMISSION_MANAGE_ALLY)) {
            throw new Exception('You do not have permission to update alliance texts');
        }

        $alliance = Alliance::findOrFail($allianceId);
        $alliance->internal_text = $internalText;
        $alliance->external_text = $externalText;
        $alliance->application_text = $applicationText;
        $alliance->save();
    }

    /**
     * Disband an alliance.
     *
     * @param int $allianceId
     * @param int $disbandingUserId
     * @return void
     * @throws Exception
     */
    public function disbandAlliance(int $allianceId, int $disbandingUserId): void
    {
        // Verify the disbanding user has permission
        $member = $this->getAllianceMember($allianceId, $disbandingUserId);
        if (!$member || !$member->hasPermission(AllianceRank::PERMISSION_DELETE_ALLY)) {
            throw new Exception('You do not have permission to disband the alliance');
        }

        DB::beginTransaction();
        try {
            // Update all members' alliance_id to null
            $members = $this->getAllianceMembers($allianceId);
            foreach ($members as $allianceMember) {
                $user = User::find($allianceMember->user_id);
                if ($user) {
                    $user->alliance_id = null;
                    $user->save();
                }
            }

            // Delete the alliance (cascade will delete members, ranks, applications)
            Alliance::destroy($allianceId);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
