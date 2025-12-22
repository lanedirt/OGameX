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
        Schema::create('wreck_fields', function (Blueprint $table) {
            $table->id();

            // Coordinates - unique per location
            $table->integer('galaxy');
            $table->integer('system');
            $table->integer('planet');
            $table->unique(['galaxy', 'system', 'planet']);

            // Owner who can repair the ships
            $table->integer('owner_player_id', false, true);
            $table->foreign('owner_player_id')->references('id')->on('users')->onDelete('cascade');

            // Timing
            $table->timestamp('created_at');
            $table->timestamp('expires_at'); // 72 hours lifetime
            $table->timestamp('repair_started_at')->nullable();
            $table->timestamp('repair_completed_at')->nullable();

            // Repair information
            $table->integer('space_dock_level')->nullable(); // Level when repairs started
            $table->string('status', 20)->default('active'); // active, repairing, completed, burned

            // Ship data stored as JSON
            $table->json('ship_data')->nullable(); // Array of ship types, quantities, and repair progress

            $table->index(['status', 'expires_at']);
            $table->index('owner_player_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wreck_fields');
    }
};