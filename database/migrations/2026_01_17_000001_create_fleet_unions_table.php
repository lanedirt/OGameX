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
        Schema::create('fleet_unions', function (Blueprint $table) {
            $table->id();

            // Creator/initiator user ID
            $table->integer('user_id', false, true);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Optional union name
            $table->string('name', 100)->nullable();

            // Target coordinates
            $table->integer('galaxy_to');
            $table->integer('system_to');
            $table->integer('position_to');
            $table->tinyInteger('planet_type_to')->default(1)->comment('1 = Planet, 3 = Moon');

            // Coordinated arrival time
            $table->integer('time_arrival')->comment('Coordinated arrival time for all fleets in the union');

            // Limits
            $table->tinyInteger('max_fleets')->default(16)->comment('Maximum number of fleets allowed in this union');
            $table->tinyInteger('max_players')->default(5)->comment('Maximum number of unique players allowed in this union');

            $table->timestamps();

            // Indexes for performance
            $table->index(['galaxy_to', 'system_to', 'position_to'], 'fleet_unions_target_index');
            $table->index('time_arrival');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fleet_unions');
    }
};
