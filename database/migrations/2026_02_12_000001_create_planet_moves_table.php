<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planet_moves', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('planet_id');
            $table->foreign('planet_id')->references('id')->on('planets');
            $table->integer('target_galaxy');
            $table->integer('target_system');
            $table->integer('target_position');
            $table->integer('time_start');
            $table->integer('time_arrive');
            $table->boolean('canceled')->default(false);
            $table->boolean('processed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planet_moves');
    }
};
