<?php

namespace OGame\Console\Commands\Scheduler;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use OGame\Models\DebrisField;

#[Description('Resets (deletes) empty debris fields. In OGame, this happens weekly on Monday at 1:00 AM.')]
#[Signature('ogamex:scheduler:reset-debris-fields')]
class ResetDebrisFields extends Command
{
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
