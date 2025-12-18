<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // Insert bonus expedition slots setting
        DB::table('settings')->updateOrInsert(
            ['key' => 'bonus_expedition_slots'],
            ['value' => '0', 'created_at' => now(), 'updated_at' => now()]
        );

        // Insert expedition reward multiplier settings
        $rewardMultipliers = [
            'expedition_reward_multiplier_resources' => '1.0',
            'expedition_reward_multiplier_ships' => '1.0',
            'expedition_reward_multiplier_dark_matter' => '1.0',
            'expedition_reward_multiplier_items' => '1.0',
        ];

        foreach ($rewardMultipliers as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        // Insert expedition outcome weight settings with default values
        // These are based on official OGame percentages with unimplemented outcomes redistributed
        $weights = [
            'expedition_weight_ships' => '17.9',         // 17% official + redistribution
            'expedition_weight_resources' => '36.8',     // 35% official + redistribution
            'expedition_weight_delay' => '7.9',          // 7.5% official + redistribution
            'expedition_weight_speedup' => '2.9',        // 2.75% official + redistribution
            'expedition_weight_nothing' => '26.3',       // 25% official + redistribution
            'expedition_weight_black_hole' => '0.2',     // 0.2% official
            'expedition_weight_pirates' => '0',          // 0% (not yet implemented, official: 3%)
            'expedition_weight_aliens' => '0',           // 0% (not yet implemented, official: 1.5%)
            'expedition_weight_dark_matter' => '7.9',    // 7.5% official + redistribution
            'expedition_weight_merchant' => '0.4',       // 0.4% official
            'expedition_weight_items' => '0',            // 0% (not yet implemented, official: 0.5%)
        ];

        foreach ($weights as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        // Remove old expedition outcome enable/disable checkboxes
        // These are replaced by the weight system (weight = 0 means disabled)
        $oldSettings = [
            'expedition_failed',
            'expedition_failed_and_delay',
            'expedition_failed_and_speedup',
            'expedition_gain_ships',
            'expedition_gain_dark_matter',
            'expedition_gain_resources',
            'expedition_gain_merchant_trade',
            'expedition_gain_item',
            'expedition_loss_of_fleet',
        ];

        foreach ($oldSettings as $key) {
            DB::table('settings')->where('key', $key)->delete();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Remove bonus expedition slots
        DB::table('settings')->where('key', 'bonus_expedition_slots')->delete();

        // Remove reward multiplier settings
        $rewardMultipliers = [
            'expedition_reward_multiplier_resources',
            'expedition_reward_multiplier_ships',
            'expedition_reward_multiplier_dark_matter',
            'expedition_reward_multiplier_items',
        ];

        foreach ($rewardMultipliers as $key) {
            DB::table('settings')->where('key', $key)->delete();
        }

        // Remove all weight settings
        $weights = [
            'expedition_weight_ships',
            'expedition_weight_resources',
            'expedition_weight_delay',
            'expedition_weight_speedup',
            'expedition_weight_nothing',
            'expedition_weight_black_hole',
            'expedition_weight_pirates',
            'expedition_weight_aliens',
            'expedition_weight_dark_matter',
            'expedition_weight_merchant',
            'expedition_weight_items',
        ];

        foreach ($weights as $key) {
            DB::table('settings')->where('key', $key)->delete();
        }

        // Restore old settings with default enabled state
        $oldSettings = [
            'expedition_failed' => '1',
            'expedition_failed_and_delay' => '1',
            'expedition_failed_and_speedup' => '1',
            'expedition_gain_ships' => '1',
            'expedition_gain_dark_matter' => '1',
            'expedition_gain_resources' => '1',
            'expedition_gain_merchant_trade' => '0',
            'expedition_gain_item' => '0',
            'expedition_loss_of_fleet' => '1',
        ];

        foreach ($oldSettings as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
};
