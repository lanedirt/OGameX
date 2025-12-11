<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add expedition merchant outcome setting (enabled by default)
        DB::table('settings')->updateOrInsert(
            ['key' => 'expedition_gain_merchant_trade'],
            [
                'value' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->where('key', 'expedition_gain_merchant_trade')->delete();
    }
};
