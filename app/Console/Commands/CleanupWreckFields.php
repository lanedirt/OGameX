<?php

namespace OGame\Console\Commands;

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
     * According to rules: "Ships automatically return after 72 hours + 3 days if not manually deployed"
     *
     * @return int Number of wreck fields auto-deployed
     */
    private function processAutoDeployRepairs(): int
    {
        // Find wreck fields that are completed and older than 72 hours + 3 days
        $autoDeployDeadline = now()->subHours(72 + 72); // 72 hours + 3 days

        $completedWreckFields = WreckField::where('status', 'completed')
            ->where('repair_completed_at', '<', $autoDeployDeadline)
            ->get();

        $count = 0;
        foreach ($completedWreckFields as $wreckField) {
            // Deploy the ships to the planet
            $this->deployShipsToPlanet($wreckField);

            // Delete the wreck field after deployment
            $wreckField->delete();
            $count++;
        }

        return $count;
    }

    /**
     * Deploy repaired ships to the planet.
     *
     * @param WreckField $wreckField
     * @return void
     */
    private function deployShipsToPlanet(WreckField $wreckField): void
    {
        $planetServiceFactory = resolve(\OGame\Factories\PlanetServiceFactory::class);
        $planet = $planetServiceFactory->make($wreckField->owner_player_id)
            ->getPlayer()->planets->getPlanetByCoordinates(
                new \OGame\Models\Planet\Coordinate($wreckField->galaxy, $wreckField->system, $wreckField->planet)
            );

        if (!$planet) {
            $this->error("Could not find planet for wreck field at {$wreckField->galaxy}:{$wreckField->system}:{$wreckField->planet}");
            return;
        }

        $shipData = $wreckField->getShipData();
        $objectService = app(\OGame\Services\ObjectService::class);

        foreach ($shipData as $ship) {
            if ($ship['repair_progress'] >= 100) {
                $unitObject = $objectService->getUnitObjectByMachineName($ship['machine_name']);
                if ($unitObject) {
                    $planet->addUnit($unitObject->machine_name, $ship['quantity']);

                    $this->line("Auto-deployed {$ship['quantity']} {$ship['machine_name']} to planet {$wreckField->galaxy}:{$wreckField->system}:{$wreckField->planet}");
                }
            }
        }
    }
}
