<?php

namespace OGame\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use OGame\Models\FleetMission;
use OGame\Services\FleetMissionService;
use OGame\Services\PlayerService;
use Throwable;

class ProcessFleetMissions implements ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 0; // Unlimited retries for lock contention

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 1;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 3600; // 1 hour for large battles

    /**
     * @var FleetMission $mission
     */
    private FleetMission $mission;

    /**
     * @var PlayerService $player
     */
    private PlayerService $player;

    /**
     * Create a new job instance.
     */
    public function __construct(FleetMission $mission, PlayerService $player)
    {
        $this->mission = $mission;
        $this->player = $player;
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        // If no destination planet (e.g., expeditions to empty space),
        // use mission ID as lock key to ensure at least job-level uniqueness
        $lock_key = $this->mission->planet_id_to
            ? 'planet_'.$this->mission->planet_id_to
            : 'mission_' . $this->mission->id;

        return [
            (new WithoutOverlapping($lock_key))
                ->releaseAfter(1)       // Retry after 1 second if lock is held
        ];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        \Log::info("FLEET MISSION STARTED FOR PLANET {$this->mission->planet_id_to}");
        if ($this->mission->canceled == 1) {
            return;
        }
        // FIFO check: Is there an earlier-arriving unprocessed mission targeting the same planet?
        if ($this->mission->planet_id_to) {
            try {
                // Small delay to ensure database consistency before checking
                usleep(50000); // 50ms delay

                $older_mission_exists = FleetMission::where('planet_id_to', $this->mission->planet_id_to)
                    ->where('processed', 0)
                    ->where(function ($query) {
                        // Earlier arrival time, OR same arrival time but lower ID (tie-breaker)
                        $query->where('time_arrival', '<', $this->mission->time_arrival)
                              ->orWhere(function ($q) {
                                  $q->where('time_arrival', '=', $this->mission->time_arrival)
                                    ->where('id', '<', $this->mission->id);
                              });
                    })
                    ->lockForUpdate()
                    ->exists();

                \Log::info('FLEET OLDER MISSION EXISTS', ['exists' => $older_mission_exists]);

                if ($older_mission_exists) {
                    // There's an earlier-arriving mission waiting, release this job back to queue
                    \Log::info('FLEET MISSION WAITING', [
                        'mission_id' => $this->mission->id,
                        'planet_id_to' => $this->mission->planet_id_to,
                        'time_arrival' => $this->mission->time_arrival,
                        'reason' => 'Earlier-arriving mission exists, maintaining FIFO order by arrival time',
                    ]);
                    $this->release(1); // Retry after 1 second
                    return;
                } else {
                    \Log::info('FLEET NO MISSION WAITING', [
                        'mission_id' => $this->mission->id,
                        'planet_id_to' => $this->mission->planet_id_to,
                        'older_mission_exists' => $older_mission_exists,
                    ]);
                }
            } catch (Throwable $e) {
                \Log::error('FLEET MISSION FIFO CHECK FAILED', [
                    'mission_id' => $this->mission->id,
                    'planet_id_to' => $this->mission->planet_id_to,
                    'error' => $e->getMessage(),
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
                // On database error, retry after 5 seconds
                $this->release(5);
                return;
            }
        }

        // Calculate processing delay
        $expected_arrival = $this->mission->time_arrival;
        $actual_processing_time = time();
        $processing_delay = $actual_processing_time - $expected_arrival;

        $context = [
            'mission_id' => $this->mission->id,
            'planet_id_to' => $this->mission->planet_id_to,
            'mission_type' => $this->mission->mission_type,
            'lock_key' => $this->mission->planet_id_to
                ? 'planet_'.$this->mission->planet_id_to
                : 'mission_' . $this->mission->id,
            'worker' => getmypid(),
            'expected_arrival' => $expected_arrival,
            'actual_processing_time' => $actual_processing_time,
            'processing_delay_seconds' => $processing_delay,
        ];

        \Log::info('FLEET MISSION START', $context);

        // Warning if processing is delayed more than 5 seconds
        if ($processing_delay > 5) {
            \Log::warning('FLEET MISSION PROCESSING DELAYED', [
                'mission_id' => $this->mission->id,
                'delay_seconds' => $processing_delay,
                'reason' => 'Mission processed later than expected arrival time',
            ]);
        }

        $start_time = microtime(true);

        try {
            $fleet_mission_service = resolve(FleetMissionService::class, ['player' => $this->player]);
            $fleet_mission_service->updateMission($this->mission);

            $duration = round((microtime(true) - $start_time) * 1000, 2);

            \Log::info('FLEET MISSION COMPLETE', [
                'mission_id' => $this->mission->id,
                'planet_id_to' => $this->mission->planet_id_to,
                'duration_ms' => $duration,
                'worker' => getmypid()
            ]);
        } catch (Throwable $e) {
            $duration = round((microtime(true) - $start_time) * 1000, 2);

            \Log::error('FLEET MISSION PROCESSING FAILED', [
                'mission_id' => $this->mission->id,
                'planet_id_to' => $this->mission->planet_id_to,
                'mission_type' => $this->mission->mission_type,
                'duration_ms' => $duration,
                'error' => $e->getMessage(),
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'worker' => getmypid()
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     *
     * @param Throwable $exception
     * @return void
     */
    public function failed(Throwable $exception): void
    {
        \Log::critical('FLEET MISSION JOB PERMANENTLY FAILED', [
            'mission_id' => $this->mission->id,
            'planet_id_to' => $this->mission->planet_id_to,
            'mission_type' => $this->mission->mission_type,
            'user_id' => $this->mission->user_id,
            'error' => $exception->getMessage(),
            'exception' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'worker' => getmypid(),
            'timestamp' => time(),
        ]);
    }
}
