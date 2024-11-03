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
        Schema::create('highscores', function (Blueprint $table) {
            $table->id();
            $table->integer('player_id', false, true);
            $table->foreign('player_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('general')->default(0);
            $table->bigInteger('economy')->default(0);
            $table->bigInteger('research')->default(0);
            $table->bigInteger('military')->default(0);
            $table->bigInteger('general_rank')->nullable()->default(null);
            $table->bigInteger('economy_rank')->nullable()->default(null);
            $table->bigInteger('research_rank')->nullable()->default(null);
            $table->bigInteger('military_rank')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('highscores');
    }
};
