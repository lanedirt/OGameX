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
            // Add composite index for coordinate lookups
            // This optimizes the query in getGroupsForTarget() which filters by these columns
            $table->index(['galaxy_to', 'system_to', 'position_to', 'type_to', 'status'], 'acs_groups_coordinates_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('acs_groups', function (Blueprint $table) {
            $table->dropIndex('acs_groups_coordinates_status_index');
        });
    }
};
