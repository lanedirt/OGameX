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
        // Seed the inactive player deletion threshold. 0 = feature disabled (default).
        DB::table('settings')->updateOrInsert(
            ['key' => 'inactive_player_deletion_days'],
            ['value' => '0', 'created_at' => now(), 'updated_at' => now()]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        DB::table('settings')->where('key', 'inactive_player_deletion_days')->delete();
    }
};
