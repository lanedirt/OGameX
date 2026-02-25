<?php

namespace OGame\Http\Controllers;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Models\Alliance;
use OGame\Models\AllianceMember;
use OGame\Models\ChatMessage;
use OGame\Models\User;
use OGame\Services\BuddyService;

class BuddiesController extends OGameController
{
    /**
     * Shows the buddies index page
     *
     * @param BuddyService $buddyService
     * @return View
     */
    public function index(BuddyService $buddyService): View
    {
        $this->setBodyId('buddies');

        $userId = (int) auth()->id();

        // Get all buddy-related data
        $buddies = $buddyService->getBuddies($userId);
        $receivedRequests = $buddyService->getReceivedRequests($userId);
        $sentRequests = $buddyService->getSentRequests($userId);
        $ignoredPlayers = $buddyService->getIgnoredPlayers($userId);
        $unreadRequestsCount = $buddyService->getUnreadRequestsCount($userId);

        // Mark requests as viewed when the page is loaded
        $buddyService->markRequestsAsViewed($userId);

        return view('ingame.buddies.index')->with([
            'buddies' => $buddies,
            'received_requests' => $receivedRequests,
            'sent_requests' => $sentRequests,
            'ignored_players' => $ignoredPlayers,
            'unread_requests_count' => $unreadRequestsCount,
        ]);
    }

    /**
     * Handle buddy actions via POST request
     *
     * @param Request $request
     * @param BuddyService $buddyService
     * @return JsonResponse|View|RedirectResponse
     */
    public function post(Request $request, BuddyService $buddyService): JsonResponse|View|RedirectResponse
    {
        $userId = (int) auth()->id();

        $action = $request->input('action');
        $targetId = $request->input('id');

        try {
            switch ($action) {
                case 3: // Cancel/withdraw buddy request
                    $buddyService->cancelRequest($targetId, $userId);
                    $message = __('t_buddies.success.request_cancelled');
                    break;

                case 4: // Reject buddy request
                    $buddyService->rejectRequest($targetId, $userId);
                    $message = __('t_buddies.success.request_rejected');
                    break;

                case 5: // Accept buddy request
                    $buddyService->acceptRequest($targetId, $userId);
                    $message = __('t_buddies.success.request_accepted');
                    break;

                case 9: // List/reload buddies
                    return $this->getBuddyListPartial($buddyService, $userId);

                case 10: // Delete buddy
                    $buddyService->deleteBuddy($targetId, $userId);
                    if ($request->input('ajax') == 1) {
                        return $this->getBuddyListPartial($buddyService, $userId);
                    }
                    $message = __('t_buddies.success.buddy_deleted');
                    break;

                case 15: // Search buddies
                    $searchTerm = $request->input('search', '');
                    return $this->searchBuddies($searchTerm, $buddyService, $userId);

                default:
                    throw new Exception(__('t_buddies.error.invalid_action'));
            }

            if ($request->input('ajax') == 1) {
                return response()->json(['success' => true, 'message' => $message]);
            }

            return redirect()->route('buddies.index')->with('status', $message);
        } catch (Exception $e) {
            if ($request->input('ajax') == 1) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }

            return redirect()->route('buddies.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Get buddy list partial view for AJAX requests
     *
     * @param BuddyService $buddyService
     * @param int $userId
     * @return View
     */
    private function getBuddyListPartial(BuddyService $buddyService, int $userId): View
    {
        $buddies = $buddyService->getBuddies($userId);

        return view('ingame.buddies.partials.buddy-list')->with([
            'buddies' => $buddies,
        ]);
    }

    /**
     * Search for users to add as buddies
     *
     * @param string $searchTerm
     * @param BuddyService $buddyService
     * @param int $userId
     * @return View
     */
    private function searchBuddies(string $searchTerm, BuddyService $buddyService, int $userId): View
    {
        $users = $buddyService->searchUsers($searchTerm, $userId);

        return view('ingame.buddies.partials.buddy-list')->with([
            'search_results' => $users,
        ]);
    }

    /**
     * Show buddy request dialog
     *
     * @param Request $request
     * @return View
     */
    public function showRequestDialog(Request $request): View
    {
        $playerId = $request->input('id');
        $playerName = $request->input('name', 'player');

        return view('ingame.buddies.dialog')->with([
            'playerId' => $playerId,
            'playerName' => $playerName,
        ]);
    }

    /**
     * Send a buddy request
     *
     * @param Request $request
     * @param BuddyService $buddyService
     * @return JsonResponse
     */
    public function sendRequest(Request $request, BuddyService $buddyService): JsonResponse
    {
        $userId = (int) auth()->id();

        $request->validate([
            'receiver_id' => 'required|integer|exists:users,id',
            'message' => 'nullable|string|max:5000',
        ]);

        try {
            $buddyRequest = $buddyService->sendRequest(
                $userId,
                $request->input('receiver_id'),
                $request->input('message')
            );

            return response()->json([
                'success' => true,
                'message' => __('t_buddies.success.request_sent'),
                'request_id' => $buddyRequest->id,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Ignore a player
     *
     * @param Request $request
     * @param BuddyService $buddyService
     * @return RedirectResponse
     */
    public function ignorePlayer(Request $request, BuddyService $buddyService): RedirectResponse
    {
        $userId = (int) auth()->id();

        $request->validate([
            'ignored_user_id' => 'required|integer|exists:users,id',
        ]);

        try {
            $buddyService->ignorePlayer($userId, $request->input('ignored_user_id'));
            return redirect()->route('buddies.index')->with('status', __('t_buddies.success.player_ignored'));
        } catch (Exception $e) {
            return redirect()->route('buddies.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Unignore a player
     *
     * @param Request $request
     * @param BuddyService $buddyService
     * @return RedirectResponse
     */
    public function unignorePlayer(Request $request, BuddyService $buddyService): RedirectResponse
    {
        $userId = (int) auth()->id();

        $request->validate([
            'ignored_user_id' => 'required|integer|exists:users,id',
        ]);

        try {
            $buddyService->unignorePlayer($userId, $request->input('ignored_user_id'));
            return redirect()->route('buddies.index')->with('status', __('t_buddies.success.player_unignored'));
        } catch (Exception $e) {
            return redirect()->route('buddies.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Get online buddies list for the chat bar
     *
     * @param BuddyService $buddyService
     * @return JsonResponse
     */
    public function getOnlineBuddies(BuddyService $buddyService): JsonResponse
    {
        $userId = (int) auth()->id();
        $user = User::find($userId);
        $allBuddies = $buddyService->getBuddies($userId);

        // Get IDs of players with active (recent) chats
        $activeChatPartnerIds = ChatMessage::where(function ($q) use ($userId) {
            $q->where('sender_id', $userId)->orWhere('recipient_id', $userId);
        })
            ->whereNotNull('recipient_id')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get()
            ->map(function ($msg) use ($userId) {
                return $msg->sender_id === $userId ? $msg->recipient_id : $msg->sender_id;
            })
            ->unique()
            ->values()
            ->all();

        $buddyList = $allBuddies->map(function ($buddyRequest) use ($userId, $activeChatPartnerIds) {
            $buddy = $buddyRequest->sender_user_id === $userId
                ? $buddyRequest->receiver
                : $buddyRequest->sender;

            return [
                'id' => $buddy->id,
                'username' => $buddy->username,
                'isOnline' => $buddy->isOnline(),
                'hasActiveChat' => in_array($buddy->id, $activeChatPartnerIds),
            ];
        });

        // Count only online buddies for the counter
        $onlineCount = $buddyList->filter(function ($buddy) {
            return $buddy['isOnline'];
        })->count();

        // Alliance info and members
        $alliance = null;
        $allianceMembers = [];
        if ($user && $user->alliance_id) {
            /** @var Alliance|null $allianceModel */
            $allianceModel = $user->alliance;
            if ($allianceModel) {
                $alliance = [
                    'id' => $allianceModel->id,
                    'name' => $allianceModel->alliance_name,
                    'tag' => $allianceModel->alliance_tag,
                ];
                /** @var Collection<int, AllianceMember> $members */
                $members = $allianceModel->members()
                    ->where('user_id', '!=', $userId)
                    ->with('user')
                    ->get();
                $allianceMembers = $members->map(function (AllianceMember $member) use ($activeChatPartnerIds) {
                    return [
                        'id' => $member->user_id,
                        'username' => $member->user->username,
                        'isOnline' => $member->user->isOnline(),
                        'hasActiveChat' => in_array($member->user_id, $activeChatPartnerIds),
                    ];
                })
                    ->values()
                    ->all();
            }
        }

        // Recent chat partners (players the user has chatted with recently, excluding buddies and alliance members)
        $buddyIds = $buddyList->pluck('id')->toArray();
        $allianceMemberIds = array_column($allianceMembers, 'id');
        $recentPartners = ChatMessage::where(function ($q) use ($userId) {
            $q->where('sender_id', $userId)->orWhere('recipient_id', $userId);
        })
            ->whereNotNull('recipient_id')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get()
            ->map(function ($msg) use ($userId) {
                return $msg->sender_id === $userId ? $msg->recipient_id : $msg->sender_id;
            })
            ->unique()
            ->filter(function ($partnerId) use ($buddyIds, $allianceMemberIds) {
                return !in_array($partnerId, $buddyIds) && !in_array($partnerId, $allianceMemberIds);
            })
            ->take(20)
            ->map(function ($partnerId) use ($user) {
                $partner = User::find($partnerId);
                if (!$partner) {
                    return;
                }

                // Only reveal online status for alliance members
                $sameAlliance = $user && $user->alliance_id && $user->alliance_id === $partner->alliance_id;

                return [
                    'id' => $partner->id,
                    'username' => $partner->username,
                    'isOnline' => $sameAlliance && $partner->isOnline(),
                ];
            })
            ->filter()
            ->values()
            ->all();

        return response()->json([
            'success' => true,
            'buddies' => $buddyList->values()->all(),
            'count' => $onlineCount,
            'alliance' => $alliance,
            'allianceMembers' => $allianceMembers,
            'recentPartners' => $recentPartners,
        ]);
    }
}
