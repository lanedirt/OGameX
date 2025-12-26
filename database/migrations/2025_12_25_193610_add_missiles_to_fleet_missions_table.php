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
            // Add missile columns after espionage_probe
            $table->integer('interplanetary_missile')->default(0)->after('espionage_probe');
            $table->integer('target_priority')->nullable()->after('interplanetary_missile');

            // Add planet type tracking columns
            $table->integer('type_from')->default(1)->after('position_from');
            $table->integer('type_to')->default(1)->after('position_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fleet_missions', function (Blueprint $table) {
            // Only drop columns that exist
            if (Schema::hasColumn('fleet_missions', 'interplanetary_missile')) {
                $table->dropColumn('interplanetary_missile');
            }
            if (Schema::hasColumn('fleet_missions', 'target_priority')) {
                $table->dropColumn('target_priority');
            }
            if (Schema::hasColumn('fleet_missions', 'type_from')) {
                $table->dropColumn('type_from');
            }
            if (Schema::hasColumn('fleet_missions', 'type_to')) {
                $table->dropColumn('type_to');
            }
        });
    }
};
