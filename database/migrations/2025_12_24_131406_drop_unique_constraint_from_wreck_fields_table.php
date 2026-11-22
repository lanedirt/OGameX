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
        Schema::table('wreck_fields', function (Blueprint $table) {
            // Drop the unique constraint on galaxy, system, planet to allow multiple wreck fields at the same location
            // This enables having an active/blocked wreck field while another is being repaired
            $table->dropUnique(['galaxy', 'system', 'planet']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wreck_fields', function (Blueprint $table) {
            // Restore the unique constraint
            $table->unique(['galaxy', 'system', 'planet']);
        });
    }
};
