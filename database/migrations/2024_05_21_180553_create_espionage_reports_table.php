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
        Schema::create('espionage_reports', function (Blueprint $table) {
            $table->id();
            // We store the target planet coordinates instead of the planet ID because the planet might
            // be deleted later while the report should still be available.
            $table->integer('planet_galaxy');
            $table->integer('planet_system');
            $table->integer('planet_position');
            // We store the player ID as well because we want to keep the report even if the planet which has a
            // link to the player is deleted.
            $table->integer('planet_user_id', false, true);
            $table->foreign('planet_user_id')->references('id')->on('users');
            $table->json('player_info');
            $table->json('resources');
            $table->json('buildings')->nullable();
            $table->json('research')->nullable();
            $table->json('ships')->nullable();
            $table->json('defense')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('espionage_reports');
    }
};
