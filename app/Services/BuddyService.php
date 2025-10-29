<?php

namespace OGame\Services;

use Illuminate\Support\Carbon;
use OGame\Models\Buddy;
use OGame\Models\BuddyRequest;
use OGame\Models\User;

class BuddyService
{
    /**
     * Send a buddy request to another player
     *
     * @param int $senderId
     * @param int $receiverId
     * @param string|null $message
     * @return BuddyRequest|null
     */
    public static function sendRequest(int $senderId, int $receiverId, ?string $message = null): ?BuddyRequest
    {
        // Can't send request to yourself
        if ($senderId === $receiverId) {
            return null;
        }

        // Check if already buddies
        if (self::areBuddies($senderId, $receiverId)) {
            return null;
        }

        // Check if request already exists
        $existingRequest = BuddyRequest::where(function ($query) use ($senderId, $receiverId) {
            $query->where('sender_id', $senderId)->where('receiver_id', $receiverId);
        })->orWhere(function ($query) use ($senderId, $receiverId) {
            $query->where('sender_id', $receiverId)->where('receiver_id', $senderId);
        })->first();

        if ($existingRequest) {
            return null;
        }

        return BuddyRequest::create([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'message' => $message,
            'status' => 'pending',
        ]);
    }

    /**
     * Accept a buddy request
     *
     * @param int $requestId
     * @param int $userId
     * @return bool
     */
    public static function acceptRequest(int $requestId, int $userId): bool
    {
        $request = BuddyRequest::find($requestId);

        if (!$request || $request->receiver_id !== $userId || $request->status !== 'pending') {
            return false;
        }

        // Update request status
        $request->status = 'accepted';
        $request->save();

        // Create bidirectional buddy relationship
        $now = Carbon::now();

        Buddy::create([
            'user_id' => $request->sender_id,
            'buddy_id' => $request->receiver_id,
            'created_at' => $now,
        ]);

        Buddy::create([
            'user_id' => $request->receiver_id,
            'buddy_id' => $request->sender_id,
            'created_at' => $now,
        ]);

        return true;
    }

    /**
     * Reject a buddy request
     *
     * @param int $requestId
     * @param int $userId
     * @return bool
     */
    public static function rejectRequest(int $requestId, int $userId): bool
    {
        $request = BuddyRequest::find($requestId);

        if (!$request || $request->receiver_id !== $userId || $request->status !== 'pending') {
            return false;
        }

        $request->status = 'rejected';
        $request->save();

        return true;
    }

    /**
     * Cancel a sent buddy request
     *
     * @param int $requestId
     * @param int $userId
     * @return bool
     */
    public static function cancelRequest(int $requestId, int $userId): bool
    {
        $request = BuddyRequest::find($requestId);

        if (!$request || $request->sender_id !== $userId || $request->status !== 'pending') {
            return false;
        }

        $request->delete();

        return true;
    }

    /**
     * Remove a buddy
     *
     * @param int $userId
     * @param int $buddyId
     * @return bool
     */
    public static function removeBuddy(int $userId, int $buddyId): bool
    {
        // Delete both sides of the relationship
        Buddy::where('user_id', $userId)->where('buddy_id', $buddyId)->delete();
        Buddy::where('user_id', $buddyId)->where('buddy_id', $userId)->delete();

        // Delete the buddy request record
        BuddyRequest::where(function ($query) use ($userId, $buddyId) {
            $query->where('sender_id', $userId)->where('receiver_id', $buddyId);
        })->orWhere(function ($query) use ($userId, $buddyId) {
            $query->where('sender_id', $buddyId)->where('receiver_id', $userId);
        })->delete();

        return true;
    }

    /**
     * Check if two users are buddies
     *
     * @param int $userId1
     * @param int $userId2
     * @return bool
     */
    public static function areBuddies(int $userId1, int $userId2): bool
    {
        return Buddy::where('user_id', $userId1)->where('buddy_id', $userId2)->exists();
    }

    /**
     * Get all buddies for a user
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getBuddies(int $userId)
    {
        return Buddy::where('user_id', $userId)
            ->with('buddyUser')
            ->get();
    }

    /**
     * Get pending requests received by a user
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getPendingReceivedRequests(int $userId)
    {
        return BuddyRequest::where('receiver_id', $userId)
            ->where('status', 'pending')
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get pending requests sent by a user
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getPendingSentRequests(int $userId)
    {
        return BuddyRequest::where('sender_id', $userId)
            ->where('status', 'pending')
            ->with('receiver')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get count of new (unread) buddy requests
     *
     * @param int $userId
     * @return int
     */
    public static function getNewRequestCount(int $userId): int
    {
        return BuddyRequest::where('receiver_id', $userId)
            ->where('status', 'pending')
            ->count();
    }

    /**
     * Check if there's a pending buddy request between two users
     *
     * @param int $userId1
     * @param int $userId2
     * @return bool
     */
    public static function hasPendingRequest(int $userId1, int $userId2): bool
    {
        return BuddyRequest::where(function ($query) use ($userId1, $userId2) {
            $query->where('sender_id', $userId1)->where('receiver_id', $userId2);
        })->orWhere(function ($query) use ($userId1, $userId2) {
            $query->where('sender_id', $userId2)->where('receiver_id', $userId1);
        })->where('status', 'pending')
        ->exists();
    }
}
