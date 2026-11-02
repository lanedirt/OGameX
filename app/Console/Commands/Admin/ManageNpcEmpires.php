<?php

namespace OGame\Console\Commands\Admin;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use OGame\Models\Planet;
use OGame\Models\User;
use OGame\Services\NpcEmpire\NpcEmpireManager;
use OGame\Services\SettingsService;
use Throwable;

#[Description('Manage NPC empires: spawn, list or remove AI-controlled empires')]
#[Signature('ogamex:admin:npc {action=spawn : Action to perform: spawn|list|remove} {--count=1 : Number of NPC empires to spawn} {--name= : Custom username for the spawned NPC empire} {--user= : NPC user id(s, comma separated) or "all" when removing} {--force : Remove without confirmation}')]
class ManageNpcEmpires extends Command
{
    public function __construct(
        private NpcEmpireManager $npcEmpireManager,
        private SettingsService $settingsService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @throws Throwable
     */
    public function handle(): int
    {
        return match ((string)$this->argument('action')) {
            'spawn'  => $this->handleSpawn(),
            'list'   => $this->handleList(),
            'remove' => $this->handleRemove(),
            default  => $this->invalidAction(),
        };
    }

    /**
     * Spawn one or more NPC empires.
     */
    private function handleSpawn(): int
    {
        if (!$this->settingsService->npcEmpiresEnabled()) {
            $this->warn('NPC empires are disabled in the server settings (npc_empires_enabled).');
        }

        $count = max(1, (int)$this->option('count'));
        $currentCount = $this->npcEmpireManager->listEmpires()->count();
        $maxCount = $this->settingsService->npcMaxCount();

        if ($currentCount + $count > $maxCount) {
            $this->warn("Spawning {$count} empires would exceed the configured maximum of {$maxCount} (currently {$currentCount}).");
            if (!$this->confirm('Spawn anyway?', true)) {
                return Command::SUCCESS;
            }
        }

        $name = $this->option('name');
        $spawned = 0;
        do {
            $empire = $this->npcEmpireManager->spawn($spawned === 0 ? $name : null);
            $spawned++;
            $this->info("Spawned NPC empire: {$empire->user->username} (user id {$empire->user_id})");
        } while ($spawned < $count);

        return Command::SUCCESS;
    }

    /**
     * List all NPC empires with their traits.
     */
    private function handleList(): int
    {
        $empires = $this->npcEmpireManager->listEmpires();

        if ($empires->isEmpty()) {
            $this->info('No NPC empires exist on this server.');
            return Command::SUCCESS;
        }

        $rows = $empires->map(function ($empire) {
            return [
                'user_id'           => $empire->user_id,
                'username'          => $empire->user->username,
                'planets'           => Planet::query()->where('user_id', $empire->user_id)->count(),
                'development_speed' => $empire->development_speed,
                'aggression'        => $empire->aggression,
                'expansion_rate'    => $empire->expansion_rate,
                'last_decision'     => $empire->last_ai_decision_at?->toDateTimeString() ?? '-',
            ];
        })->all();

        $this->table(
            ['User ID', 'Username', 'Planets', 'Dev speed', 'Aggression', 'Expansion', 'Last decision'],
            $rows
        );

        return Command::SUCCESS;
    }

    /**
     * Remove NPC empires by user id(s) or all of them.
     */
    private function handleRemove(): int
    {
        $filter = (string)$this->option('user');

        if ($filter === 'all') {
            $users = User::query()->where('is_npc', true)->get();
        } else {
            $ids = array_filter(array_map('intval', explode(',', $filter)));
            if ($ids === []) {
                $this->error('Provide --user=all or a comma separated list of NPC user ids.');
                return Command::FAILURE;
            }
            $users = User::query()->where('is_npc', true)->whereIn('id', $ids)->get();
        }

        if ($users->isEmpty()) {
            $this->error('No matching NPC empires found.');
            return Command::FAILURE;
        }

        if (!$this->option('force')) {
            $names = $users->pluck('username')->implode(', ');
            if (!$this->confirm("Remove {$users->count()} NPC empire(s): {$names}? This cannot be undone.")) {
                $this->info('Aborted.');
                return Command::SUCCESS;
            }
        }

        foreach ($users as $user) {
            $this->npcEmpireManager->delete($user);
            $this->info("Removed NPC empire: {$user->username} (user id {$user->id})");
        }

        return Command::SUCCESS;
    }

    /**
     * Report an invalid action argument.
     */
    private function invalidAction(): int
    {
        $this->error('Invalid action. Use one of: spawn, list, remove.');
        return Command::FAILURE;
    }
}
