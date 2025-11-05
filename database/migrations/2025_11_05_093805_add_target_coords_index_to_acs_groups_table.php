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
        Schema::table('acs_groups', function (Blueprint $table) {
            // Add composite index for finding available ACS groups by target coordinates
            // This significantly speeds up getGroupsForTarget() queries
            $table->index(['galaxy_to', 'system_to', 'position_to', 'type_to', 'status'], 'idx_target_coords_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('acs_groups', function (Blueprint $table) {
            // Drop the composite index
            $table->dropIndex('idx_target_coords_status');
        });
    }
};
