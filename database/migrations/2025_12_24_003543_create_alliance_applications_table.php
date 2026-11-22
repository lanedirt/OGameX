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
        Schema::create('alliance_applications', function (Blueprint $table) {
            $table->id();
            // Foreign key to the alliance
            $table->unsignedBigInteger('alliance_id');
            $table->foreign('alliance_id')->references('id')->on('alliances')->onDelete('cascade');
            // Foreign key to the user applying
            $table->integer('user_id', false, true);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // Application message from the user
            $table->text('application_message')->nullable();
            // Status: 0 = pending, 1 = accepted, 2 = rejected
            $table->tinyInteger('status')->default(0);
            // Whether the application has been viewed
            $table->boolean('viewed')->default(false);
            $table->timestamps();

            // Add indexes for performance
            $table->index(['alliance_id', 'status']); // For listing pending applications
            $table->index(['user_id', 'status']); // For checking user's applications
            // Prevent duplicate applications from same user to same alliance
            $table->unique(['alliance_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alliance_applications');
    }
};
