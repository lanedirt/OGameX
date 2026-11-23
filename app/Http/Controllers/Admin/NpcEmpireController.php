<?php

namespace OGame\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use OGame\Http\Controllers\OGameController;
use OGame\Models\Planet;
use OGame\Models\User;
use OGame\Services\NpcEmpire\NpcEmpireManager;
use OGame\Services\SettingsService;
use Throwable;

class NpcEmpireController extends OGameController
{
    /**
     * Shows the NPC empires management page.
     *
     * @param NpcEmpireManager $npcEmpireManager
     * @param SettingsService $settingsService
     * @return View
     */
    public function index(NpcEmpireManager $npcEmpireManager, SettingsService $settingsService): View
    {
        $empires = $npcEmpireManager->listEmpires();

        $rows = [];
        foreach ($empires as $empire) {
            $planets = Planet::query()
                ->where('user_id', $empire->user_id)
                ->where('planet_type', 1)
                ->get();

            $row = [
                'user_id'           => $empire->user_id,
                'username'          => $empire->user->username,
                'development_speed' => $empire->development_speed,
                'aggression'        => $empire->aggression,
                'expansion_rate'    => $empire->expansion_rate,
                'last_decision'     => $empire->last_ai_decision_at?->format('Y-m-d H:i') ?? '-',
                'planets'           => [],
            ];
            foreach ($planets as $planet) {
                $row['planets'][] = [
                    'coords'     => $planet->galaxy . ':' . $planet->system . ':' . $planet->planet,
                    'name'       => $planet->name,
                ];
            }
            $rows[] = $row;
        }

        return view('ingame.admin.npc-empires')->with([
            'empires'         => $rows,
            'npc_count'       => count($rows),
            'npc_max_count'   => $settingsService->npcMaxCount(),
            'empires_enabled' => $settingsService->npcEmpiresEnabled(),
            'attacks_enabled' => $settingsService->npcAttackPlayersEnabled(),
        ]);
    }

    /**
     * Spawn one or more NPC empires.
     *
     * @param NpcEmpireManager $npcEmpireManager
     * @return RedirectResponse
     * @throws Throwable
     */
    public function spawn(NpcEmpireManager $npcEmpireManager): RedirectResponse
    {
        $count = min(10, max(1, (int)request('count', 1)));

        $spawned = 0;
        for ($i = 0; $i < $count; $i++) {
            $npcEmpireManager->spawn();
            $spawned++;
        }

        return redirect()
            ->route('admin.npc-empires.index')
            ->with('status', __('t_ingame.admin.npc_spawned', ['count' => $spawned]));
    }

    /**
     * Remove an NPC empire by user id.
     *
     * @param NpcEmpireManager $npcEmpireManager
     * @return RedirectResponse
     * @throws Throwable
     */
    public function remove(NpcEmpireManager $npcEmpireManager): RedirectResponse
    {
        $userId = (int)request('user_id', 0);

        $user = User::query()->where('is_npc', true)->find($userId);
        if ($user === null) {
            return redirect()
                ->route('admin.npc-empires.index')
                ->with('error', __('t_ingame.admin.npc_remove_not_found'));
        }

        $username = $user->username;
        $npcEmpireManager->delete($user);

        return redirect()
            ->route('admin.npc-empires.index')
            ->with('status', __('t_ingame.admin.npc_removed', ['name' => $username]));
    }
}
