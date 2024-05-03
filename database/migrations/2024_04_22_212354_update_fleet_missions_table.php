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
            // A parent_id column to store the ID of the parent mission. If a mission has a parent it means
            // it is a follow-up mission (e.g. return mission after a transport mission).
            // Adding an index to parent_id to ensure it can be used as a foreign key
            $table->bigInteger('parent_id')->unsigned()->nullable()->after('id');
        });

        Schema::table('fleet_missions', function (Blueprint $table) {
            // Add foreign key constraint
            $table->foreign('parent_id')->references('id')->on('fleet_missions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fleet_missions', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
        });

        Schema::table('fleet_missions', function (Blueprint $table) {
            $table->dropColumn('parent_id');
        });
    }
};
