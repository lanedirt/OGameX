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
        Schema::create('buddy_requests', function (Blueprint $table) {
            $table->id();
            // Foreign key to the user who sent the buddy request
            $table->integer('sender_user_id', false, true);
            $table->foreign('sender_user_id')->references('id')->on('users')->onDelete('cascade');
            // Foreign key to the user who received the buddy request
            $table->integer('receiver_user_id', false, true);
            $table->foreign('receiver_user_id')->references('id')->on('users')->onDelete('cascade');
            // Status: 0 = pending, 1 = accepted, 2 = rejected
            $table->tinyInteger('status')->default(0);
            // Optional message sent with the buddy request
            $table->text('message')->nullable();
            // Whether the request has been viewed by the receiver
            $table->boolean('viewed')->default(false);
            $table->timestamps();

            // Add indexes for performance
            $table->index(['receiver_user_id', 'status']); // For checking pending requests
            $table->index(['sender_user_id', 'status']); // For checking sent requests
            // Prevent duplicate buddy requests between same users
            $table->unique(['sender_user_id', 'receiver_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buddy_requests');
    }
};
