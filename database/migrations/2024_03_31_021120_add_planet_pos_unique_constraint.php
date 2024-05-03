<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('planets', function (Blueprint $table) {
            // Adding a unique index to the galaxy, system, and planet columns
            $table->unique(['galaxy', 'system', 'planet'], 'galaxy_system_planet_unique');
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
            // Dropping the unique index if it exists
            $table->dropUnique('galaxy_system_planet_unique');
        });
    }
};
