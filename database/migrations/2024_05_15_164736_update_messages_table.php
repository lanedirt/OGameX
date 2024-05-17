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
        // Drop old table definition.
        Schema::dropIfExists('messages');

        // Create new table definition.
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            // Foreign key to the user that this message belongs to.
            $table->integer('user_id', false, true);
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('key');
            // Subject can contain inline planet ID or a user ID which will be expanded to planet name or username by the game engine.
            $table->text('subject')->nullable();
            // Foreign key to planet that this message actions should be performed on (attack, spy etc). Null if not applicable.
            $table->integer('action_planet_id', false, true)->nullable();
            $table->foreign('action_planet_id')->references('id')->on('planets');
            // Foreign key sender user ID (e.g. if the message is from a user in alliance circular message context).
            $table->integer('sender_user_id', false, true)->nullable();
            $table->foreign('sender_user_id')->references('id')->on('users');
            // Foreign key alliance ID (e.g. if message is from alliance notification context).
            // TODO: add when alliances are implemented.
            $table->text('body')->nullable();
            $table->text('params')->nullable();
            $table->boolean('viewed')->default(false);
            // TODO: message can be shared with alliance members with certain rank. Have to add status columns for this.
            $table->timestamps();

            // Add indexes for performance:
            $table->index(['user_id', 'key', 'created_at']); // For message listing
            $table->index(['user_id', 'key', 'viewed']); // For unread messages count
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop new table definition.
        Schema::dropIfExists('messages');

        // Recreate old table definition.
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            // Foreign key to the user that this message belongs to.
            $table->integer('user_id', false, true);
            $table->foreign('user_id')->references('id')->on('users');
            // Message type.
            $table->tinyInteger('type');
            // Subject can contain inline planet ID or a user ID which will be expanded to planet name or username by the game engine.
            $table->text('subject');
            // Foreign key to planet that this message actions should be performed on (attack, spy etc). Null if not applicable.
            $table->integer('action_planet_id', false, true)->nullable();
            $table->foreign('action_planet_id')->references('id')->on('planets');
            // Foreign key sender user ID (e.g. if the message is from a user in alliance circular message context).
            $table->integer('sender_user_id', false, true)->nullable();
            $table->foreign('sender_user_id')->references('id')->on('users');
            // Foreign key alliance ID (e.g. if message is from alliance notification context).
            // TODO: add when alliances are implemented.
            $table->text('body');
            $table->boolean('viewed')->default(false);
            // TODO: message can be shared with alliance members with certain rank. Have to add status columns for this.
            $table->timestamps();

            // Add indexes for performance:
            $table->index(['user_id', 'type', 'viewed']); // For unread messages count
            $table->index(['user_id', 'type', 'created_at']); // For message listing
        });
    }
};
