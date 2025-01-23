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
            $table->bigInteger('time_duration')->default(0)->change();
            $table->bigInteger('time_start')->default(0)->change();
            $table->bigInteger('time_end')->default(0)->change();
        });

        Schema::table('research_queues', function (Blueprint $table) {
            $table->bigInteger('time_duration')->default(0)->change();
            $table->bigInteger('time_start')->default(0)->change();
            $table->bigInteger('time_end')->default(0)->change();
        });

        Schema::table('unit_queues', function (Blueprint $table) {
            $table->bigInteger('time_duration')->default(0)->change();
            $table->bigInteger('time_start')->default(0)->change();
            $table->bigInteger('time_end')->default(0)->change();
            $table->bigInteger('time_progress')->default(0)->change();
        });

        Schema::table('fleet_missions', function (Blueprint $table) {
            $table->bigInteger('time_departure')->default(0)->change();
            $table->bigInteger('time_arrival')->default(0)->change();
        });

        Schema::table('planets', function (Blueprint $table) {
            $table->bigInteger('time_last_update')->default(0)->change();
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->bigInteger('last_activity')->change();
        });

        Schema::table('cache', function (Blueprint $table) {
            $table->bigInteger('expiration')->change();
        });

        Schema::table('cache_locks', function (Blueprint $table) {
            $table->bigInteger('expiration')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('building_queues', function (Blueprint $table) {
            $table->integer('time_duration')->default(0)->change();
            $table->integer('time_start')->default(0)->change();
            $table->integer('time_end')->default(0)->change();
        });

        Schema::table('research_queues', function (Blueprint $table) {
            $table->integer('time_duration')->default(0)->change();
            $table->integer('time_start')->default(0)->change();
            $table->integer('time_end')->default(0)->change();
        });

        Schema::table('unit_queues', function (Blueprint $table) {
            $table->integer('time_duration')->default(0)->change();
            $table->integer('time_start')->default(0)->change();
            $table->integer('time_end')->default(0)->change();
            $table->integer('time_progress')->default(0)->change();
        });

        Schema::table('fleet_missions', function (Blueprint $table) {
            $table->integer('time_departure')->default(0)->change();
            $table->integer('time_arrival')->default(0)->change();
        });

        Schema::table('planets', function (Blueprint $table) {
            $table->integer('time_last_update')->default(0)->change();
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->integer('last_activity')->change();
        });

        Schema::table('cache', function (Blueprint $table) {
            $table->integer('expiration')->change();
        });

        Schema::table('cache_locks', function (Blueprint $table) {
            $table->integer('expiration')->change();
        });
    }
};
