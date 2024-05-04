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
        Schema::table('fleet_missions', function (Blueprint $table) {
            // From coordinates are required in case a colonization mission is canceled and
            // therefore the fleet "returns" from a planet that does not exist.
            $table->integer('galaxy_from')->after('planet_id_from')->nullable();
            $table->integer('system_from')->after('galaxy_from')->nullable();
            $table->integer('position_from')->after('system_from')->nullable();

            // Make planet_id_from nullable as it is not available for all mission types.
            $table->integer('planet_id_from', false, true)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fleet_missions', function (Blueprint $table) {
            $table->dropColumn('galaxy_from');
            $table->dropColumn('system_from');
            $table->dropColumn('position_from');
        });
    }
};
