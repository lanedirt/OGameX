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
        Schema::create('ignored_players', function (Blueprint $table) {
            $table->id();
            // Foreign key to the user who is ignoring someone
            $table->integer('user_id', false, true);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // Foreign key to the user who is being ignored
            $table->integer('ignored_user_id', false, true);
            $table->foreign('ignored_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();

            // Add indexes for performance
            $table->index('user_id'); // For getting list of ignored players
            // Prevent duplicate ignore entries
            $table->unique(['user_id', 'ignored_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ignored_players');
    }
};
