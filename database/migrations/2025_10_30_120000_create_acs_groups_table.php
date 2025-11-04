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
        Schema::create('acs_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // User-defined name for the ACS attack
            $table->unsignedInteger('creator_id'); // Player who created the ACS group

            // Target coordinates
            $table->unsignedInteger('galaxy_to');
            $table->unsignedInteger('system_to');
            $table->unsignedInteger('position_to');
            $table->unsignedTinyInteger('type_to'); // 1=planet, 2=debris, 3=moon

            // Timing
            $table->integer('arrival_time'); // When all fleets arrive together

            // Status
            $table->enum('status', ['pending', 'active', 'completed', 'cancelled'])->default('pending');

            $table->timestamps();

            // Foreign key
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index(['creator_id', 'status']);
            $table->index('arrival_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acs_groups');
    }
};
