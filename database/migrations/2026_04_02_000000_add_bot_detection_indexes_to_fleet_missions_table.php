<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * These compound indexes dramatically speed up the three bot-detection queries on
     * large fleet_missions tables. Without them each query performs a full table scan
     * and the self-joins become O(n²).
     *
     * Index strategy:
     *  - bot_detection_type_canceled_departure : Signal 1 (round-the-clock) filter +
     *                                            Signal 3 (attack side) filter
     *  - bot_detection_type_canceled_arrival   : Signal 2 (expedition return side) filter
     *  - bot_detection_user_type_canceled_departure : Signal 2 (next-expedition join) +
     *                                                 Signal 3 (response join)
     */
    public function up(): void
    {
        Schema::table('fleet_missions', function (Blueprint $table) {
            // Covers: WHERE mission_type = X AND canceled = 0 AND time_departure > Y
            $table->index(['mission_type', 'canceled', 'time_departure'], 'idx_fm_type_canceled_departure');

            // Covers: WHERE mission_type = 15 AND canceled = 0 AND time_arrival > Y  (return side of self-join)
            $table->index(['mission_type', 'canceled', 'time_arrival'], 'idx_fm_type_canceled_arrival');

            // Covers: JOIN ON user_id = X AND mission_type = Z AND canceled = 0 AND time_departure BETWEEN A AND B
            $table->index(['user_id', 'mission_type', 'canceled', 'time_departure'], 'idx_fm_user_type_canceled_departure');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fleet_missions', function (Blueprint $table) {
            $table->dropIndex('idx_fm_type_canceled_departure');
            $table->dropIndex('idx_fm_type_canceled_arrival');
            $table->dropIndex('idx_fm_user_type_canceled_departure');
        });
    }
};
