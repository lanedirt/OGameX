<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'npc_autospawn_enabled'],
            ['value' => '0', 'updated_at' => now()]
        );

        DB::table('settings')->updateOrInsert(
            ['key' => 'npc_autospawn_interval_hours'],
            ['value' => '24', 'updated_at' => now()]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'npc_autospawn_enabled',
            'npc_autospawn_interval_hours',
        ])->delete();
    }
};
