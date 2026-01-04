<?php

namespace OGame\Console\Commands;

use OGame\Factories\PlanetServiceFactory;
use OGame\Models\Planet\Coordinate;
use OGame\Services\ObjectService;
use Illuminate\Console\Command;
use OGame\Models\WreckField;

class CleanupWreckFields extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ogame:wreck-fields:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired wreck fields and process completed repairs';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting wreck field cleanup...');

        // Clean up expired wreck fields
        $expiredCount = $this->cleanupExpiredWreckFields();
        if ($expiredCount > 0) {
            $this->info("Cleaned up {$expiredCount} expired wreck fields");
        }

        // Process completed repairs that are overdue for auto-deployment
        $deployedCount = $this->processAutoDeployRepairs();
        if ($deployedCount > 0) {
            $this->info("Auto-deployed {$deployedCount} completed wreck fields");
        }

        $this->info('Wreck field cleanup completed.');
        return 0;
    }

    /**
     * Clean up expired wreck fields.
     *
     * @return int Number of wreck fields cleaned up
     */
    private function cleanupExpiredWreckFields(): int
    {
        $expiredWreckFields = WreckField::where('expires_at', '<', now())
            ->where('status', '!=', 'burned')
            ->get();

        $count = 0;
        foreach ($expiredWreckFields as $wreckField) {
            // Only delete if not currently repairing
            if ($wreckField->status !== 'repairing') {
                $wreckField->delete();
                $count++;
            }
        }

        return $count;
    }

    /**
     * Process auto-deployment of completed repairs.
     * According to rules: "Ships automatically return after 72 hours from repair start if not manually collected"
     *
     * @return int Number of wreck fields auto-deployed
     */
    private function processAutoDeployRepairs(): int
    {
        // Find wreck fields that started repairs more than 72 hours ago
        $autoDeployDeadline = now()->subHours(72);

        $repairingWreckFields = WreckField::where('status', 'repairing')
            ->where('repair_started_at', '<', $autoDeployDeadline)
            ->get();

        $count = 0;
        foreach ($repairingWreckFields as $wreckField) {
            // Deploy the ships to the planet based on current repair progress
            $this->deployShipsToPlanetWithProgress($wreckField);

            // Delete the wreck field after deployment
            $wreckField->delete();
            $count++;
        }

        return $count;
    }

    /**
     * Deploy repaired ships to the planet based on repair progress.
     *
     * @param WreckField $wreckField
     * @return void
     */
    private function deployShipsToPlanetWithProgress(WreckField $wreckField): void
    {
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $planet = $planetServiceFactory->make($wreckField->owner_player_id)
            ->getPlayer()->planets->getPlanetByCoordinates(
                new Coordinate($wreckField->galaxy, $wreckField->system, $wreckField->planet)
            );

        if (!$planet) {
            $this->error("Could not find planet for wreck field at {$wreckField->galaxy}:{$wreckField->system}:{$wreckField->planet}");
            return;
        }

        // Calculate repair progress (same logic as WreckFieldService)
        $totalTime = (int) $wreckField->repair_completed_at->timestamp - (int) $wreckField->repair_started_at->timestamp;
        $elapsedTime = (int) now()->timestamp - (int) $wreckField->repair_started_at->timestamp;
        $timeBasedProgress = min(100, max(0, (int) (($elapsedTime / $totalTime) * 100)));

        // Get Space Dock level cap
        $level = $wreckField->space_dock_level ?? 1;
        $percentages = [
            1 => 31.5, 2 => 33.6, 3 => 34.3, 4 => 35.0, 5 => 35.7,
            6 => 36.4, 7 => 37.1, 8 => 37.1, 9 => 37.8, 10 => 37.8,
            11 => 38.5, 12 => 38.5, 13 => 38.5, 14 => 39.2, 15 => 39.2,
        ];
        if ($level > 15) {
            $level = 15;
        }
        $levelCap = $percentages[$level] ?? 31.5;
        $cappedProgress = min($timeBasedProgress, $levelCap);
        $overallProgress = $cappedProgress / 100;

        $shipData = $wreckField->getShipData();
        $objectService = app(ObjectService::class);
        $totalDeployed = 0;

        foreach ($shipData as $ship) {
            $repairedCount = (int) floor($ship['quantity'] * $overallProgress);

            if ($repairedCount > 0) {
                $unitObject = $objectService->getUnitObjectByMachineName($ship['machine_name']);
                if ($unitObject) {
                    $planet->addUnit($unitObject->machine_name, $repairedCount);
                    $totalDeployed += $repairedCount;

                    $this->line("Auto-deployed {$repairedCount} {$ship['machine_name']} ({$cappedProgress}%) to planet {$wreckField->galaxy}:{$wreckField->system}:{$wreckField->planet}");
                }
            }
        }

        if ($totalDeployed > 0) {
            $this->info("Auto-deployed {$totalDeployed} ships from wreck field at {$wreckField->galaxy}:{$wreckField->system}:{$wreckField->planet}");
        }
    }
}
