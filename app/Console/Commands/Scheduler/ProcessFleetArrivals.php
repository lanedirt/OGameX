<?php

namespace OGame\Console\Commands\Scheduler;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use OGame\Services\FleetMissionService;

#[Description('Process overdue fleet arrival jobs after worker downtime or missed queue execution.')]
#[Signature('ogamex:scheduler:process-fleet-arrivals {--limit=100 : Maximum number of destination backlogs to process}')]
class ProcessFleetArrivals extends Command
{

    public function handle(FleetMissionService $fleetMissionService): int
    {
        $processedDestinations = $fleetMissionService->processMissedMissionEvents((int) $this->option('limit'));

        $this->info("Processed {$processedDestinations} fleet destination backlog(s).");

        return self::SUCCESS;
    }
}
