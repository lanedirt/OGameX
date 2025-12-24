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
        Schema::create('alliance_members', function (Blueprint $table) {
            $table->id();
            // Foreign key to the alliance
            $table->unsignedBigInteger('alliance_id');
            $table->foreign('alliance_id')->references('id')->on('alliances')->onDelete('cascade');
            // Foreign key to the user
            $table->integer('user_id', false, true);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // Foreign key to the rank (nullable for founder who doesn't have a rank)
            $table->unsignedBigInteger('rank_id')->nullable();
            $table->foreign('rank_id')->references('id')->on('alliance_ranks')->onDelete('set null');
            // When the user joined the alliance
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();

            // Add indexes for performance
            $table->index('alliance_id'); // For listing alliance members
            $table->index('user_id'); // For looking up user's alliance
            $table->index('rank_id'); // For filtering by rank
            // Ensure a user can only be in one alliance at a time
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alliance_members');
    }
};
