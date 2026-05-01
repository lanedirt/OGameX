<?php

namespace OGame\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use OGame\Services\FleetMissionService;
use Throwable;

class ProcessFleetArrival implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public function __construct(public int $missionId)
    {
        $this->onQueue(FleetMissionService::ARRIVAL_QUEUE_NAME);
    }

    public function handle(FleetMissionService $fleetMissionService): void
    {
        try {
            $fleetMissionService->processDueMissionEventsForMissionId($this->missionId);
        } catch (LockTimeoutException) {
            // Another worker is already processing this destination (e.g. a large battle).
            // Release back to the queue so this job is retried once the lock is free.
            Log::warning('Fleet destination lock busy, re-queuing job', ['mission_id' => $this->missionId]);
            $this->release(30);
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Fleet arrival job failed permanently', [
            'mission_id' => $this->missionId,
            'error' => $exception->getMessage(),
        ]);
    }
}
