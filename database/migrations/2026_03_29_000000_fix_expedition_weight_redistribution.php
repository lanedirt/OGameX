<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * Revert the redistributed weights back to official OGame percentages.
     * When pirates, aliens, and items were originally disabled (weight 0), their
     * combined 5% was redistributed across other outcomes. Now that pirates and
     * aliens are enabled, the redistribution must be removed to avoid inflated totals.
     */
    public function up(): void
    {
        $weights = [
            'expedition_weight_ships' => '17',          // was 17.9 (official: 17%)
            'expedition_weight_resources' => '35',      // was 36.8 (official: 35%)
            'expedition_weight_delay' => '7.5',         // was 7.9 (official: 7.5%)
            'expedition_weight_speedup' => '2.75',      // was 2.9 (official: 2.75%)
            'expedition_weight_nothing' => '25',        // was 26.3 (official: 25%)
            'expedition_weight_dark_matter' => '7.5',   // was 7.9 (official: 7.5%)
        ];

        foreach ($weights as $key => $value) {
            DB::table('settings')->where('key', $key)->update([
                'value' => $value,
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * Restore the redistributed weights.
     */
    public function down(): void
    {
        $weights = [
            'expedition_weight_ships' => '17.9',
            'expedition_weight_resources' => '36.8',
            'expedition_weight_delay' => '7.9',
            'expedition_weight_speedup' => '2.9',
            'expedition_weight_nothing' => '26.3',
            'expedition_weight_dark_matter' => '7.9',
        ];

        foreach ($weights as $key => $value) {
            DB::table('settings')->where('key', $key)->update([
                'value' => $value,
                'updated_at' => now(),
            ]);
        }
    }
};
