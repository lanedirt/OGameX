<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get the current fleet_speed value, default to 1 if not set
        $currentFleetSpeed = DB::table('settings')->where('key', 'fleet_speed')->value('value') ?? 1;

        // Insert the three new fleet speed settings with the current fleet_speed value
        DB::table('settings')->insert([
            [
                'key' => 'fleet_speed_war',
                'value' => $currentFleetSpeed,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'fleet_speed_holding',
                'value' => $currentFleetSpeed,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'fleet_speed_peaceful',
                'value' => $currentFleetSpeed,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'fleet_speed_war',
            'fleet_speed_holding',
            'fleet_speed_peaceful',
        ])->delete();
    }
};
