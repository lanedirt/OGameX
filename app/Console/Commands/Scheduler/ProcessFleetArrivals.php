<?php

namespace OGame\Console\Commands\Scheduler;

use Illuminate\Console\Command;
use OGame\Services\FleetMissionService;

class ProcessFleetArrivals extends Command
{
    protected $signature = 'ogamex:scheduler:process-fleet-arrivals {--limit=100 : Maximum number of destination backlogs to process}';

    protected $description = 'Process overdue fleet arrival jobs after worker downtime or missed queue execution.';

    public function handle(FleetMissionService $fleetMissionService): int
    {
        $processedDestinations = $fleetMissionService->processMissedMissionEvents((int) $this->option('limit'));

        $this->info("Processed {$processedDestinations} fleet destination backlog(s).");

        return self::SUCCESS;
    }
}
