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
        Schema::table('battle_reports', function (Blueprint $table) {
            $table->integer('planet_type')->after('planet_position')->default(1);
        });

        Schema::table('espionage_reports', function (Blueprint $table) {
            $table->integer('planet_type')->after('planet_position')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('battle_reports', function (Blueprint $table) {
            $table->dropColumn('planet_type');
        });

        Schema::table('espionage_reports', function (Blueprint $table) {
            $table->dropColumn('planet_type');
        });
    }
};
