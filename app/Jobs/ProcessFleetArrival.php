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

    /**
     * Allow several lock-contention retries while a large battle holds the
     * destination lock (release delay is 30s; see handle()).
     */
    public int $tries = 10;

    /**
     * Must be >= the destination Cache::lock TTL (600s) so a long battle cannot
     * be killed by the queue worker while still holding the lock.
     * Multi-million-unit battles can take 10s+; leave generous headroom.
     */
    public int $timeout = 600;

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
