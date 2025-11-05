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
        Schema::create('acs_fleet_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('acs_group_id');
            $table->unsignedBigInteger('fleet_mission_id'); // FleetMission that joined
            $table->unsignedInteger('player_id'); // Player who owns this fleet
            $table->timestamps();

            // Foreign keys
            $table->foreign('acs_group_id')->references('id')->on('acs_groups')->onDelete('cascade');
            $table->foreign('fleet_mission_id')->references('id')->on('fleet_missions')->onDelete('cascade');
            $table->foreign('player_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index('acs_group_id');
            $table->index('player_id');
            $table->unique('fleet_mission_id'); // Each fleet can only be in one ACS group
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acs_fleet_members');
    }
};
