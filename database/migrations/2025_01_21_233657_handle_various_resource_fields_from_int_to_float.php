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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('building_queues', function (Blueprint $table) {
            $table->integer('metal')->default(0)->change();
            $table->integer('crystal')->default(0)->change();
            $table->integer('deuterium')->default(0)->change();
        });
        Schema::table('research_queues', function (Blueprint $table) {
            $table->integer('metal')->default(0)->change();
            $table->integer('crystal')->default(0)->change();
            $table->integer('deuterium')->default(0)->change();
        });
        Schema::table('unit_queues', function (Blueprint $table) {
            $table->integer('metal')->default(0)->change();
            $table->integer('crystal')->default(0)->change();
            $table->integer('deuterium')->default(0)->change();
        });
    }
};