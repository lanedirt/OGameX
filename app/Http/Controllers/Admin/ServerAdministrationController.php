<?php

namespace OGame\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use OGame\Factories\PlayerServiceFactory;
use OGame\Http\Controllers\OGameController;
use OGame\Models\User;

class ServerAdministrationController extends OGameController
{
    /**
     * Shows the server administration page.
     */
    public function index(): View
    {
        // --- Shared IP groups ---
        // Find IPs (last_ip) shared by 2–10 users. Groups larger than 10 are likely
        // shared infrastructure (VPNs, university networks) and are excluded to reduce noise.
        $sharedLastIps = DB::table('users')
            ->select('last_ip')
            ->whereNotNull('last_ip')
            ->groupBy('last_ip')
            ->havingRaw('COUNT(*) > 1 AND COUNT(*) <= 10')
            ->pluck('last_ip');

        // Find IPs (register_ip) shared by 2–10 users
        $sharedRegisterIps = DB::table('users')
            ->select('register_ip')
            ->whereNotNull('register_ip')
            ->groupBy('register_ip')
            ->havingRaw('COUNT(*) > 1 AND COUNT(*) <= 10')
            ->pluck('register_ip');

        $sharedIpGroups = collect();

        foreach ($sharedLastIps as $ip) {
            $users = User::where('last_ip', $ip)->get();
            $sharedIpGroups->push([
                'ip'             => $ip,
                'type'           => 'Last active IP',
                'users'          => $users,
                'cross_missions' => $this->getCrossAccountMissions($users->pluck('id')->toArray()),
            ]);
        }

        foreach ($sharedRegisterIps as $ip) {
            $users = User::where('register_ip', $ip)->get();
            $sharedIpGroups->push([
                'ip'             => $ip,
                'type'           => 'Registration IP',
                'users'          => $users,
                'cross_missions' => $this->getCrossAccountMissions($users->pluck('id')->toArray()),
            ]);
        }

        // --- Currently banned users ---
        $bannedUsers = User::whereNotNull('ban_reason')
            ->where(function ($query) {
                $query->whereNull('banned_until')
                    ->orWhere('banned_until', '>', now());
            })
            ->get();

        return view('ingame.admin.server-administration', [
            'sharedIpGroups'  => $sharedIpGroups,
            'unusualActivity' => $this->getUnusualActivitySuspects(),
            'bannedUsers'     => $bannedUsers,
        ]);
    }

    /**
     * Returns players with bot-like fleet activity over the past 7 days.
     *
     * A player is flagged when they have both:
     *   - Fleet missions spanning 18 or more distinct hours of the day (only 6 hours "offline")
     *   - At least 700 total missions (≈ 6/hour × 18 hours/day × 7 days — the upper bound of intense human play)
     *
     * Using both signals together avoids false positives from players who play long
     * hours at low intensity, or who were simply online for a day straight during a war.
     *
     * @return \Illuminate\Support\Collection<int, object>
     */
    private function getUnusualActivitySuspects(): \Illuminate\Support\Collection
    {
        $cutoff = now()->subDays(7)->timestamp;

        $rows = DB::table('fleet_missions')
            ->where('time_departure', '>', $cutoff)
            ->where('canceled', 0)
            ->groupBy('user_id')
            ->havingRaw('COUNT(DISTINCT FLOOR(time_departure % 86400 / 3600)) >= 18 AND COUNT(*) >= 700')
            ->select([
                'user_id',
                DB::raw('COUNT(*) as mission_count'),
                DB::raw('COUNT(DISTINCT FLOOR(time_departure % 86400 / 3600)) as active_hours'),
            ])
            ->orderByDesc('active_hours')
            ->get();

        if ($rows->isEmpty()) {
            return collect();
        }

        $users = User::whereIn('id', $rows->pluck('user_id'))->get()->keyBy('id');

        return $rows
            ->filter(fn ($row) => isset($users[$row->user_id]))
            ->map(function ($row) use ($users) {
                $row->user = $users[$row->user_id];
                return $row;
            })
            ->values();
    }

    /**
     * Returns fleet missions sent between accounts in the same IP group.
     * Mission types: 1=Attack, 3=Transport, 6=Espionage.
     *
     * @param array<int> $userIds
     * @return \Illuminate\Support\Collection<int, object>
     */
    private function getCrossAccountMissions(array $userIds): \Illuminate\Support\Collection
    {
        if (count($userIds) < 2) {
            return collect();
        }

        return DB::table('fleet_missions')
            ->join('planets as p_to', 'fleet_missions.planet_id_to', '=', 'p_to.id')
            ->join('users as sender', 'fleet_missions.user_id', '=', 'sender.id')
            ->join('users as target', 'p_to.user_id', '=', 'target.id')
            ->whereIn('fleet_missions.user_id', $userIds)
            ->whereIn('p_to.user_id', $userIds)
            ->whereColumn('fleet_missions.user_id', '!=', 'p_to.user_id')
            ->whereIn('fleet_missions.mission_type', [1, 3, 6])
            ->where('fleet_missions.canceled', 0)
            ->select([
                'fleet_missions.id',
                'fleet_missions.mission_type',
                'fleet_missions.time_departure',
                'fleet_missions.metal',
                'fleet_missions.crystal',
                'fleet_missions.deuterium',
                'sender.username as sender_username',
                'target.username as target_username',
            ])
            ->orderBy('fleet_missions.time_departure', 'desc')
            ->limit(20)
            ->get();
    }

    /**
     * Bans a user.
     */
    public function ban(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string', 'exists:users,username'],
            'reason'   => ['required', 'string', 'max:1000'],
            'duration' => ['required', 'string'],
        ]);

        /** @var User $user */
        $user = User::where('username', $request->input('username'))->firstOrFail();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.server-administration.index')
                ->with('error', 'Administrators cannot be banned.');
        }

        $bannedUntil = null;

        if ($request->input('duration') !== 'permanent') {
            $bannedUntil = now()->addSeconds((int) $request->input('duration'));
        }

        $user->ban_reason = $request->input('reason');
        $user->banned_until = $bannedUntil;
        $user->save();

        // Temporary bans activate vacation mode so the account is protected while the
        // player is away. Permanent bans do not — the account is forfeit anyway.
        // This bypasses canActivateVacationMode() (which blocks when fleets are in transit)
        // — bans take precedence. Fleets already in transit will still complete.
        // Vacation mode persists after unban; the player must disable it manually.
        if ($bannedUntil !== null) {
            $playerService = resolve(PlayerServiceFactory::class)->make($user->id);
            $playerService->activateVacationMode();
        }

        return redirect()->route('admin.server-administration.index')
            ->with('status', "User \"{$user->username}\" has been banned.");
    }

    /**
     * Unbans a user.
     */
    public function unban(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        /** @var User $user */
        $user = User::findOrFail($request->input('user_id'));

        $user->ban_reason = null;
        $user->banned_until = null;
        $user->save();

        return redirect()->route('admin.server-administration.index')
            ->with('status', "User \"{$user->username}\" has been unbanned.");
    }
}
