<?php

namespace OGame\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\View\View;
use OGame\Factories\GameMissionFactory;
use OGame\Http\Controllers\OGameController;
use OGame\Models\FleetMission;
use OGame\Models\User;
use OGame\Services\PlayerService;

class FleetTimingController extends OGameController
{

    private const PER_PAGE_OPTIONS = [50, 100, 200, 500];
    private const PER_PAGE_DEFAULT = 50;

    /**
     * Shows the fleet timing control panel.
     */
    public function index(PlayerService $playerService, Request $request): View
    {
        $request->validate([
            'user_id'  => 'sometimes|nullable|integer|min:1',
            'per_page' => 'sometimes|nullable|integer|in:50,100,200,500',
        ]);

        $perPage = (int) $request->input('per_page', self::PER_PAGE_DEFAULT);

        $query = FleetMission::where('processed', 0)
            ->where('canceled', 0)
            ->orderBy('time_arrival');

        if ($request->filled('user_id')) {
            $query->where('user_id', (int) $request->input('user_id'));
        }

        $missions = $query->paginate($perPage)->withQueryString();

        $userIds = $missions->pluck('user_id')->unique();
        $users   = User::whereIn('id', $userIds)->pluck('username', 'id');

        // Only users that have at least one active mission (any, ignoring current filter)
        $activeUserIds = FleetMission::where('processed', 0)
            ->where('canceled', 0)
            ->pluck('user_id')
            ->unique();
        $allUsers = User::whereIn('id', $activeUserIds)->orderBy('username')->pluck('username', 'id');

        return view('ingame.admin.fleettiming', [
            'missions'          => $missions,
            'users'             => $users,
            'allUsers'          => $allUsers,
            'missionTypeLabels' => collect(GameMissionFactory::getAllMissions())
                ->mapWithKeys(fn ($mission, $id) => [$id => $mission::getName()]),
            'filterUserId'      => $request->input('user_id'),
            'perPage'           => $perPage,
            'perPageOptions'    => self::PER_PAGE_OPTIONS,
            'now'               => Date::now()->timestamp,
        ]);
    }

    /**
     * Fast-forwards a single mission to arrive immediately.
     */
    public function fastForward(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mission_id' => 'required|integer|min:1',
        ]);

        /** @var FleetMission $mission */
        $mission = FleetMission::where('id', $validated['mission_id'])
            ->where('processed', 0)
            ->where('canceled', 0)
            ->firstOrFail();

        $mission->time_arrival = (int) Date::now()->timestamp - 1;
        $mission->time_holding = 0;
        $mission->save();

        return redirect()->back()->with('success', "Mission #{$mission->id} → arrival set to now.");
    }

    /**
     * Fast-forwards all active missions to arrive immediately (optionally filtered by user).
     */
    public function fastForwardAll(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'sometimes|nullable|integer|min:1',
        ]);

        $query = FleetMission::where('processed', 0)->where('canceled', 0);

        if ($request->filled('user_id')) {
            $query->where('user_id', (int) $request->input('user_id'));
        }

        $now   = (int) Date::now()->timestamp - 1;
        $count = $query->update(['time_arrival' => $now, 'time_holding' => 0]);

        $scope = $request->filled('user_id') ? "for user #{$request->input('user_id')}" : 'globally';

        return redirect()->back()->with('success', "{$count} mission(s) fast-forwarded {$scope}.");
    }

    /**
     * Reduces the arrival time of a single mission by a given number of minutes.
     */
    public function reduceTime(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mission_id' => 'required|integer|min:1',
            'minutes'    => 'required|integer|min:1|max:10000',
        ]);

        /** @var FleetMission $mission */
        $mission = FleetMission::where('id', $validated['mission_id'])
            ->where('processed', 0)
            ->where('canceled', 0)
            ->firstOrFail();

        $reduction  = $validated['minutes'] * 60;
        $newArrival = (int) $mission->time_arrival - $reduction;

        // Don't go past "now" — clamp to now-1 so the scheduler picks it up immediately
        $mission->time_arrival = max($newArrival, (int) Date::now()->timestamp - 1);
        if ($newArrival <= Date::now()->timestamp) {
            $mission->time_holding = 0;
        }
        $mission->save();

        return redirect()->back()->with('success', "Mission #{$mission->id} → arrival reduced by {$validated['minutes']} min.");
    }
}
