<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('acs_invitations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('acs_group_id');
            $table->unsignedInteger('invited_player_id');
            $table->enum('status', ['pending', 'joined', 'declined'])->default('pending');
            $table->timestamps();

            // Foreign keys
            $table->foreign('acs_group_id')->references('id')->on('acs_groups')->onDelete('cascade');
            $table->foreign('invited_player_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index(['acs_group_id', 'status']);
            $table->index('invited_player_id');
            $table->unique(['acs_group_id', 'invited_player_id']); // Each player invited once per ACS
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acs_invitations');
    }
};
