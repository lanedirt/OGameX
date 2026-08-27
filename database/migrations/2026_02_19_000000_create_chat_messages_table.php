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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            // Sender of the message
            $table->integer('sender_id', false, true);
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            // Recipient for direct messages (null for alliance messages)
            $table->integer('recipient_id', false, true)->nullable();
            $table->foreign('recipient_id')->references('id')->on('users')->onDelete('cascade');
            // Alliance for alliance chat messages (null for direct messages)
            $table->unsignedBigInteger('alliance_id')->nullable();
            $table->foreign('alliance_id')->references('id')->on('alliances')->onDelete('cascade');
            // Message content
            $table->text('message');
            // Reply reference
            $table->unsignedBigInteger('reply_to_id')->nullable();
            $table->foreign('reply_to_id')->references('id')->on('chat_messages')->onDelete('set null');
            // Read tracking
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['sender_id', 'recipient_id']);
            $table->index(['recipient_id', 'sender_id']);
            $table->index(['alliance_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
