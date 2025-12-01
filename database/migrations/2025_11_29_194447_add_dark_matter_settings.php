<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = [
            ['key' => 'dark_matter_initial', 'value' => '8000'],
            ['key' => 'dark_matter_regen_enabled', 'value' => '0'], // Disabled by default to match official game
            ['key' => 'dark_matter_regen_amount', 'value' => '150000'],
            ['key' => 'dark_matter_regen_period', 'value' => '604800'], // 1 week in seconds
            ['key' => 'expedition_dark_matter_multiplier', 'value' => '1.0'],
            ['key' => 'expedition_dark_matter_min_pathfinder', 'value' => '300'],
            ['key' => 'expedition_dark_matter_max_pathfinder', 'value' => '400'],
            ['key' => 'expedition_dark_matter_min_no_pathfinder', 'value' => '150'],
            ['key' => 'expedition_dark_matter_max_no_pathfinder', 'value' => '200'],
            ['key' => 'commanding_staff_cost_per_week', 'value' => '42500'],
            ['key' => 'player_class_change_cost', 'value' => '500000'],
            ['key' => 'merchant_cost_per_use', 'value' => '3500'],
            ['key' => 'planet_relocation_cost', 'value' => '240000'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->insertOrIgnore($setting);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $keys = [
            'dark_matter_initial',
            'dark_matter_regen_enabled',
            'dark_matter_regen_amount',
            'dark_matter_regen_period',
            'expedition_dark_matter_multiplier',
            'expedition_dark_matter_min_pathfinder',
            'expedition_dark_matter_max_pathfinder',
            'expedition_dark_matter_min_no_pathfinder',
            'expedition_dark_matter_max_no_pathfinder',
            'commanding_staff_cost_per_week',
            'player_class_change_cost',
            'merchant_cost_per_use',
            'planet_relocation_cost',
        ];

        DB::table('settings')->whereIn('key', $keys)->delete();
    }
};
