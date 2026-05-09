<?php

namespace OGame\Console\Commands\Scheduler;

use Illuminate\Console\Command;
use OGame\Models\Message;

class DeleteOldMessages extends Command
{
    private const RETENTION_DAYS = 7;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ogamex:scheduler:delete-old-messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes player messages older than the message retention period.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $cutoff = now()->subDays(self::RETENTION_DAYS);
        $deletedCount = 0;

        Message::where('created_at', '<=', $cutoff)
            ->chunkById(1000, function ($messages) use (&$deletedCount): void {
                $deletedCount += Message::whereIn('id', $messages->modelKeys())->delete();
            });

        $this->info("Deleted {$deletedCount} message(s) older than " . self::RETENTION_DAYS . ' days.');

        return Command::SUCCESS;
    }
}
