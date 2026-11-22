<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('fleet_missions', function (Blueprint $table) {
            // Add class-specific ships to fleet missions
            // Pathfinder - Discoverer class ship
            $table->integer('pathfinder')->default(0)->after('espionage_probe');

            // Reaper - General class ship
            $table->integer('reaper')->default(0)->after('pathfinder');

            // Crawler - Collector class unit (not flyable, but included for consistency)
            $table->integer('crawler')->default(0)->after('reaper');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('fleet_missions', function (Blueprint $table) {
            $table->dropColumn(['pathfinder', 'reaper', 'crawler']);
        });
    }
};
