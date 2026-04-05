<?php

namespace OGame\Console\Commands\Scheduler;

use Exception;
use Illuminate\Console\Command;
use OGame\Factories\PlayerServiceFactory;
use OGame\GameMessages\WreckFieldRepairCompleted;
use OGame\Models\WreckField;
use OGame\Services\MessageService;
use OGame\Services\SettingsService;
use OGame\Services\WreckFieldService;

class CleanupWreckFields extends Command
{
    public function __construct(private readonly SettingsService $settingsService)
    {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ogamex:scheduler:cleanup-wreckfields';

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
            // Once repairs have started, expires_at no longer controls the wreck field lifecycle.
            // Repairing/completed wreck fields are handled exclusively by the auto-return flow.
            if (in_array($wreckField->status, ['repairing', 'completed'], true)) {
                continue;
            }

            $wreckField->delete();
            $count++;
        }

        return $count;
    }

    /**
     * Process auto-deployment of completed repairs.
     * According to rules: "Ships automatically return after 72 hours from repair start if not manually collected"
     *
     * @return int Number of wreck fields auto-deployed
     */
    private function processAutoDeployRepairs(): int
    {
        // Ships automatically return after the configured wreck field lifetime
        // counted from repair start, regardless of the original expires_at timestamp.
        $autoDeployDeadline = now()->subHours($this->settingsService->wreckFieldLifetimeHours());

        $wreckFieldIds = WreckField::whereIn('status', ['repairing', 'completed'])
            ->where('repair_started_at', '<', $autoDeployDeadline)
            ->pluck('id');

        $count = 0;
        $playerServiceFactory = resolve(PlayerServiceFactory::class);
        foreach ($wreckFieldIds as $wreckFieldId) {
            $wreckField = WreckField::whereKey($wreckFieldId)->first();
            if (!$wreckField) {
                continue;
            }

            try {
                $playerService = $playerServiceFactory->make($wreckField->owner_player_id);
                $wreckFieldService = new WreckFieldService($playerService, $this->settingsService);
                $processed = $wreckFieldService->autoDeployWreckFieldAtomic($wreckFieldId);
            } catch (Exception $e) {
                $this->error($e->getMessage());
                continue;
            }

            if ($processed === false) {
                continue;
            }

            if (($processed['total_deployed'] ?? 0) > 0) {
                $messageService = resolve(MessageService::class, ['player' => $playerService]);
                $messageService->sendSystemMessageToPlayer($playerService, WreckFieldRepairCompleted::class, [
                    'planet' => '[planet]' . $processed['planet_id'] . '[/planet]',
                    'ship_count' => (string) $processed['total_deployed'],
                ]);
            }

            $count++;
        }

        return $count;
    }
}
