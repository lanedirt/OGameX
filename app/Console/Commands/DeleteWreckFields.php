<?php

namespace OGame\Console\Commands;

use Illuminate\Console\Command;
use OGame\Models\WreckField;

class DeleteWreckFields extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wreckfields:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all existing wreck fields';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('=== CURRENT WRECK FIELDS ===');

        $wreckFields = WreckField::all();

        foreach ($wreckFields as $wreckField) {
            $this->line("ID: {$wreckField->id} - Galaxy: {$wreckField->galaxy}:{$wreckField->system}:{$wreckField->planet} - Status: {$wreckField->status} - Owner: {$wreckField->owner_player_id}");
        }

        $this->info('');
        $this->info('DELETING ALL WRECK FIELDS...');

        // Delete all wreck fields
        $deletedCount = WreckField::query()->delete();
        $this->info("Deleted {$deletedCount} wreck fields.");

        $this->info('');
        $this->info('=== VERIFICATION ===');
        $remaining = WreckField::count();
        $this->line("Remaining wreck fields: {$remaining}");

        $this->info('Wreck field cleanup complete!');

        return Command::SUCCESS;
    }
}