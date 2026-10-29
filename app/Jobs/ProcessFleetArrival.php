<?php

namespace OGame\Jobs;

use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use OGame\Services\FleetMissionService;
use Throwable;

class ProcessFleetArrival implements ShouldQueue
{
    use Queueable;

    /**
     * Seconds to wait before retrying after the destination lock was busy.
     */
    public const LOCK_RETRY_DELAY = 30;

    /**
     * Lock-contention retry budget. A released job counts as an attempt, so
     * tries * (DESTINATION_LOCK_WAIT + LOCK_RETRY_DELAY) must be >= DESTINATION_LOCK_TTL,
     * otherwise a job queued behind one long battle exhausts its attempts and hits
     * failed() before the lock is even allowed to expire. 20 * (10 + 30) = 800s > 600s.
     */
    public int $tries = 20;

    /**
     * The generous $tries budget is for lock contention only. A job that keeps
     * throwing (a poison mission) fails after this many exceptions instead.
     */
    public int $maxExceptions = 3;

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
            $this->release(self::LOCK_RETRY_DELAY);
        }
    }

    public function failed(Throwable $exception): void
    {
        // The mission stays unprocessed (processed=0), so the every-minute scheduler keeps
        // retrying it and the admin stuck-fleet tooling will surface it. Log with enough
        // context to identify a poison mission (e.g. a battle that repeatedly runs out of memory).
        Log::error('Fleet arrival job failed permanently after all retries; fleet may be stuck', [
            'mission_id' => $this->missionId,
            'attempts' => $this->attempts(),
            'error' => $exception->getMessage(),
        ]);
    }
}
