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
        // These match the original hardcoded values before server settings were added
        $weights = [
            'expedition_weight_ships' => '22',           // 22% (was 220/1000)
            'expedition_weight_resources' => '32.5',     // 32.5% (was 325/1000)
            'expedition_weight_delay' => '7',            // 7% (was 70/1000)
            'expedition_weight_speedup' => '2',          // 2% (was 20/1000)
            'expedition_weight_nothing' => '26.5',       // 26.5% (was 265/1000, includes pirates/aliens)
            'expedition_weight_black_hole' => '0.3',     // 0.3% (was 3/1000)
            'expedition_weight_pirates' => '0',          // 0% (not yet implemented)
            'expedition_weight_aliens' => '0',           // 0% (not yet implemented)
            'expedition_weight_dark_matter' => '9',      // 9% (was 90/1000)
            'expedition_weight_merchant' => '0.7',       // 0.7% (was 7/1000)
            'expedition_weight_items' => '0',            // 0% (not yet implemented)
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
