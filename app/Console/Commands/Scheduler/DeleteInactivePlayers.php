<?php

namespace OGame\Console\Commands\Scheduler;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use OGame\Factories\PlayerServiceFactory;
use OGame\Models\User;
use OGame\Services\SettingsService;
use Throwable;

#[Description('Permanently deletes players who have been inactive for the configured number of days.')]
#[Signature('ogamex:scheduler:delete-inactive-players')]
class DeleteInactivePlayers extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(SettingsService $settings, PlayerServiceFactory $playerServiceFactory): int
    {
        $days = $settings->inactivePlayerDeletionDays();

        // 0 = feature disabled (this is the default).
        if ($days <= 0) {
            $this->info('Inactive player deletion is disabled (inactive_player_deletion_days = 0).');

            return Command::SUCCESS;
        }

        $cutoff = now()->subDays($days)->timestamp;
        $deletedCount = 0;
        $failedCount = 0;

        // Admins are excluded to protect the server operator and system accounts, mirroring the
        // existing "administrators cannot be banned" rule. Vacation mode does NOT exempt a player.
        // The users.time column holds the last-activity UNIX timestamp.
        User::withoutRole('admin')
            ->whereNotNull('time')
            ->where('time', '<', $cutoff)
            ->chunkById(200, function ($users) use ($playerServiceFactory, &$deletedCount, &$failedCount): void {
                foreach ($users as $user) {
                    try {
                        // Load with a fresh cache so the deletion acts on the player's current
                        // planets, moons and missions rather than any stale cached state.
                        $playerServiceFactory->make($user->id, true)->deleteInactiveAccount();
                        $deletedCount++;
                    } catch (Throwable $e) {
                        $failedCount++;
                        $this->error("Failed to delete inactive player #{$user->id}: " . $e->getMessage());
                    }
                }
            });

        $this->info("Deleted {$deletedCount} inactive player(s) with no activity in the last {$days} day(s).");

        if ($failedCount > 0) {
            $this->warn("{$failedCount} player(s) could not be deleted; see errors above.");
        }

        return $failedCount > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
