<?php

namespace OGame\Console\Commands\Migration;

use Exception;
use Illuminate\Console\Command;
use OGame\GameObjects\Models\Enums\GameObjectType;
use OGame\Models\BattleReport;
use OGame\Models\User;
use OGame\Services\ObjectService;

class MigrateMilitaryStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ogamex:migrate:military-statistics {--dry-run : Run without saving changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrates military statistics from existing battle reports (one-time migration)';

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

        foreach ($battleReports as $report) {
            // Calculate attacker losses
            $attackerPlayerId = $report->attacker['player_id'] ?? null;
            $attackerLostPoints = $this->calculatePointsFromUnits($report->attacker['units'] ?? []);

            // Calculate defender losses
            $defenderPlayerId = $report->defender['player_id'] ?? null;
            $defenderLostPoints = $this->calculatePointsFromUnits($report->defender['units'] ?? []);

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
                ['Players with statistics', number_format(count($userStats))],
            ]
        );

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
     * Calculate military points from unit array in battle report.
     * Battle reports store starting units, so we calculate based on resource loss.
     *
     * @param array<string, int> $units Array of unit machine names to amounts
     * @return int The military points value
     */
    private function calculatePointsFromUnits(array $units): int
    {
        $points = 0;

        foreach ($units as $machineName => $amount) {
            if ($amount <= 0) {
                continue;
            }

            try {
                // Get the unit object (works for both ships and defenses)
                $unitObject = ObjectService::getUnitObjectByMachineName($machineName);

                $unitValue = $unitObject->price->resources->sum();

                // Apply appropriate multiplier based on unit type
                if ($unitObject->type === GameObjectType::Ship) {
                    // Check if it's a military or civil ship
                    $militaryShips = ObjectService::getMilitaryShipObjects();
                    $isMilitaryShip = false;
                    foreach ($militaryShips as $militaryShip) {
                        if ($militaryShip->machine_name === $unitObject->machine_name) {
                            $isMilitaryShip = true;
                            break;
                        }
                    }

                    if ($isMilitaryShip) {
                        // Military ships: 100%
                        $points += ($unitValue * $amount);
                    } else {
                        // Civil ships: 50%
                        $points += ($unitValue * $amount * 0.5);
                    }
                } elseif ($unitObject->type === GameObjectType::Defense) {
                    // Defense units: 100%
                    $points += ($unitValue * $amount);
                }
            } catch (Exception $e) {
                // Skip units we can't process
                continue;
            }
        }

        // Convert to points (divide by 1000, same as regular highscore calculation)
        return (int)floor($points / 1000);
    }
}
