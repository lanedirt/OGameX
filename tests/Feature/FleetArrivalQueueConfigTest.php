<?php

namespace Tests\Feature;

use OGame\Jobs\ProcessFleetArrival;
use OGame\Services\FleetMissionService;
use Tests\TestCase;

/**
 * Guards the timing invariants the fleet-arrival queue depends on.
 *
 * These values live in three different places (the job class, the lock in
 * FleetMissionService, the queue config, and docker/entrypoint.sh) and MUST
 * stay in the correct relationship to each other. Raising one without the
 * others reintroduces real bugs (double-dispatch, mid-transaction lock expiry,
 * or a battle killed by the worker), so this test freezes the relationships.
 */
class FleetArrivalQueueConfigTest extends TestCase
{
    private function jobTimeout(): int
    {
        return (new ProcessFleetArrival(0))->timeout;
    }

    public function testRetryAfterExceedsJobTimeoutOnEveryDriver(): void
    {
        $jobTimeout = $this->jobTimeout();

        // Only the drivers that can actually run the fleet-arrivals worker
        // (the default connection is 'database'; 'redis' is the prod alternative).
        foreach (['database', 'redis'] as $connection) {
            $retryAfter = config("queue.connections.$connection.retry_after");

            $this->assertIsInt($retryAfter, "retry_after must be configured for the '$connection' connection.");
            $this->assertGreaterThan(
                $jobTimeout,
                $retryAfter,
                "queue.connections.$connection.retry_after ($retryAfter) must exceed ProcessFleetArrival::\$timeout ($jobTimeout), "
                . 'otherwise a long battle job is re-dispatched to a second worker while still running.'
            );
        }
    }

    public function testDestinationLockTtlIsAtLeastJobTimeout(): void
    {
        $this->assertGreaterThanOrEqual(
            $this->jobTimeout(),
            FleetMissionService::DESTINATION_LOCK_TTL,
            'The destination lock must outlive the job, or another worker can observe uncommitted writes.'
        );
    }

    public function testQueueWorkerTimeoutIsAtLeastJobTimeout(): void
    {
        // The worker commands live in the supervisor config (one per pool).
        $config = file_get_contents(base_path('docker/supervisor/queue-worker.conf'));
        $this->assertIsString($config);

        $matched = preg_match_all('/queue:work[^\n]*--timeout=(\d+)/', $config, $matches);
        $this->assertGreaterThanOrEqual(
            1,
            $matched,
            'The supervisor config must define at least one queue:work command with an explicit --timeout.'
        );

        // Every worker pool's --timeout must clear the job timeout.
        foreach ($matches[1] as $timeoutValue) {
            $workerTimeout = (int) $timeoutValue;
            $this->assertGreaterThanOrEqual(
                $this->jobTimeout(),
                $workerTimeout,
                "A worker --timeout ($workerTimeout) must be >= ProcessFleetArrival::\$timeout ({$this->jobTimeout()}), "
                . 'or the worker kills a long battle job before it finishes.'
            );
        }
    }

    public function testJobAllowsMultipleLockContentionRetries(): void
    {
        // Simultaneous arrivals at one destination contend for the lock; the job
        // releases and retries, so it needs more than a single attempt.
        $this->assertGreaterThanOrEqual(
            5,
            (new ProcessFleetArrival(0))->tries,
            'ProcessFleetArrival must allow several retries to survive lock contention during busy arrivals.'
        );
    }
}
