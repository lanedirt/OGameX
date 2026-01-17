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
        Schema::table('fleet_missions', function (Blueprint $table) {
            // Add union reference for ACS Attack missions
            $table->unsignedBigInteger('union_id')->nullable()->after('mission_type');
            $table->foreign('union_id')->references('id')->on('fleet_unions')->onDelete('set null');

            // Position in the union (1 = initiator, 2-16 = participants)
            $table->tinyInteger('union_slot')->nullable()->after('union_id')
                ->comment('Slot number in the union (1-16), 1 = initiator');

            // Add index for querying fleets by union
            $table->index('union_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fleet_missions', function (Blueprint $table) {
            $table->dropForeign(['union_id']);
            $table->dropIndex(['union_id']);
            $table->dropColumn(['union_id', 'union_slot']);
        });
    }
};
