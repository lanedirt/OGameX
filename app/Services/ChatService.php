<?php

namespace OGame\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use OGame\Events\ChatMessageSent;
use OGame\Models\ChatMessage;
use OGame\Models\IgnoredPlayer;
use OGame\Models\User;

/**
 * Class ChatService.
 *
 * Chat Service - handles all chat messaging logic.
 *
 * @package OGame\Services
 */
class ChatService
{
    /**
     * Send a direct message to another player.
     */
    public function sendDirectMessage(int $senderId, int $recipientId, string $message, int|null $replyToId = null): ChatMessage
    {
        $chatMessage = ChatMessage::create([
            'sender_id' => $senderId,
            'recipient_id' => $recipientId,
            'message' => $message,
            'reply_to_id' => $replyToId,
        ]);

        $chatMessage->load(['sender', 'replyTo.sender']);

        broadcast(new ChatMessageSent($chatMessage))->toOthers();

        return $chatMessage;
    }

    /**
     * Send a message to an alliance chat.
     */
    public function sendAllianceMessage(int $senderId, int $allianceId, string $message, int|null $replyToId = null): ChatMessage
    {
        $chatMessage = ChatMessage::create([
            'sender_id' => $senderId,
            'alliance_id' => $allianceId,
            'message' => $message,
            'reply_to_id' => $replyToId,
        ]);

        $chatMessage->load(['sender', 'replyTo.sender']);

        broadcast(new ChatMessageSent($chatMessage))->toOthers();

        return $chatMessage;
    }

    /**
     * Get conversation history between two players.
     *
     * @return Collection<int, ChatMessage>
     */
    public function getConversation(int $userId, int $partnerId, int $limit = 50, int|null $beforeId = null): Collection
    {
        $query = ChatMessage::where(function ($q) use ($userId, $partnerId) {
            $q->where(function ($q2) use ($userId, $partnerId) {
                $q2->where('sender_id', $userId)->where('recipient_id', $partnerId);
            })->orWhere(function ($q2) use ($userId, $partnerId) {
                $q2->where('sender_id', $partnerId)->where('recipient_id', $userId);
            });
        })->with(['sender', 'replyTo.sender']);

        if ($beforeId) {
            $query->where('id', '<', $beforeId);
        }

        return $query->orderBy('id', 'desc')->limit($limit)->get()->reverse()->values();
    }

    /**
     * Get alliance chat message history.
     *
     * @return Collection<int, ChatMessage>
     */
    public function getAllianceMessages(int $allianceId, int $limit = 50, int|null $beforeId = null): Collection
    {
        $query = ChatMessage::where('alliance_id', $allianceId)
            ->with(['sender', 'replyTo.sender']);

        if ($beforeId) {
            $query->where('id', '<', $beforeId);
        }

        return $query->orderBy('id', 'desc')->limit($limit)->get()->reverse()->values();
    }

    /**
     * Mark all messages from a partner as read.
     */
    public function markAsRead(int $userId, int $partnerId): void
    {
        ChatMessage::where('sender_id', $partnerId)
            ->where('recipient_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Mark all alliance messages as read for a user.
     * (Uses the read_at on sender's own view - we track per-message read status)
     */
    public function markAllianceAsRead(int $allianceId): void
    {
        // Alliance messages don't have per-user read tracking in this schema.
        // The frontend handles unread counts via cookies/local state.
    }

    /**
     * Get unread message counts per sender for a user.
     *
     * @return array<int, int> Map of sender_id => unread count
     */
    public function getUnreadCounts(int $userId): array
    {
        return ChatMessage::where('recipient_id', $userId)
            ->whereNull('read_at')
            ->selectRaw('sender_id, COUNT(*) as count')
            ->groupBy('sender_id')
            ->pluck('count', 'sender_id')
            ->toArray();
    }

    /**
     * Get the number of conversations with unread messages for a user.
     */
    public function getUnreadConversationCount(int $userId): int
    {
        return ChatMessage::where('recipient_id', $userId)
            ->whereNull('read_at')
            ->distinct('sender_id')
            ->count('sender_id');
    }

    /**
     * Get the total number of unread chat messages for a user.
     */
    public function getTotalUnreadMessageCount(int $userId): int
    {
        return ChatMessage::where('recipient_id', $userId)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Get recent conversations for a user (unique chat partners with last message info).
     *
     * @return array<int, array{partner_id: int, partner_name: string, last_message: string, last_message_date: Carbon, unread_count: int}>
     */
    public function getRecentConversations(int $userId): array
    {
        // Get all direct messages involving this user, ordered by most recent
        $messages = ChatMessage::where(function ($q) use ($userId) {
            $q->where('sender_id', $userId)->orWhere('recipient_id', $userId);
        })
            ->whereNull('alliance_id')
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->get();

        $unreadCounts = $this->getUnreadCounts($userId);
        $conversations = [];

        foreach ($messages as $message) {
            $partnerId = $message->sender_id === $userId ? $message->recipient_id : $message->sender_id;

            if (isset($conversations[$partnerId])) {
                continue;
            }

            $partner = $message->sender_id === $partnerId ? $message->sender : User::find($partnerId);

            $conversations[$partnerId] = [
                'partner_id' => $partnerId,
                'partner_name' => $partner->username ?? 'Unknown',
                'last_message' => $message->message,
                'last_message_date' => $message->created_at,
                'unread_count' => $unreadCounts[$partnerId] ?? 0,
            ];
        }

        return array_values($conversations);
    }

    /**
     * Check if a player can message another player (not ignored).
     */
    public function canMessagePlayer(int $senderId, int $recipientId): bool
    {
        // Check if sender is ignored by recipient
        return !IgnoredPlayer::where('user_id', $recipientId)
            ->where('ignored_user_id', $senderId)
            ->exists();
    }

    /**
     * Format chat messages for the frontend response.
     *
     * @param Collection<int, ChatMessage> $messages
     * @return array{chatItems: array<int|string, array<string, mixed>>, chatItemsByDateAsc: list<numeric-string>}
     */
    public function formatMessagesForFrontend(Collection $messages, int $currentPlayerId): array
    {
        $chatItems = [];
        $chatItemsByDateAsc = [];

        foreach ($messages as $message) {
            $key = (string) $message->id;
            $isOwnMessage = $message->sender_id === $currentPlayerId;

            $item = [
                'date' => $message->created_at->timestamp,
                'newClass' => '',
                'playerName' => $message->sender->username,
                'altClass' => $isOwnMessage ? 'odd' : '',
                'chatID' => $message->id,
                'chatContent' => e($message->message),
            ];

            // Add reply reference data if present
            if ($message->replyTo) {
                $item['refData'] = [
                    'author' => $message->replyTo->sender->username ?? 'Unknown',
                    'text' => e($message->replyTo->message),
                ];
            }

            $chatItems[$key] = $item;
            $chatItemsByDateAsc[] = $key;
        }

        return [
            'chatItems' => $chatItems,
            'chatItemsByDateAsc' => $chatItemsByDateAsc,
        ];
    }
}
