<?php

namespace OGame\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use OGame\Services\FleetMissionService;

class ProcessFleetArrival implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public int $missionId)
    {
        $this->onQueue(FleetMissionService::ARRIVAL_QUEUE_NAME);
    }

    public function handle(FleetMissionService $fleetMissionService): void
    {
        $fleetMissionService->processDueMissionEventsForMissionId($this->missionId);
    }
}
