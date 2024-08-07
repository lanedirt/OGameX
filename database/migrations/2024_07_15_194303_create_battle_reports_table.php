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
        Schema::create('battle_reports', function (Blueprint $table) {
            $table->id();
            // We store the target planet coordinates instead of the planet ID because the planet might
            // be deleted later while the report should still be available.
            $table->integer('planet_galaxy');
            $table->integer('planet_system');
            $table->integer('planet_position');
            // We store the player ID as well because we want to keep the report even if the planet which has a
            // link to the player is deleted.
            $table->integer('planet_user_id', false, true);
            $table->foreign('planet_user_id')->references('id')->on('users');
            $table->json('general')->nullable();
            $table->json('attacker')->nullable();
            $table->json('defender')->nullable();
            $table->json('rounds')->nullable();
            $table->json('loot')->nullable();
            $table->json('debris')->nullable();
            $table->json('repaired_defenses')->nullable();
            $table->json('wreckage')->nullable();

            $table->timestamps();
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->bigInteger('battle_report_id', false, true)->nullable()->after('espionage_report_id');
            $table->foreign('battle_report_id')->references('id')->on('battle_reports');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['battle_report_id']);
            $table->dropColumn('battle_report_id');
        });

        Schema::dropIfExists('battle_reports');
    }
};
