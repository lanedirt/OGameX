<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Track total points of military units destroyed by this player
            $table->bigInteger('military_units_destroyed_points')->default(0)->after('character_class_changed_at');

            // Track total points of military units lost by this player
            $table->bigInteger('military_units_lost_points')->default(0)->after('military_units_destroyed_points');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['military_units_destroyed_points', 'military_units_lost_points']);
        });
    }
};
