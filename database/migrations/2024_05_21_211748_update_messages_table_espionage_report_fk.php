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
        Schema::table('messages', function (Blueprint $table) {
            $table->bigInteger('espionage_report_id', false, true)->nullable()->after('params');
            $table->foreign('espionage_report_id')->references('id')->on('espionage_reports');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['espionage_report_id']);
            $table->dropColumn('espionage_report_id');
        });
    }
};
