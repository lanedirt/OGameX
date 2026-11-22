<?php

namespace OGame\Console\Commands\Scheduler;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use OGame\Models\ChatMessage;
use OGame\Models\Message;

#[Description('Deletes player inbox messages and archives chat messages older than the retention period.')]
#[Signature('ogamex:scheduler:delete-old-messages')]
class DeleteOldMessages extends Command
{
    private const RETENTION_DAYS = 7;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $cutoff = now()->subDays(self::RETENTION_DAYS);
        $deletedCount = 0;
        $archivedCount = 0;

        Message::where('created_at', '<=', $cutoff)
            ->chunkById(1000, function ($messages) use (&$deletedCount): void {
                $deletedCount += Message::whereIn('id', $messages->modelKeys())->delete();
            });

        ChatMessage::where('created_at', '<=', $cutoff)
            ->chunkById(1000, function ($messages) use (&$archivedCount): void {
                $archivedCount += ChatMessage::whereIn('id', $messages->modelKeys())->delete();
            });

        $this->info("Deleted {$deletedCount} inbox message(s) older than " . self::RETENTION_DAYS . ' days.');
        $this->info("Archived {$archivedCount} chat message(s) older than " . self::RETENTION_DAYS . ' days.');

        return Command::SUCCESS;
    }
}
