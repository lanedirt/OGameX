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
            $table->json('debris')->nullable()->after('resources');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('espionage_reports', function (Blueprint $table) {
            $table->dropColumn('debris');
        });
    }
};
