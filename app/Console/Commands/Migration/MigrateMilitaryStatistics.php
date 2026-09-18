<?php

namespace OGame\Console\Commands\Migration;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use OGame\Models\BattleReport;
use OGame\Models\User;

#[Description('Migrates military statistics from existing battle reports (one-time migration)')]
#[Signature('ogamex:migrate:military-statistics {--dry-run : Run without saving changes}')]
class MigrateMilitaryStatistics extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('Running in DRY RUN mode - no changes will be saved');
        }

        $this->info('Starting military statistics migration...');
        $this->info('This will calculate destroyed/lost points from all existing battle reports.');
        $this->newLine();

        // Reset all user statistics to 0 before recalculating
        if (!$dryRun) {
            $this->info('Resetting all user military statistics to 0...');
            User::query()->update([
                'military_units_destroyed_points' => 0,
                'military_units_lost_points' => 0,
            ]);
        }

        // Get all battle reports
        $battleReports = BattleReport::all();
        $totalReports = $battleReports->count();

        if ($totalReports === 0) {
            $this->warn('No battle reports found. Nothing to migrate.');
            return;
        }

        $this->info("Found {$totalReports} battle reports to process.");
        $bar = $this->output->createProgressBar($totalReports);
        $bar->start();

        $userStats = [];
        $processedReports = 0;
        $reportsWithoutLossData = 0;

        foreach ($battleReports as $report) {
            $attackerLoss = $report->attacker['resource_loss'] ?? null;
            $defenderLoss = $report->defender['resource_loss'] ?? null;

            if ($attackerLoss === null && $defenderLoss === null) {
                // Report predates resource loss tracking, so there is nothing to derive.
                $reportsWithoutLossData++;
            }

            // Calculate attacker losses
            $attackerPlayerId = $report->attacker['player_id'] ?? null;
            $attackerLostPoints = $this->calculatePointsFromResourceLoss($attackerLoss);

            // Calculate defender losses
            $defenderPlayerId = $report->defender['player_id'] ?? null;
            $defenderLostPoints = $this->calculatePointsFromResourceLoss($defenderLoss);

            // Accumulate statistics
            if ($attackerPlayerId) {
                if (!isset($userStats[$attackerPlayerId])) {
                    $userStats[$attackerPlayerId] = ['destroyed' => 0, 'lost' => 0];
                }
                $userStats[$attackerPlayerId]['destroyed'] += $defenderLostPoints;
                $userStats[$attackerPlayerId]['lost'] += $attackerLostPoints;
            }

            if ($defenderPlayerId) {
                if (!isset($userStats[$defenderPlayerId])) {
                    $userStats[$defenderPlayerId] = ['destroyed' => 0, 'lost' => 0];
                }
                $userStats[$defenderPlayerId]['destroyed'] += $attackerLostPoints;
                $userStats[$defenderPlayerId]['lost'] += $defenderLostPoints;
            }

            $processedReports++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Update user statistics
        $this->info('Updating user statistics...');
        $userBar = $this->output->createProgressBar(count($userStats));
        $userBar->start();

        foreach ($userStats as $userId => $stats) {
            if (!$dryRun) {
                User::where('id', $userId)->update([
                    'military_units_destroyed_points' => $stats['destroyed'],
                    'military_units_lost_points' => $stats['lost'],
                ]);
            }
            $userBar->advance();
        }

        $userBar->finish();
        $this->newLine(2);

        // Display summary
        $this->info('Migration Summary:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Battle reports processed', number_format($processedReports)],
                ['Reports without loss data', number_format($reportsWithoutLossData)],
                ['Players with statistics', number_format(count($userStats))],
            ]
        );

        if ($reportsWithoutLossData > 0) {
            $this->warn("{$reportsWithoutLossData} battle reports have no resource loss data and were counted as zero.");
        }

        if ($dryRun) {
            $this->warn('DRY RUN complete - no changes were saved');
            $this->info('Run without --dry-run flag to apply changes');
        } else {
            $this->info('Migration completed successfully!');
            $this->info('Next steps:');
            $this->info('1. Run: php artisan ogamex:scheduler:generate-highscores');
            $this->info('2. Run: php artisan ogamex:scheduler:generate-highscore-ranks');
        }
    }

    /**
     * Calculate military points from the resource loss recorded on a battle report.
     *
     * Battle reports store the fleet as it stood at the start of the battle, so the unit
     * lists cannot be used to derive losses. The 'resource_loss' value is the summed
     * resource value of the units that actually died, which is what we credit here.
     *
     * Note this weighs every lost unit at 100%, whereas live tracking counts civil ships
     * at 50%. Reports do not record which units were lost, so a one-time backfill cannot
     * reproduce that split; going forward the live tracking applies the correct weighting.
     *
     * @param mixed $resourceLoss The 'resource_loss' value from the report, if present
     * @return int The military points value
     */
    private function calculatePointsFromResourceLoss(mixed $resourceLoss): int
    {
        if (!is_numeric($resourceLoss)) {
            return 0;
        }

        $resourceLoss = (float)$resourceLoss;

        if ($resourceLoss <= 0) {
            return 0;
        }

        // Convert to points (divide by 1000, same as regular highscore calculation)
        return (int)floor($resourceLoss / 1000);
    }
}
