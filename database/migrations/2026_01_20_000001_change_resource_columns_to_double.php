<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Changes resource columns from FLOAT to DOUBLE for better precision.
     * FLOAT has ~7 digits precision which causes issues with large values (20M+).
     * DOUBLE has ~15 digits precision (enough for 1 trillion resources).
     */
    public function up(): void
    {
        // Planets table - main resource storage
        Schema::table('planets', function (Blueprint $table) {
            $table->double('metal')->default(0)->change();
            $table->double('crystal')->default(0)->change();
            $table->double('deuterium')->default(0)->change();
            $table->double('energy_used')->default(0)->change();
            $table->double('energy_max')->default(0)->change();
        });

        // Debris fields table
        Schema::table('debris_fields', function (Blueprint $table) {
            $table->double('metal')->default(0)->change();
            $table->double('crystal')->default(0)->change();
            $table->double('deuterium')->default(0)->change();
        });

        // Queue tables (resource costs)
        Schema::table('building_queues', function (Blueprint $table) {
            $table->double('metal')->default(0)->change();
            $table->double('crystal')->default(0)->change();
            $table->double('deuterium')->default(0)->change();
        });

        Schema::table('research_queues', function (Blueprint $table) {
            $table->double('metal')->default(0)->change();
            $table->double('crystal')->default(0)->change();
            $table->double('deuterium')->default(0)->change();
        });

        Schema::table('unit_queues', function (Blueprint $table) {
            $table->double('metal')->default(0)->change();
            $table->double('crystal')->default(0)->change();
            $table->double('deuterium')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planets', function (Blueprint $table) {
            $table->float('metal')->default(0)->change();
            $table->float('crystal')->default(0)->change();
            $table->float('deuterium')->default(0)->change();
            $table->float('energy_used')->default(0)->change();
            $table->float('energy_max')->default(0)->change();
        });

        Schema::table('debris_fields', function (Blueprint $table) {
            $table->float('metal')->default(0)->change();
            $table->float('crystal')->default(0)->change();
            $table->float('deuterium')->default(0)->change();
        });

        Schema::table('building_queues', function (Blueprint $table) {
            $table->float('metal')->default(0)->change();
            $table->float('crystal')->default(0)->change();
            $table->float('deuterium')->default(0)->change();
        });

        Schema::table('research_queues', function (Blueprint $table) {
            $table->float('metal')->default(0)->change();
            $table->float('crystal')->default(0)->change();
            $table->float('deuterium')->default(0)->change();
        });

        Schema::table('unit_queues', function (Blueprint $table) {
            $table->float('metal')->default(0)->change();
            $table->float('crystal')->default(0)->change();
            $table->float('deuterium')->default(0)->change();
        });
    }
};
