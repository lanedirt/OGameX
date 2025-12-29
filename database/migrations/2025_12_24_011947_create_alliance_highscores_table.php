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
        Schema::create('alliance_highscores', function (Blueprint $table) {
            $table->id();
            // Foreign key to the alliance
            $table->unsignedBigInteger('alliance_id')->unique();
            $table->foreign('alliance_id')->references('id')->on('alliances')->onDelete('cascade');

            // Points for different categories
            $table->bigInteger('general')->default(0);
            $table->bigInteger('economy')->default(0);
            $table->bigInteger('research')->default(0);
            $table->bigInteger('military')->default(0);

            // Ranks for different categories
            $table->integer('general_rank')->nullable();
            $table->integer('economy_rank')->nullable();
            $table->integer('research_rank')->nullable();
            $table->integer('military_rank')->nullable();

            $table->timestamps();

            // Indexes for ranking queries
            $table->index('general');
            $table->index('economy');
            $table->index('research');
            $table->index('military');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alliance_highscores');
    }
};
