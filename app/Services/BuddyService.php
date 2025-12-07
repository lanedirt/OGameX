<?php

namespace OGame\Services;

use Illuminate\Database\Eloquent\Collection;
use OGame\GameMessages\BuddyRemoved;
use OGame\GameMessages\BuddyRequestAccepted;
use OGame\GameMessages\BuddyRequestReceived;
use OGame\Models\BuddyRequest;
use OGame\Models\IgnoredPlayer;
use OGame\Models\User;

/**
 * Class BuddyService.
 *
 * Buddy Service - handles all buddy/friend request related logic.
 *
 * @package OGame\Services
 */
class BuddyService
{
    private MessageService $messageService;

    /**
     * BuddyService constructor.
     *
     * @param MessageService $messageService
     */
    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }
    /**
     * Get all buddies (accepted buddy requests) for a user.
     *
     * @param int $userId
     * @return Collection<int, BuddyRequest>
     */
    public function getBuddies(int $userId): Collection
    {
        return BuddyRequest::where('status', BuddyRequest::STATUS_ACCEPTED)
            ->where(function ($query) use ($userId) {
                $query->where('sender_user_id', $userId)
                    ->orWhere('receiver_user_id', $userId);
            })
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get pending buddy requests received by a user.
     *
     * @param int $userId
     * @return Collection<int, BuddyRequest>
     */
    public function getReceivedRequests(int $userId): Collection
    {
        return BuddyRequest::where('receiver_user_id', $userId)
            ->where('status', BuddyRequest::STATUS_PENDING)
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get pending buddy requests sent by a user.
     *
     * @param int $userId
     * @return Collection<int, BuddyRequest>
     */
    public function getSentRequests(int $userId): Collection
    {
        return BuddyRequest::where('sender_user_id', $userId)
            ->where('status', BuddyRequest::STATUS_PENDING)
            ->with('receiver')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get count of unread (new) buddy requests received by a user.
     *
     * @param int $userId
     * @return int
     */
    public function getUnreadRequestsCount(int $userId): int
    {
        return BuddyRequest::where('receiver_user_id', $userId)
            ->where('status', BuddyRequest::STATUS_PENDING)
            ->where('viewed', false)
            ->count();
    }

    /**
     * Send a buddy request.
     *
     * @param int $senderId
     * @param int $receiverId
     * @param string|null $message
     * @return BuddyRequest
     * @throws \Exception
     */
    public function sendRequest(int $senderId, int $receiverId, ?string $message = null): BuddyRequest
    {
        // Check if users are the same
        if ($senderId === $receiverId) {
            throw new \Exception('Cannot send buddy request to yourself.');
        }

        // Check if sender and receiver exist
        $sender = User::find($senderId);
        $receiver = User::find($receiverId);
        if (!$receiver || !$sender) {
            throw new \Exception('User not found.');
        }

        // Check if receiver is admin (cannot send buddy requests to admins)
        $receiverPlayer = app(\OGame\Services\PlayerService::class, ['player_id' => $receiver->id]);
        if ($receiverPlayer->isAdmin()) {
            throw new \Exception('Cannot send buddy requests to administrators.');
        }

        // Check if there's already a pending or accepted request between these users
        $existingRequest = BuddyRequest::where(function ($query) use ($senderId, $receiverId) {
            $query->where('sender_user_id', $senderId)
                ->where('receiver_user_id', $receiverId);
        })
        ->orWhere(function ($query) use ($senderId, $receiverId) {
            $query->where('sender_user_id', $receiverId)
                ->where('receiver_user_id', $senderId);
        })
        ->whereIn('status', [BuddyRequest::STATUS_PENDING, BuddyRequest::STATUS_ACCEPTED])
        ->first();

        if ($existingRequest) {
            if ($existingRequest->isAccepted()) {
                throw new \Exception('You are already buddies with this user.');
            }
            throw new \Exception('A buddy request already exists between these users.');
        }

        // Create the buddy request
        $buddyRequest = BuddyRequest::create([
            'sender_user_id' => $senderId,
            'receiver_user_id' => $receiverId,
            'status' => BuddyRequest::STATUS_PENDING,
            'message' => $message,
            'viewed' => false,
        ]);

        // Send a system message to the receiver
        $receiverPlayer = app(\OGame\Services\PlayerService::class, ['player_id' => $receiver->id]);
        $this->messageService->sendSystemMessageToPlayer($receiverPlayer, BuddyRequestReceived::class, [
            'sender_name' => $sender->username,
        ]);

        return $buddyRequest;
    }

    /**
     * Accept a buddy request.
     *
     * @param int $requestId
     * @param int $userId
     * @return bool
     * @throws \Exception
     */
    public function acceptRequest(int $requestId, int $userId): bool
    {
        $request = BuddyRequest::find($requestId);

        if (!$request) {
            throw new \Exception('Buddy request not found.');
        }

        // Only the receiver can accept the request
        if ($request->receiver_user_id !== $userId) {
            throw new \Exception('You are not authorized to accept this request.');
        }

        if (!$request->isPending()) {
            throw new \Exception('This request has already been processed.');
        }

        $request->status = BuddyRequest::STATUS_ACCEPTED;
        $request->viewed = true;
        $success = $request->save();

        if ($success) {
            // Get user info to send notification
            $accepter = User::find($userId);
            $sender = User::find($request->sender_user_id);

            if ($accepter && $sender) {
                // Send a system message to the original sender
                $senderPlayer = app(\OGame\Services\PlayerService::class, ['player_id' => $sender->id]);
                $this->messageService->sendSystemMessageToPlayer($senderPlayer, BuddyRequestAccepted::class, [
                    'accepter_name' => $accepter->username,
                ]);
            }
        }

        return $success;
    }

    /**
     * Reject a buddy request.
     *
     * @param int $requestId
     * @param int $userId
     * @return bool
     * @throws \Exception
     */
    public function rejectRequest(int $requestId, int $userId): bool
    {
        $request = BuddyRequest::find($requestId);

        if (!$request) {
            throw new \Exception('Buddy request not found.');
        }

        // Only the receiver can reject the request
        if ($request->receiver_user_id !== $userId) {
            throw new \Exception('You are not authorized to reject this request.');
        }

        if (!$request->isPending()) {
            throw new \Exception('This request has already been processed.');
        }

        // Delete the request instead of marking it as rejected
        return $request->delete();
    }

    /**
     * Cancel (withdraw) a sent buddy request.
     *
     * @param int $requestId
     * @param int $userId
     * @return bool
     * @throws \Exception
     */
    public function cancelRequest(int $requestId, int $userId): bool
    {
        $request = BuddyRequest::find($requestId);

        if (!$request) {
            throw new \Exception('Buddy request not found.');
        }

        // Only the sender can cancel the request
        if ($request->sender_user_id !== $userId) {
            throw new \Exception('You are not authorized to cancel this request.');
        }

        if (!$request->isPending()) {
            throw new \Exception('This request has already been processed.');
        }

        return $request->delete();
    }

    /**
     * Delete a buddy (remove accepted buddy relationship).
     *
     * @param int $buddyUserId - The ID of the buddy to remove
     * @param int $userId - The ID of the current user
     * @return bool
     * @throws \Exception
     */
    public function deleteBuddy(int $buddyUserId, int $userId): bool
    {
        $request = BuddyRequest::where('status', BuddyRequest::STATUS_ACCEPTED)
            ->where(function ($query) use ($userId, $buddyUserId) {
                $query->where(function ($q) use ($userId, $buddyUserId) {
                    $q->where('sender_user_id', $userId)
                        ->where('receiver_user_id', $buddyUserId);
                })
                ->orWhere(function ($q) use ($userId, $buddyUserId) {
                    $q->where('sender_user_id', $buddyUserId)
                        ->where('receiver_user_id', $userId);
                });
            })
            ->first();

        if (!$request) {
            throw new \Exception('Buddy relationship not found.');
        }

        // Get user info before deleting the request
        $currentUser = User::find($userId);
        $buddyUser = User::find($buddyUserId);

        if ($currentUser && $buddyUser) {
            // Send a system message to the removed buddy
            $buddyPlayer = app(\OGame\Services\PlayerService::class, ['player_id' => $buddyUser->id]);
            $this->messageService->sendSystemMessageToPlayer($buddyPlayer, BuddyRemoved::class, [
                'remover_name' => $currentUser->username,
            ]);
        }

        return $request->delete();
    }

    /**
     * Search for users to add as buddies.
     *
     * @param string $searchTerm
     * @param int $currentUserId
     * @param int $limit
     * @return Collection<int, User>
     */
    public function searchUsers(string $searchTerm, int $currentUserId, int $limit = 20): Collection
    {
        return User::where('username', 'like', '%' . $searchTerm . '%')
            ->where('id', '!=', $currentUserId)
            ->limit($limit)
            ->get();
    }

    /**
     * Mark received buddy requests as viewed.
     *
     * @param int $userId
     * @return int Number of requests marked as viewed
     */
    public function markRequestsAsViewed(int $userId): int
    {
        return BuddyRequest::where('receiver_user_id', $userId)
            ->where('status', BuddyRequest::STATUS_PENDING)
            ->where('viewed', false)
            ->update(['viewed' => true]);
    }

    /**
     * Get all ignored players for a user.
     *
     * @param int $userId
     * @return Collection<int, IgnoredPlayer>
     */
    public function getIgnoredPlayers(int $userId): Collection
    {
        return IgnoredPlayer::where('user_id', $userId)
            ->with('ignoredUser')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Ignore a player.
     *
     * @param int $userId
     * @param int $ignoredUserId
     * @return IgnoredPlayer
     * @throws \Exception
     */
    public function ignorePlayer(int $userId, int $ignoredUserId): IgnoredPlayer
    {
        // Check if users are the same
        if ($userId === $ignoredUserId) {
            throw new \Exception('Cannot ignore yourself.');
        }

        // Check if already ignored
        $existing = IgnoredPlayer::where('user_id', $userId)
            ->where('ignored_user_id', $ignoredUserId)
            ->first();

        if ($existing) {
            throw new \Exception('Player is already ignored.');
        }

        return IgnoredPlayer::create([
            'user_id' => $userId,
            'ignored_user_id' => $ignoredUserId,
        ]);
    }

    /**
     * Unignore a player.
     *
     * @param int $userId
     * @param int $ignoredUserId
     * @return bool
     * @throws \Exception
     */
    public function unignorePlayer(int $userId, int $ignoredUserId): bool
    {
        $ignoredPlayer = IgnoredPlayer::where('user_id', $userId)
            ->where('ignored_user_id', $ignoredUserId)
            ->first();

        if (!$ignoredPlayer) {
            throw new \Exception('Player is not in your ignored list.');
        }

        return $ignoredPlayer->delete();
    }

    /**
     * Check if a user is ignored by another user.
     *
     * @param int $userId
     * @param int $ignoredUserId
     * @return bool
     */
    public function isPlayerIgnored(int $userId, int $ignoredUserId): bool
    {
        return IgnoredPlayer::where('user_id', $userId)
            ->where('ignored_user_id', $ignoredUserId)
            ->exists();
    }
}
