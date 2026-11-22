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
            // Change columns to unsigned big integer to support resource values above 2^31 (~2.1 billion)
            $table->unsignedBigInteger('metal')->default(0)->change();
            $table->unsignedBigInteger('crystal')->default(0)->change();
            $table->unsignedBigInteger('deuterium')->default(0)->change();
            $table->unsignedBigInteger('metal_production')->default(0)->change();
            $table->unsignedBigInteger('crystal_production')->default(0)->change();
            $table->unsignedBigInteger('deuterium_production')->default(0)->change();
            $table->unsignedBigInteger('energy_used')->default(0)->change();
            $table->unsignedBigInteger('energy_max')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planets', function (Blueprint $table) {
            // Revert columns back to their previous types
            $table->float('metal', 16)->default(0)->change();
            $table->float('crystal', 16)->default(0)->change();
            $table->float('deuterium', 16)->default(0)->change();
            $table->integer('metal_production')->default(0)->change();
            $table->integer('crystal_production')->default(0)->change();
            $table->integer('deuterium_production')->default(0)->change();
            $table->integer('energy_used')->default(0)->change();
            $table->integer('energy_max')->default(0)->change();
        });
    }
};
