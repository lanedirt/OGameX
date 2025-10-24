<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Services\BuddyService;
use OGame\Services\PlayerService;

class BuddiesController extends OGameController
{
    /**
     * Shows the buddies index page
     *
     * @param PlayerService $player
     * @return View
     */
    public function index(PlayerService $player): View
    {
        $userId = $player->getId();

        $buddies = BuddyService::getBuddies($userId);
        $receivedRequests = BuddyService::getPendingReceivedRequests($userId);
        $sentRequests = BuddyService::getPendingSentRequests($userId);
        $newRequestCount = BuddyService::getNewRequestCount($userId);

        return view('ingame.buddies.index', [
            'buddies' => $buddies,
            'receivedRequests' => $receivedRequests,
            'sentRequests' => $sentRequests,
            'newRequestCount' => $newRequestCount,
        ]);
    }

    /**
     * Send a buddy request
     *
     * @param Request $request
     * @param PlayerService $player
     * @return RedirectResponse
     */
    public function sendRequest(Request $request, PlayerService $player): RedirectResponse
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'nullable|string|max:500',
        ]);

        $senderId = $player->getId();
        $receiverId = $request->input('receiver_id');
        $message = $request->input('message');

        $buddyRequest = BuddyService::sendRequest($senderId, $receiverId, $message);

        if ($buddyRequest) {
            return redirect()->route('buddies.index')->with('success', 'Buddy request sent successfully!');
        }

        return redirect()->route('buddies.index')->with('error', 'Could not send buddy request. You may already be buddies or have a pending request.');
    }

    /**
     * Accept a buddy request
     *
     * @param int $requestId
     * @param PlayerService $player
     * @return RedirectResponse
     */
    public function acceptRequest(int $requestId, PlayerService $player): RedirectResponse
    {
        $success = BuddyService::acceptRequest($requestId, $player->getId());

        if ($success) {
            return redirect()->route('buddies.index')->with('success', 'Buddy request accepted!');
        }

        return redirect()->route('buddies.index')->with('error', 'Could not accept buddy request.');
    }

    /**
     * Reject a buddy request
     *
     * @param int $requestId
     * @param PlayerService $player
     * @return RedirectResponse
     */
    public function rejectRequest(int $requestId, PlayerService $player): RedirectResponse
    {
        $success = BuddyService::rejectRequest($requestId, $player->getId());

        if ($success) {
            return redirect()->route('buddies.index')->with('success', 'Buddy request rejected.');
        }

        return redirect()->route('buddies.index')->with('error', 'Could not reject buddy request.');
    }

    /**
     * Cancel a sent buddy request
     *
     * @param int $requestId
     * @param PlayerService $player
     * @return RedirectResponse
     */
    public function cancelRequest(int $requestId, PlayerService $player): RedirectResponse
    {
        $success = BuddyService::cancelRequest($requestId, $player->getId());

        if ($success) {
            return redirect()->route('buddies.index')->with('success', 'Buddy request cancelled.');
        }

        return redirect()->route('buddies.index')->with('error', 'Could not cancel buddy request.');
    }

    /**
     * Remove a buddy
     *
     * @param int $buddyId
     * @param PlayerService $player
     * @return RedirectResponse
     */
    public function removeBuddy(int $buddyId, PlayerService $player): RedirectResponse
    {
        $success = BuddyService::removeBuddy($player->getId(), $buddyId);

        if ($success) {
            return redirect()->route('buddies.index')->with('success', 'Buddy removed.');
        }

        return redirect()->route('buddies.index')->with('error', 'Could not remove buddy.');
    }
}
