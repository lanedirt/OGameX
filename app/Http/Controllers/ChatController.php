<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Models\Alliance;
use OGame\Models\ChatMessage;
use OGame\Models\Enums\PlanetType;
use OGame\Models\Highscore;
use OGame\Models\Planet;
use OGame\Models\User;
use OGame\Services\BuddyService;
use OGame\Services\ChatService;

class ChatController extends OGameController
{
    /**
     * Show the dedicated chat page.
     */
    public function index(Request $request, ChatService $chatService, BuddyService $buddyService): View
    {
        $this->setBodyId('chat');

        $userId = (int) auth()->id();
        $user = User::find($userId);

        // Get buddy list and resolve buddy User objects
        $buddyRequests = $buddyService->getBuddies($userId);
        $buddyUsers = $buddyRequests->map(function ($req) use ($userId) {
            return $req->sender_user_id === $userId ? $req->receiver : $req->sender;
        });
        $buddyUserIds = $buddyUsers->pluck('id')->toArray();

        // Get alliance members (if user is in an alliance)
        $allianceMembers = collect();
        $alliance = null;
        $allianceMemberIds = [];
        if ($user && $user->alliance_id) {
            /** @var Alliance $alliance */
            $alliance = $user->alliance;
            $allianceMembers = $alliance->members()->where('user_id', '!=', $userId)->with('user')->get();
            $allianceMemberIds = $allianceMembers->pluck('user_id')->toArray();
        }

        // Get recent direct message conversations
        $conversations = $chatService->getRecentConversations($userId);

        // Build strangers list: conversation partners who are not buddies or alliance members
        $knownIds = array_merge($buddyUserIds, $allianceMemberIds);
        $strangers = collect($conversations)->filter(function ($conv) use ($knownIds) {
            return !in_array($conv['partner_id'], $knownIds);
        })->map(function ($conv) {
            return (object) ['id' => $conv['partner_id'], 'username' => $conv['partner_name']];
        })->values();

        // Get latest alliance message for the chat list preview
        $latestAllianceMessage = null;
        if ($alliance) {
            $latestAllianceMessage = ChatMessage::where('alliance_id', $alliance->id)
                ->with('sender')
                ->orderBy('created_at', 'desc')
                ->first();
        }

        $viewData = [
            'conversations' => $conversations,
            'buddyUsers' => $buddyUsers,
            'allianceMembers' => $allianceMembers,
            'alliance' => $alliance,
            'latestAllianceMessage' => $latestAllianceMessage,
            'strangers' => $strangers,
            'chatPartner' => null,
            'chatMessages' => collect(),
        ];

        // If a playerId is specified, load the chat thread with that player
        $chatPlayerId = $request->query('playerId') ? (int) $request->query('playerId') : null;
        if ($chatPlayerId) {
            $chatPartner = User::find($chatPlayerId);
            if ($chatPartner) {
                $messages = $chatService->getConversation($userId, $chatPlayerId);
                $chatService->markAsRead($userId, $chatPlayerId);

                $viewData['chatPartner'] = $chatPartner;
                $viewData['chatMessages'] = $messages;
                $viewData['isBuddy'] = in_array($chatPlayerId, $buddyUserIds);
                $viewData['isAllianceMember'] = in_array($chatPlayerId, $allianceMemberIds);

                // Get chat partner's alliance, highscore rank, and home planet for the header
                $viewData['chatPartnerAlliance'] = $chatPartner->alliance_id ? $chatPartner->alliance : null;
                $highscoreEntry = Highscore::where('player_id', $chatPlayerId)->first();
                $viewData['chatPartnerRank'] = $highscoreEntry->general_rank ?? 0;
                $chatPartnerPlanet = Planet::where('user_id', $chatPlayerId)
                    ->where('planet_type', PlanetType::Planet->value)
                    ->orderBy('id')
                    ->first();
                $viewData['chatPartnerPlanet'] = $chatPartnerPlanet;

                // Calculate planet image path from coordinates (same logic as PlanetService)
                if ($chatPartnerPlanet) {
                    $position = $chatPartnerPlanet->planet;
                    $system = $chatPartnerPlanet->system;

                    // Biome type
                    $biomeMap = [
                        1 => ['odd' => 'dry', 'even' => 'desert'],
                        2 => ['odd' => 'dry', 'even' => 'desert'],
                        3 => ['odd' => 'dry', 'even' => 'desert'],
                        4 => ['odd' => 'normal', 'even' => 'dry'],
                        5 => ['odd' => 'normal', 'even' => 'dry'],
                        6 => ['odd' => 'jungle', 'even' => 'normal'],
                        7 => ['odd' => 'jungle', 'even' => 'normal'],
                        8 => ['odd' => 'water', 'even' => 'jungle'],
                        9 => ['odd' => 'water', 'even' => 'jungle'],
                        10 => ['odd' => 'ice', 'even' => 'water'],
                        11 => ['odd' => 'ice', 'even' => 'water'],
                        12 => ['odd' => 'gas', 'even' => 'ice'],
                        13 => ['odd' => 'gas', 'even' => 'ice'],
                        14 => ['odd' => 'normal', 'even' => 'gas'],
                        15 => ['odd' => 'normal', 'even' => 'gas'],
                    ];
                    $oddEven = ($system % 2 === 0) ? 'even' : 'odd';
                    $biome = $biomeMap[$position][$oddEven] ?? 'normal';

                    // Image type
                    $typeMap = [1 => 3, 2 => 4, 3 => 5, 4 => 6, 5 => 7, 6 => 8, 7 => 9, 8 => 10, 9 => 1, 10 => 2, 11 => 3, 12 => 4, 13 => 5, 14 => 6, 15 => 7];
                    $baseType = $typeMap[$position] ?? 1;
                    $systemMod = ($system % 10) - 1;
                    if ($systemMod === -1) {
                        $systemMod = 9;
                    }
                    $type = $baseType + $systemMod;
                    if ($type > 10) {
                        $type -= 10;
                    }

                    $viewData['chatPartnerPlanetImage'] = 'img/planets/medium/' . $biome . '_' . $type . '.png';
                }
            }
        }

        // If an allianceId is specified, load the alliance chat thread
        $chatAllianceId = $request->query('allianceId') ? (int) $request->query('allianceId') : null;
        if ($chatAllianceId && $alliance && $alliance->id === $chatAllianceId) {
            $allianceMessages = $chatService->getAllianceMessages($chatAllianceId);
            $viewData['chatAllianceId'] = $chatAllianceId;
            $viewData['chatAllianceMessages'] = $allianceMessages;
        }

        return view('ingame.chat.index', $viewData);
    }

    /**
     * Send a chat message (direct or alliance).
     *
     * Handles mode 1 (direct message) and mode 3 (alliance message).
     */
    public function sendMessage(Request $request, ChatService $chatService): JsonResponse
    {
        $userId = (int) auth()->id();
        $mode = (int) $request->input('mode', 1);
        $text = $request->input('text', '');
        $replyToId = $request->input('msg2reply') ? (int) $request->input('msg2reply') : null;

        // Validate message text
        if (trim($text) === '') {
            return response()->json(['status' => 'TEXT_EMPTY']);
        }

        if (mb_strlen($text) > 2000) {
            return response()->json(['status' => 'TEXT_TOO_LONG']);
        }

        if ($mode === 1) {
            // Direct message
            $recipientId = (int) $request->input('playerId');

            if ($recipientId === $userId) {
                return response()->json(['status' => 'SAME_USER']);
            }

            $recipient = User::find($recipientId);
            if (!$recipient) {
                return response()->json(['status' => 'INVALID_PARAMETERS']);
            }

            // Check if sender is ignored by recipient
            if (!$chatService->canMessagePlayer($userId, $recipientId)) {
                return response()->json(['status' => 'IGNORED_USER']);
            }

            $message = $chatService->sendDirectMessage($userId, $recipientId, $text, $replyToId);

            $response = [
                'status' => 'OK',
                'id' => $message->id,
                'targetId' => $recipientId,
                'text' => e($message->message),
                'date' => $message->created_at->timestamp,
            ];

            if ($message->replyTo) {
                $response['refAuthor'] = $message->replyTo->sender->username ?? 'Unknown';
                $response['refContent'] = e($message->replyTo->message);
            }

            return response()->json($response);
        }

        if ($mode === 3) {
            // Alliance message
            $allianceId = (int) $request->input('associationId');
            $user = User::find($userId);

            if (!$user || $user->alliance_id !== $allianceId) {
                return response()->json(['status' => 'NOT_AUTHORIZED']);
            }

            $message = $chatService->sendAllianceMessage($userId, $allianceId, $text, $replyToId);

            $response = [
                'status' => 'OK',
                'id' => $message->id,
                'targetAssociationId' => $allianceId,
                'text' => e($message->message),
                'date' => $message->created_at->timestamp,
            ];

            if ($message->replyTo) {
                $response['refAuthor'] = $message->replyTo->sender->username ?? 'Unknown';
                $response['refContent'] = e($message->replyTo->message);
            }

            return response()->json($response);
        }

        return response()->json(['status' => 'INVALID_PARAMETERS']);
    }

    /**
     * Get chat history with a player or alliance.
     *
     * Handles mode 2 (player chat history) and mode 4 (alliance chat history).
     */
    public function getHistory(Request $request, ChatService $chatService, BuddyService $buddyService): JsonResponse
    {
        $userId = (int) auth()->id();
        $mode = (int) $request->input('mode', 2);
        $updateUnread = (bool) $request->input('updateUnread', false);

        if ($mode === 2) {
            // Player chat history
            $partnerId = (int) $request->input('playerId');
            $partner = User::find($partnerId);

            if (!$partner) {
                return response()->json(['status' => 'INVALID_PARAMETERS']);
            }

            $messages = $chatService->getConversation($userId, $partnerId);

            if ($updateUnread) {
                $chatService->markAsRead($userId, $partnerId);
            }

            $formatted = $chatService->formatMessagesForFrontend($messages, $userId);

            // Only reveal online status if partner is a buddy or in the same alliance
            $user = User::find($userId);
            $canSeeOnline = $buddyService->areBuddies($userId, $partnerId)
                || ($user && $user->alliance_id && $user->alliance_id === $partner->alliance_id);
            $playerStatus = $canSeeOnline ? ($partner->isOnline() ? 'online' : 'offline') : 'offline';

            return response()->json([
                'playerId' => $partnerId,
                'playerName' => $partner->username,
                'playerstatus' => $playerStatus,
                'chatItems' => $formatted['chatItems'],
                'chatItemsByDateAsc' => $formatted['chatItemsByDateAsc'],
            ]);
        }

        if ($mode === 4) {
            // Alliance chat history
            $allianceId = (int) $request->input('associationId');
            $user = User::find($userId);

            if (!$user || $user->alliance_id !== $allianceId) {
                return response()->json(['status' => 'NOT_AUTHORIZED']);
            }

            $messages = $chatService->getAllianceMessages($allianceId);
            $formatted = $chatService->formatMessagesForFrontend($messages, $userId);

            return response()->json([
                'associationId' => $allianceId,
                'associationName' => $user->alliance->alliance_name ?? 'Alliance',
                'playerstatus' => 'online',
                'chatItems' => $formatted['chatItems'],
                'chatItemsByDateAsc' => $formatted['chatItemsByDateAsc'],
            ]);
        }

        return response()->json(['status' => 'INVALID_PARAMETERS']);
    }

    /**
     * Load more (older) messages for pagination.
     */
    public function loadMore(Request $request, ChatService $chatService): JsonResponse
    {
        $userId = (int) auth()->id();
        $beforeId = (int) $request->input('beforeId');
        $playerId = $request->input('playerId') ? (int) $request->input('playerId') : null;
        $allianceId = $request->input('associationId') ? (int) $request->input('associationId') : null;

        if ($playerId) {
            $messages = $chatService->getConversation($userId, $playerId, 50, $beforeId);
        } elseif ($allianceId) {
            $messages = $chatService->getAllianceMessages($allianceId, 50, $beforeId);
        } else {
            return response()->json(['status' => 'INVALID_PARAMETERS']);
        }

        $formatted = $chatService->formatMessagesForFrontend($messages, $userId);

        return response()->json([
            'chatItems' => $formatted['chatItems'],
            'chatItemsByDateAsc' => $formatted['chatItemsByDateAsc'],
        ]);
    }

    /**
     * Mark messages as read.
     */
    public function markRead(Request $request, ChatService $chatService): JsonResponse
    {
        $userId = (int) auth()->id();
        $partnerId = (int) $request->input('playerId');

        if ($partnerId) {
            $chatService->markAsRead($userId, $partnerId);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Toggle chat visibility state.
     * This is called when opening/closing chat windows in the chat bar.
     */
    public function toggleVisibility(Request $request): JsonResponse
    {
        // Chat visibility is handled client-side via cookies.
        // This endpoint exists for compatibility with the frontend.
        return response()->json(['success' => true]);
    }
}
