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
            // Change columns to float
            $table->float('metal_max', 16)->default(0)->change();
            $table->float('crystal_max', 16)->default(0)->change();
            $table->float('deuterium_max', 16)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planets', function (Blueprint $table) {
            // Revert columns back to integer
            $table->integer('metal_max')->default(0)->change();
            $table->integer('crystal_max')->default(0)->change();
            $table->integer('deuterium_max')->default(0)->change();
        });
    }
};
