<?php

namespace OGame\Console\Commands\Scheduler;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;
use OGame\Factories\PlanetServiceFactory;
use OGame\Models\Enums\PlanetType;
use OGame\Models\Planet;
use Throwable;

#[Description('Permanently deletes destroyed planets/moons that have been flagged for at least 24 hours (daily 3:00 purge).')]
#[Signature('ogamex:scheduler:cleanup-destroyed-planets')]
class CleanupDestroyedPlanets extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(PlanetServiceFactory $planetServiceFactory): int
    {
        // Official rule: at each daily cycle, purge bodies flagged for >= 24 hours.
        $cutoff = (int) Date::now()->subDay()->timestamp;

        // Purge moons first so parent planet delete does not try to cascade twice.
        $destroyedBodies = Planet::where('destroyed', '>', 0)
            ->where('destroyed', '<=', $cutoff)
            ->orderByRaw('CASE WHEN planet_type = ? THEN 0 ELSE 1 END', [PlanetType::Moon->value])
            ->orderBy('id')
            ->get();

        $deletedCount = 0;

        foreach ($destroyedBodies as $planetModel) {
            // Row may already be gone if a parent planet permanently deleted its moon.
            if (!Planet::where('id', $planetModel->id)->exists()) {
                continue;
            }

            try {
                $planetService = $planetServiceFactory->makeFromModel($planetModel);
                $planetService->permanentlyDeletePlanet();
                $deletedCount++;
            } catch (Throwable $e) {
                $this->error("Failed to permanently delete planet #{$planetModel->id}: {$e->getMessage()}");
            }
        }

        $this->info("Permanently deleted {$deletedCount} destroyed planet(s)/moon(s).");

        return Command::SUCCESS;
    }
}
