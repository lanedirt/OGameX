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
        Schema::table('planets', function (Blueprint $table) {
            // Drop the existing unique constraint
            $table->dropUnique('galaxy_system_planet_unique');

            // Add new unique constraint including planet_type
            $table->unique(['galaxy', 'system', 'planet', 'planet_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planets', function (Blueprint $table) {
            // Drop the new unique constraint
            $table->dropUnique(['galaxy', 'system', 'planet', 'planet_type']);

            // Restore the original unique constraint
            $table->unique(['galaxy', 'system', 'planet'], 'galaxy_system_planet_unique');
        });
    }
};
