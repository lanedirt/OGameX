<?php

namespace OGame\Console\Commands;

use Illuminate\Console\Command;
use OGame\Models\DebrisField;

class ResetDebrisFields extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ogamex:reset-debris-fields';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resets (deletes) empty debris fields. In OGame, this happens weekly on Monday at 1:00 AM.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Resetting empty debris fields...');

        // Delete all debris fields that have no resources
        $deletedCount = DebrisField::where('metal', 0)
            ->where('crystal', 0)
            ->where('deuterium', 0)
            ->delete();

        $this->info("Deleted {$deletedCount} empty debris field(s).");
    }
}
