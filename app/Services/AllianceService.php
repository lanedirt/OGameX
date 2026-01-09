<?php

namespace OGame\Services;

use Log;
use OGame\GameMessages\AllianceBroadcast;
use OGame\GameMessages\AllianceApplicationReceived;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use OGame\Models\Alliance;
use OGame\Models\AllianceApplication;
use OGame\Models\AllianceMember;
use OGame\Models\AllianceRank;
use OGame\Models\Message;
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
     * AllianceService constructor.
     *
     * @param SettingsService $settingsService
     * @param MessageService $messageService
     */
    public function __construct(
        private SettingsService $settingsService,
        private MessageService $messageService
    ) {
    }

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

        // Check if user left an alliance recently (configurable cooldown)
        if ($user->alliance_left_at !== null) {
            $cooldownDays = $this->settingsService->allianceCooldownDays();
            $cooldownEnd = $user->alliance_left_at->addDays($cooldownDays);
            if (now()->isBefore($cooldownEnd)) {
                $remainingHours = now()->diffInHours($cooldownEnd);
                $remainingDays = ceil($remainingHours / 24);
                throw new Exception("You must wait {$remainingDays} more day(s) before you can create or join an alliance.");
            }
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

            // Update user's alliance_id and clear cooldown
            /** @phpstan-ignore assign.propertyType */
            $user->alliance_id = $alliance->id;
            $user->alliance_left_at = null;
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
            ->with(['user.highscore', 'rank'])
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

        // Check if user left an alliance recently (configurable cooldown)
        if ($user->alliance_left_at !== null) {
            $cooldownDays = $this->settingsService->allianceCooldownDays();
            $cooldownEnd = $user->alliance_left_at->addDays($cooldownDays);
            if (now()->isBefore($cooldownEnd)) {
                $remainingHours = now()->diffInHours($cooldownEnd);
                $remainingDays = ceil($remainingHours / 24);
                throw new Exception("You must wait {$remainingDays} more day(s) before you can create or join an alliance.");
            }
        }

        // Validate that alliance exists and is open
        $alliance = Alliance::findOrFail($allianceId);
        if (!$alliance->is_open) {
            throw new Exception('Alliance is not accepting applications');
        }

        // Check if user already has an application (any status)
        $existingApplication = AllianceApplication::where('alliance_id', $allianceId)
            ->where('user_id', $userId)
            ->first();

        if ($existingApplication) {
            // If there's a pending application, don't allow a new one
            if ($existingApplication->status === AllianceApplication::STATUS_PENDING) {
                throw new Exception('You already have a pending application to this alliance');
            }

            // If there's an old rejected/accepted application, delete it first
            $existingApplication->delete();
        }

        // Create the application
        $application = AllianceApplication::create([
            'alliance_id' => $allianceId,
            'user_id' => $userId,
            'application_message' => $message,
            'status' => AllianceApplication::STATUS_PENDING,
        ]);

        // Send notification to all alliance members with permission to manage applications
        $this->notifyApplicationReceivers($alliance, $user, $message);

        return $application;
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

            // Update user's alliance_id and clear cooldown
            /** @phpstan-ignore assign.propertyType */
            $applicant->alliance_id = $application->alliance_id;
            $applicant->alliance_left_at = null;
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

            // Update user's alliance_id and set cooldown
            $user->alliance_id = null;
            $user->alliance_left_at = now();
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
     * Update rank permissions.
     *
     * @param int $allianceId
     * @param array $rankPermissions Array with rank IDs as keys and permission bitmasks as values
     * @param int $updatingUserId
     * @return void
     * @throws Exception
     */
    public function updateRankPermissions(int $allianceId, array $rankPermissions, int $updatingUserId): void
    {
        // Verify the updating user has permission
        $member = $this->getAllianceMember($allianceId, $updatingUserId);
        if (!$member || !$member->hasPermission(AllianceRank::PERMISSION_MANAGE_ALLY)) {
            throw new Exception('You do not have permission to update rank permissions');
        }

        foreach ($rankPermissions as $rankId => $permissionsBitmask) {
            $rank = AllianceRank::where('alliance_id', $allianceId)
                ->where('id', $rankId)
                ->first();

            if (!$rank) {
                continue; // Skip if rank doesn't exist
            }

            // Convert bitmask to array of permission strings
            $permissions = $this->convertBitmaskToPermissions($permissionsBitmask);

            // Update permissions
            $rank->permissions = $permissions;
            $rank->save();
        }
    }

    /**
     * Convert permission bitmask to array of permission strings.
     *
     * @param int $bitmask
     * @return array
     */
    private function convertBitmaskToPermissions(int $bitmask): array
    {
        $permissions = [];
        $permissionMap = [
            1 => AllianceRank::PERMISSION_SEE_APPLICATIONS,
            2 => AllianceRank::PERMISSION_EDIT_APPLICATIONS,
            4 => AllianceRank::PERMISSION_SEE_MEMBERS,
            8 => AllianceRank::PERMISSION_KICK_USER,
            16 => AllianceRank::PERMISSION_SEE_MEMBER_ONLINE_STATUS,
            32 => AllianceRank::PERMISSION_SEND_CIRCULAR_MSG,
            64 => AllianceRank::PERMISSION_DELETE_ALLY,
            128 => AllianceRank::PERMISSION_MANAGE_ALLY,
            256 => AllianceRank::PERMISSION_RIGHT_HAND,
            2048 => AllianceRank::PERMISSION_MANAGE_CLASSES,
        ];

        foreach ($permissionMap as $bit => $permission) {
            if (($bitmask & $bit) === $bit) {
                $permissions[] = $permission;
            }
        }

        return $permissions;
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
     * Update alliance settings.
     *
     * @param int $allianceId
     * @param int $updatingUserId
     * @param array $settings
     * @return void
     * @throws Exception
     */
    public function updateSettings(int $allianceId, int $updatingUserId, array $settings): void
    {
        // Verify the updating user has permission
        $member = $this->getAllianceMember($allianceId, $updatingUserId);
        if (!$member || !$member->hasPermission(AllianceRank::PERMISSION_MANAGE_ALLY)) {
            throw new Exception('You do not have permission to update alliance settings');
        }

        $alliance = Alliance::findOrFail($allianceId);

        if (isset($settings['homepage'])) {
            $alliance->homepage_url = $settings['homepage'];
        }
        if (isset($settings['logo_url'])) {
            $alliance->logo_url = $settings['logo_url'];
        }
        if (isset($settings['open'])) {
            $alliance->is_open = (bool)$settings['open'];
        }
        if (isset($settings['founder_rank_name'])) {
            $alliance->founder_rank_name = $settings['founder_rank_name'];
        }
        if (isset($settings['newcomer_rank_name'])) {
            $alliance->newcomer_rank_name = $settings['newcomer_rank_name'];
        }

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

    /**
     * Update alliance tag.
     *
     * @param int $allianceId
     * @param int $updatingUserId
     * @param string $newTag
     * @return void
     * @throws Exception
     */
    public function updateTag(int $allianceId, int $updatingUserId, string $newTag): void
    {
        // Verify the updating user has permission
        $member = $this->getAllianceMember($allianceId, $updatingUserId);
        if (!$member || !$member->hasPermission(AllianceRank::PERMISSION_MANAGE_ALLY)) {
            throw new Exception('You do not have permission to update alliance tag');
        }

        // Validate tag length (3-8 characters)
        if (strlen($newTag) < 3 || strlen($newTag) > 8) {
            throw new Exception('Alliance tag must be between 3 and 8 characters');
        }

        // Check if tag is already taken by another alliance
        $existingAlliance = Alliance::where('alliance_tag', $newTag)
            ->where('id', '!=', $allianceId)
            ->first();
        if ($existingAlliance) {
            throw new Exception('Alliance tag is already taken');
        }

        $alliance = Alliance::findOrFail($allianceId);
        $alliance->alliance_tag = $newTag;
        $alliance->save();
    }

    /**
     * Update alliance name.
     *
     * @param int $allianceId
     * @param int $updatingUserId
     * @param string $newName
     * @return void
     * @throws Exception
     */
    public function updateName(int $allianceId, int $updatingUserId, string $newName): void
    {
        // Verify the updating user has permission
        $member = $this->getAllianceMember($allianceId, $updatingUserId);
        if (!$member || !$member->hasPermission(AllianceRank::PERMISSION_MANAGE_ALLY)) {
            throw new Exception('You do not have permission to update alliance name');
        }

        // Validate name length (3-30 characters)
        if (strlen($newName) < 3 || strlen($newName) > 30) {
            throw new Exception('Alliance name must be between 3 and 30 characters');
        }

        // Check if name is already taken by another alliance
        $existingAlliance = Alliance::where('alliance_name', $newName)
            ->where('id', '!=', $allianceId)
            ->first();
        if ($existingAlliance) {
            throw new Exception('Alliance name is already taken');
        }

        $alliance = Alliance::findOrFail($allianceId);
        $alliance->alliance_name = $newName;
        $alliance->save();
    }

    /**
     * Send a broadcast message to alliance members
     *
     * @param int $allianceId
     * @param int $sendingUserId
     * @param string $text
     * @param array $recipients Array of rank IDs or -1 for all members
     * @return void
     * @throws Exception
     */
    public function sendBroadcastMessage(int $allianceId, int $sendingUserId, string $text, array $recipients): void
    {
        // Verify the sending user has permission
        $member = $this->getAllianceMember($allianceId, $sendingUserId);
        if (!$member || !$member->hasPermission(AllianceRank::PERMISSION_SEND_CIRCULAR_MSG)) {
            throw new Exception('You do not have permission to send broadcast messages');
        }

        // Validate message length
        if (strlen($text) > 2000) {
            throw new Exception('Message is too long (maximum 2000 characters)');
        }

        if (empty($text) || trim($text) === '') {
            throw new Exception('Message cannot be empty');
        }

        $alliance = Alliance::findOrFail($allianceId);
        $members = $alliance->members;

        Log::info('Alliance broadcast - members before filter', [
            'total_members' => $members->count(),
            'sender_id' => $sendingUserId,
        ]);

        // Filter members based on recipients
        if (!in_array('-1', $recipients)) {
            // Filter by specific ranks
            $members = $members->filter(function($allianceMember) use ($recipients) {
                return in_array($allianceMember->rank_id, $recipients);
            });
        }

        // Send message to each recipient
        $senderPlayer = resolve(PlayerService::class, ['player_id' => $sendingUserId]);

        $messageCount = 0;
        foreach ($members as $allianceMember) {
            Log::info('Processing member for broadcast', [
                'member_id' => $allianceMember->id,
                'user_id' => $allianceMember->user_id,
                'sender_id' => $sendingUserId,
            ]);

            if ($allianceMember->user_id === $sendingUserId) {
                Log::info('Skipping sender');
                continue; // Don't send to self
            }

            $recipientPlayer = resolve(PlayerService::class, ['player_id' => $allianceMember->user_id]);
            $messageService = resolve(MessageService::class, ['player' => $recipientPlayer]);

            Log::info('Sending broadcast message', [
                'recipient_id' => $recipientPlayer->getId(),
                'recipient_name' => $recipientPlayer->getUsername(),
            ]);

            // Create the broadcast message using the MessageService
            $messageService->sendSystemMessageToPlayer(
                $recipientPlayer,
                AllianceBroadcast::class,
                [
                    'sender_name' => $senderPlayer->getUsername(),
                    'alliance_tag' => $alliance->alliance_tag,
                    'message' => $text,
                ]
            );
            $messageCount++;
        }

        Log::info('Broadcast messages created', [
            'count' => $messageCount,
            'alliance_id' => $allianceId,
            'sender_id' => $sendingUserId,
        ]);
    }

    /**
     * Send notification to all alliance members who can manage applications.
     *
     * @param Alliance $alliance
     * @param User $applicant
     * @param string|null $applicationMessage
     * @return void
     */
    private function notifyApplicationReceivers(Alliance $alliance, User $applicant, string|null $applicationMessage): void
    {
        try {
            // Get all alliance members with their ranks
            $members = AllianceMember::where('alliance_id', $alliance->id)
                ->with(['user', 'rank'])
                ->get();

            foreach ($members as $member) {
                // Check if this member has permission to manage applications
                if ($member->hasPermission(AllianceRank::PERMISSION_EDIT_APPLICATIONS)) {
                    try {
                        // Send notification message
                        $playerService = resolve(PlayerService::class, ['player_id' => $member->user_id]);

                        $this->messageService->sendSystemMessageToPlayer(
                            $playerService,
                            AllianceApplicationReceived::class,
                            [
                                'applicant_name' => $applicant->username,
                                'application_message' => $applicationMessage ?? '',
                            ]
                        );
                    } catch (Exception $e) {
                        // Log error but don't fail the entire application process
                        Log::error('Failed to send alliance application notification', [
                            'member_id' => $member->user_id,
                            'alliance_id' => $alliance->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }
        } catch (Exception $e) {
            // Log error but don't fail the application creation
            Log::error('Failed to notify alliance members of application', [
                'alliance_id' => $alliance->id,
                'applicant_id' => $applicant->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
