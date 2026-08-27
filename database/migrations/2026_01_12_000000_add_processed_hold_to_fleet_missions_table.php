<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('fleet_missions', function (Blueprint $table) {
            // Add processed_hold column to track if ACS Defend hold time has expired
            // This allows us to set time_arrival = physical_arrival + hold_time
            // while still being able to find fleets that are holding (processed_hold = 0)
            $table->integer('processed_hold')->default(0)->after('processed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fleet_missions', function (Blueprint $table) {
            $table->dropColumn('processed_hold');
        });
    }
};
