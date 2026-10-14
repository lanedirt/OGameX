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
        Schema::create('npc_empires', function (Blueprint $table) {
            $table->id();
            // Foreign key to the NPC user account (one empire per user)
            $table->integer('user_id', false, true);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique('user_id');

            // Randomized personality traits, generated at spawn within the
            // global min/max ranges from the server settings. They shape how
            // the empire develops so that NPC empires feel like individual
            // players rather than clones.
            // Extra resources added on top of normal production (1.0 = 100%).
            $table->decimal('development_speed', 5, 2)->default(1.00);
            // Weight for aggressive actions (probing players, attacking). 0-100.
            $table->decimal('aggression', 5, 2)->default(50.00);
            // Multiplier for expansion cadence (1.0 = baseline colonization speed).
            $table->decimal('expansion_rate', 5, 2)->default(1.00);

            // Strategic memory: per-target cooldowns, current goals, etc.
            $table->json('ai_state')->nullable();
            // Timestamp of the last AI decision tick, used to space out decisions.
            $table->timestamp('last_ai_decision_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('npc_empires');
    }
};
