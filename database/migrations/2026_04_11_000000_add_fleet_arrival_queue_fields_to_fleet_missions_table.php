<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('fleet_missions', function (Blueprint $table) {
            $table->bigInteger('time_arrival_ms')->default(0)->after('time_arrival');
            $table->unsignedBigInteger('arrival_job_id')->nullable()->after('canceled');
            $table->unsignedBigInteger('hold_job_id')->nullable()->after('arrival_job_id');
            $table->index(['processed', 'canceled', 'time_arrival', 'time_arrival_ms'], 'fleet_missions_arrival_processing_index');
        });

        DB::table('fleet_missions')
            ->where('time_arrival_ms', 0)
            ->update(['time_arrival_ms' => DB::raw('time_arrival * 1000')]);
    }

    public function down(): void
    {
        Schema::table('fleet_missions', function (Blueprint $table) {
            $table->dropIndex('fleet_missions_arrival_processing_index');
            $table->dropColumn(['time_arrival_ms', 'arrival_job_id', 'hold_job_id']);
        });
    }
};
