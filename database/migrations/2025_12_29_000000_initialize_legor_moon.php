<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use OGame\Jobs\CreateLegorMoon;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * This migration dispatches a delayed job to create Legor's moon and debris field.
     * It simulates a "mock attack" on Arakis 90 seconds after server initialization.
     */
    public function up(): void
    {
        // Check if Legor exists (created by earlier migration)
        $legor = DB::table('users')->where('username', 'Legor')->first();

        if ($legor) {
            $legorId = (int) $legor->id;

            // Check if moon already exists
            $moonExists = DB::table('planets')
                ->where('user_id', $legorId)
                ->where('planet_type', 3) // Moon type
                ->exists();

            if (!$moonExists) {
                // Get Legor's planet (Arakis)
                $planet = DB::table('planets')
                    ->where('user_id', $legorId)
                    ->where('planet_type', 1) // Planet type
                    ->first();

                if ($planet) {
                    $planetId = (int) $planet->id;

                    // Dispatch moon creation job with 90-second delay
                    // This simulates a "mock attack" creating debris field and moon
                    CreateLegorMoon::dispatch($planetId)->delay(now()->addSeconds(90));
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Moon and debris cleanup is handled by the add_roles migration rollback
        // No additional cleanup needed here
    }
};
