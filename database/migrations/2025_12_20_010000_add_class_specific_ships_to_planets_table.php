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
        Schema::table('planets', function (Blueprint $table) {
            // Add class-specific ships
            // Pathfinder - Discoverer class ship
            $table->integer('pathfinder')->default(0)->after('solar_satellite');

            // Reaper - General class ship
            $table->integer('reaper')->default(0)->after('pathfinder');

            // Crawler - Collector class unit (mobile resource extractor)
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
        Schema::table('planets', function (Blueprint $table) {
            $table->dropColumn(['pathfinder', 'reaper', 'crawler']);
        });
    }
};
