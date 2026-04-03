<?php

namespace OGame\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use OGame\Factories\PlayerServiceFactory;
use OGame\Http\Controllers\OGameController;
use OGame\Models\Ban;
use OGame\Models\User;
use OGame\Services\SettingsService;
use stdClass;

class ServerAdministrationController extends OGameController
{
    /**
     * Mission types excluded from bot detection to avoid false positives.
     * 6 = Espionage (system scanners send hundreds), 9 = Missile attack (bulk fire, no slot cost).
     *
     * @var array<int>
     */
    private const EXCLUDED_MISSION_TYPES = [6, 9];

    /**
     * Shows the server administration page.
     */
    public function index(): View
    {
        $settingsService  = resolve(SettingsService::class);
        $dismissedIps     = json_decode((string) $settingsService->get('dismissed_shared_ip_groups', '[]'), true) ?: [];
        $dismissedUserIds = json_decode((string) $settingsService->get('dismissed_bot_suspect_ids', '[]'), true) ?: [];

        // Cache raw scalar/stdClass data only — no Eloquent models in the cache.
        // The IP list queries are inside the closure so they only run when the cache is cold.
        // cross_missions is stored as array<array<string, mixed>> (plain PHP arrays, not objects) to avoid
        // PHP 8.5 stdClass deserialisation issues with the file cache driver. Cast back to objects on read.
        /** @var array<string, array{ip: string, type: string, user_ids: array<int>, cross_missions: array<int, object>}> $ipGroupsRaw */
        $ipGroupsRaw = Cache::remember('bot_detection_ip_groups', 1800, function () {
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

            $groups = [];

            foreach ($sharedLastIps as $ip) {
                $userIds = User::where('last_ip', $ip)->pluck('id')->toArray();
                $groups[$ip] = [
                    'ip'             => $ip,
                    'type'           => 'Last active IP',
                    'user_ids'       => $userIds,
                    'cross_missions' => $this->getCrossAccountMissions($userIds)->map(fn ($m) => (array) $m)->all(),
                ];
            }

            foreach ($sharedRegisterIps as $ip) {
                if (isset($groups[$ip])) {
                    continue; // already captured via last_ip pass
                }
                $userIds = User::where('register_ip', $ip)->pluck('id')->toArray();
                $groups[$ip] = [
                    'ip'             => $ip,
                    'type'           => 'Registration IP',
                    'user_ids'       => $userIds,
                    'cross_missions' => $this->getCrossAccountMissions($userIds)->map(fn ($m) => (array) $m)->all(),
                ];
            }

            return $groups;
        });

        // Re-hydrate User models fresh on every request — never stored in the cache.
        $allIpUserIds = collect($ipGroupsRaw)->flatMap(fn ($g) => $g['user_ids'])->unique()->toArray();
        $ipUsers      = User::whereIn('id', $allIpUserIds)->get()->keyBy('id');

        $sharedIpGroups = collect($ipGroupsRaw)
            ->filter(fn ($group) => !in_array($group['ip'], $dismissedIps, true))
            ->map(fn ($group) => [
                'ip'             => $group['ip'],
                'type'           => $group['type'],
                'users'          => collect($group['user_ids'])->map(fn ($id) => $ipUsers->get($id))->filter()->values(),
                'cross_missions' => collect($group['cross_missions'])->map(fn ($m) => (object) $m),
            ])
            ->values();

        // --- Currently active bans ---
        $activeBans = Ban::with('user')
            ->where('canceled', false)
            ->where(function ($q) {
                $q->whereNull('banned_until')
                    ->orWhere('banned_until', '>', now());
            })->latest()
            ->get();

        // --- Ban history (most recent 30, including canceled/expired) ---
        $banHistory = Ban::with('user')->latest()
            ->limit(30)
            ->get();

        // Cache signal data keyed by user ID — no Eloquent models stored in the cache.
        $settings    = $this->detectionSettings();
        /** @var array<int, array{signals: array<string, mixed>}> $suspectSignals */
        $suspectSignals = Cache::remember('bot_detection_suspects', 1800, fn () => $this->getBotActivitySignals($settings));

        // Re-hydrate User models fresh on every request.
        $suspectUsers = User::whereIn('id', array_keys($suspectSignals))->get()->keyBy('id');

        $allSuspects = collect($suspectSignals)
            ->filter(fn ($entry, $userId) => isset($suspectUsers[$userId]))
            ->map(fn ($entry, $userId) => array_merge($entry, ['user' => $suspectUsers[$userId]]))
            ->sortByDesc(fn ($entry) => count($entry['signals']))
            ->values();

        $botSuspects = $allSuspects->reject(fn ($s) => in_array($s['user']->id, $dismissedUserIds, true))->values();

        return view('ingame.admin.server-administration', [
            'sharedIpGroups'        => $sharedIpGroups,
            'botSuspects'           => $botSuspects,
            'activeBans'            => $activeBans,
            'banHistory'            => $banHistory,
            'detectionSettings'     => $settings,
            'dismissedIpCount'      => count($dismissedIps),
            'dismissedSuspectCount' => $allSuspects->filter(fn ($s) => in_array($s['user']->id, $dismissedUserIds, true))->count(),
        ]);
    }

    /**
     * Saves bot detection threshold settings.
     */
    public function saveDetectionSettings(Request $request): RedirectResponse
    {
        $request->validate([
            'bot_detection_lookback_days'               => ['required', 'integer', 'min:1', 'max:90'],
            'bot_detection_active_hours'                => ['required', 'integer', 'min:1', 'max:24'],
            'bot_detection_missions_per_slot_per_day'   => ['required', 'integer', 'min:1'],
            'bot_detection_min_missions_floor'          => ['required', 'integer', 'min:0'],
            'bot_detection_expedition_gap_seconds'      => ['required', 'integer', 'min:1'],
            'bot_detection_expedition_min_occurrences'  => ['required', 'integer', 'min:1'],
            'bot_detection_attack_reaction_seconds'     => ['required', 'integer', 'min:1'],
            'bot_detection_attack_reaction_min_occurrences' => ['required', 'integer', 'min:1'],
        ]);

        $settingsService = resolve(SettingsService::class);

        foreach ($request->only([
            'bot_detection_lookback_days',
            'bot_detection_active_hours',
            'bot_detection_missions_per_slot_per_day',
            'bot_detection_min_missions_floor',
            'bot_detection_expedition_gap_seconds',
            'bot_detection_expedition_min_occurrences',
            'bot_detection_attack_reaction_seconds',
            'bot_detection_attack_reaction_min_occurrences',
        ]) as $key => $value) {
            $settingsService->set($key, (string) $value);
        }

        Cache::forget('bot_detection_suspects');

        return redirect()->route('admin.server-administration.index')
            ->with('status', 'Bot detection settings saved.');
    }

    /**
     * Returns the current bot detection settings with their defaults.
     *
     * @return array<string, int>
     */
    private function detectionSettings(): array
    {
        $s = resolve(SettingsService::class);

        return [
            'lookback_days'                   => (int) $s->get('bot_detection_lookback_days', 7),
            'active_hours'                    => (int) $s->get('bot_detection_active_hours', 18),
            'missions_per_slot_per_day'       => (int) $s->get('bot_detection_missions_per_slot_per_day', 18),
            'min_missions_floor'              => (int) $s->get('bot_detection_min_missions_floor', 50),
            'expedition_gap_seconds'          => (int) $s->get('bot_detection_expedition_gap_seconds', 10),
            'expedition_min_occurrences'      => (int) $s->get('bot_detection_expedition_min_occurrences', 5),
            'attack_reaction_seconds'         => (int) $s->get('bot_detection_attack_reaction_seconds', 10),
            'attack_reaction_min_occurrences' => (int) $s->get('bot_detection_attack_reaction_min_occurrences', 1),
        ];
    }

    /**
     * Merges all three bot-detection signals into an array keyed by user ID.
     * Returns only primitive/scalar data — no Eloquent models — so the result
     * is safe to serialize into the cache.
     *
     * @param array<string, int> $settings
     * @return array<int, array{signals: array<string, mixed>}>
     */
    private function getBotActivitySignals(array $settings): array
    {
        $byUserId = [];

        foreach ($this->getRoundTheClockSuspects($settings) as $row) {
            $byUserId[$row->user_id]['signals']['round_the_clock'] = [
                'active_hours'              => $row->active_hours,
                'missions_per_slot_per_day' => round($row->missions_per_slot_per_day, 1),
                'mission_count'             => $row->mission_count,
            ];
        }

        foreach ($this->getInstantExpeditionRedispatch($settings) as $row) {
            $byUserId[$row->user_id]['signals']['instant_expedition'] = [
                'occurrences' => $row->occurrences,
            ];
        }

        foreach ($this->getInstantFleetSaveAfterAttack($settings) as $row) {
            $byUserId[$row->user_id]['signals']['instant_attack_reaction'] = [
                'occurrences' => $row->occurrences,
            ];
        }

        return $byUserId;
    }

    /**
     * Signal 1 — Round-the-clock activity.
     *
     * Flags players who span many distinct hours of the day AND exceed a per-slot
     * mission rate, with a minimum floor to protect early-universe players.
     * Espionage (6) and missile attacks (9) are excluded.
     *
     * @param array<string, int> $settings
     * @return Collection<int, stdClass>
     */
    private function getRoundTheClockSuspects(array $settings): Collection
    {
        $cutoff     = now()->subDays($settings['lookback_days'])->timestamp;
        $floorMissions = $settings['min_missions_floor'];
        $activeHoursThreshold = $settings['active_hours'];
        $rateThreshold = $settings['missions_per_slot_per_day'];
        $lookbackDays = $settings['lookback_days'];

        return DB::table('fleet_missions')
            ->join('users_tech', 'fleet_missions.user_id', '=', 'users_tech.user_id')
            ->where('fleet_missions.time_departure', '>', $cutoff)
            ->where('fleet_missions.canceled', 0)
            ->whereNotIn('fleet_missions.mission_type', self::EXCLUDED_MISSION_TYPES)
            ->groupBy('fleet_missions.user_id', 'users_tech.computer_technology')
            ->havingRaw('COUNT(DISTINCT FLOOR(fleet_missions.time_departure % 86400 / 3600)) >= ?', [$activeHoursThreshold])
            ->havingRaw('COUNT(*) >= ?', [$floorMissions])
            ->havingRaw('COUNT(*) / (? * (COALESCE(users_tech.computer_technology, 0) + 1)) >= ?', [$lookbackDays, $rateThreshold])
            ->select([
                'fleet_missions.user_id',
                DB::raw('COUNT(*) as mission_count'),
                DB::raw('COUNT(DISTINCT FLOOR(fleet_missions.time_departure % 86400 / 3600)) as active_hours'),
                DB::raw('COALESCE(users_tech.computer_technology, 0) + 1 as fleet_slots'),
                DB::raw("COUNT(*) / ({$lookbackDays} * (COALESCE(users_tech.computer_technology, 0) + 1)) as missions_per_slot_per_day"),
            ])
            ->orderByDesc('active_hours')
            ->get();
    }

    /**
     * Signal 2 — Instant expedition re-dispatch.
     *
     * Finds players who re-dispatched an expedition within N seconds of a previous
     * expedition returning to the same planet. Physically impossible via the web UI.
     *
     * @param array<string, int> $settings
     * @return Collection<int, stdClass>
     */
    private function getInstantExpeditionRedispatch(array $settings): Collection
    {
        $cutoff     = now()->subDays($settings['lookback_days'])->timestamp;
        $gapSeconds = $settings['expedition_gap_seconds'];
        $minOccurrences = $settings['expedition_min_occurrences'];

        $rows = DB::table('fleet_missions as fm_return')
            ->join('fleet_missions as fm_next', function ($join) use ($gapSeconds) {
                $join->on('fm_next.user_id', '=', 'fm_return.user_id')
                    ->on('fm_next.planet_id_from', '=', 'fm_return.planet_id_to')
                    ->where('fm_next.mission_type', '=', 15)
                    ->whereNull('fm_next.parent_id')
                    ->where('fm_next.canceled', '=', 0)
                    ->whereRaw('fm_next.time_departure > fm_return.time_arrival')
                    ->whereRaw('fm_next.time_departure - fm_return.time_arrival <= ?', [$gapSeconds]);
            })
            ->where('fm_return.mission_type', 15)
            ->whereNotNull('fm_return.parent_id')
            ->where('fm_return.canceled', 0)
            ->where('fm_return.time_arrival', '>', $cutoff)
            ->groupBy('fm_return.user_id')
            ->havingRaw('COUNT(*) >= ?', [$minOccurrences])
            ->select([
                'fm_return.user_id',
                DB::raw('COUNT(*) as occurrences'),
            ])
            ->get();

        return $rows;
    }

    /**
     * Signal 3 — Instant fleet-save after incoming attack.
     *
     * Finds players who dispatched a fleet within N seconds of an attack being sent
     * against their planet. Sub-10-second reactions are impossible without a script
     * watching the API. Espionage (6) and missile attacks (9) are excluded from the
     * defender response to avoid false positives.
     *
     * @param array<string, int> $settings
     * @return Collection<int, stdClass>
     */
    private function getInstantFleetSaveAfterAttack(array $settings): Collection
    {
        $cutoff     = now()->subDays($settings['lookback_days'])->timestamp;
        $gapSeconds = $settings['attack_reaction_seconds'];
        $minOccurrences = $settings['attack_reaction_min_occurrences'];

        $rows = DB::table('fleet_missions as attack')
            ->join('planets', 'attack.planet_id_to', '=', 'planets.id')
            ->join('fleet_missions as response', function ($join) use ($gapSeconds) {
                $join->on('response.user_id', '=', 'planets.user_id')
                    ->where('response.canceled', '=', 0)
                    ->whereNull('response.parent_id')
                    ->whereNotIn('response.mission_type', self::EXCLUDED_MISSION_TYPES)
                    ->whereRaw('response.time_departure >= attack.time_departure')
                    ->whereRaw('response.time_departure - attack.time_departure <= ?', [$gapSeconds]);
            })
            ->where('attack.mission_type', 1)
            ->where('attack.canceled', 0)
            ->where('attack.time_departure', '>', $cutoff)
            ->groupBy('planets.user_id')
            ->havingRaw('COUNT(*) >= ?', [$minOccurrences])
            ->select([
                'planets.user_id',
                DB::raw('COUNT(*) as occurrences'),
            ])
            ->get();

        return $rows;
    }

    /**
     * Returns fleet missions sent between accounts in the same IP group.
     * Mission types: 1=Attack, 3=Transport, 6=Espionage.
     *
     * @param array<int> $userIds
     * @return Collection<int, stdClass>
     */
    private function getCrossAccountMissions(array $userIds): Collection
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
     * Dismisses a flagged shared-IP group or bot suspect so it is hidden from the panel.
     * Dismissals are stored in settings and persist until manually reset.
     *
     * Note: this does NOT invalidate the detection caches. Dismissal filtering is applied
     * as a post-filter over cached data in index(), so dismissed items disappear on the
     * next page load without re-running the expensive detection queries.
     */
    public function dismiss(Request $request): RedirectResponse
    {
        $request->validate([
            'type'  => ['required', 'string', 'in:shared_ip,bot_suspect'],
            'value' => ['required', 'string'],
        ]);

        $settingsService = resolve(SettingsService::class);

        if ($request->input('type') === 'shared_ip') {
            $dismissed   = json_decode((string) $settingsService->get('dismissed_shared_ip_groups', '[]'), true) ?: [];
            $dismissed[] = $request->input('value');
            $settingsService->set('dismissed_shared_ip_groups', (string) json_encode(array_unique($dismissed)));
            $label = 'IP group ' . $request->input('value');
        } else {
            $dismissed   = json_decode((string) $settingsService->get('dismissed_bot_suspect_ids', '[]'), true) ?: [];
            $dismissed[] = (int) $request->input('value');
            $settingsService->set('dismissed_bot_suspect_ids', (string) json_encode(array_unique($dismissed)));
            $label = 'bot suspect (user #' . $request->input('value') . ')';
        }

        return redirect()->route('admin.server-administration.index')
            ->with('status', "Dismissed {$label}. It will not appear again unless you reset dismissals.");
    }

    /**
     * Clears the bot-detection and IP-group caches so results are recomputed on the next page load.
     */
    public function clearCache(): RedirectResponse
    {
        Cache::forget('bot_detection_suspects');
        Cache::forget('bot_detection_ip_groups');

        return redirect()->route('admin.server-administration.index')
            ->with('status', 'Detection cache cleared. Results will be recomputed on this page load.');
    }

    /**
     * Bans a user.
     */
    public function ban(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string', 'exists:users,username'],
            'reason'   => ['required', 'string', 'max:1000'],
            'duration' => ['required', 'in:86400,259200,604800,2592000,permanent'],
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

        Ban::create([
            'user_id'      => $user->id,
            'reason'       => $request->input('reason'),
            'banned_until' => $bannedUntil,
            'canceled'     => false,
        ]);

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
     * Unbans a user by canceling their active ban record.
     */
    public function unban(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        /** @var User $user */
        $user = User::findOrFail($request->input('user_id'));

        $activeBan = $user->currentBan();
        if ($activeBan) {
            $activeBan->update([
                'canceled'   => true,
                'canceled_at' => now(),
            ]);
        }

        return redirect()->route('admin.server-administration.index')
            ->with('status', "User \"{$user->username}\" has been unbanned.");
    }
}
