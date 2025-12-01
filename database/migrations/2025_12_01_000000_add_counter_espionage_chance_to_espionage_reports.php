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
        Schema::table('espionage_reports', function (Blueprint $table) {
            $table->integer('counter_espionage_chance')->nullable()->after('defense');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('espionage_reports', function (Blueprint $table) {
            $table->dropColumn('counter_espionage_chance');
        });
    }
};
