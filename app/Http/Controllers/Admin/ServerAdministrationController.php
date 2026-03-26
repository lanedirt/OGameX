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
     * Shows the server administration page with multi-account detection and ban management.
     */
    public function index(): View
    {
        // --- Multi-account detection ---
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

        $suspiciousGroups = collect();

        foreach ($sharedLastIps as $ip) {
            $users = User::where('last_ip', $ip)->get();
            $suspiciousGroups->push(['ip' => $ip, 'type' => 'Last active IP', 'users' => $users]);
        }

        foreach ($sharedRegisterIps as $ip) {
            $users = User::where('register_ip', $ip)->get();
            $suspiciousGroups->push(['ip' => $ip, 'type' => 'Registration IP', 'users' => $users]);
        }

        // --- Currently banned users ---
        $bannedUsers = User::whereNotNull('ban_reason')
            ->where(function ($query) {
                $query->whereNull('banned_until')
                    ->orWhere('banned_until', '>', now());
            })
            ->get();

        return view('ingame.admin.server-administration', [
            'suspiciousGroups' => $suspiciousGroups,
            'bannedUsers' => $bannedUsers,
        ]);
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

        // Put the user in vacation mode for the duration of the ban. This bypasses the
        // normal canActivateVacationMode() check (which blocks activation when fleets are
        // in transit) — bans take precedence. Fleets already in transit will still complete.
        // Vacation mode persists after unban; the player must disable it manually.
        $playerService = resolve(PlayerServiceFactory::class)->make($user->id);
        $playerService->activateVacationMode();

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
