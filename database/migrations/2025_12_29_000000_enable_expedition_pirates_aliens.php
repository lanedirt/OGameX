<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // Enable pirates and aliens with official OGame percentages
        DB::table('settings')->updateOrInsert(
            ['key' => 'expedition_weight_pirates'],
            ['value' => '3.0', 'updated_at' => now()]
        );

        DB::table('settings')->updateOrInsert(
            ['key' => 'expedition_weight_aliens'],
            ['value' => '1.5', 'updated_at' => now()]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Revert to disabled state
        DB::table('settings')->updateOrInsert(
            ['key' => 'expedition_weight_pirates'],
            ['value' => '0', 'updated_at' => now()]
        );

        DB::table('settings')->updateOrInsert(
            ['key' => 'expedition_weight_aliens'],
            ['value' => '0', 'updated_at' => now()]
        );
    }
};
