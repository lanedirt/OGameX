<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Global NPC empire feature toggles. Random trait ranges determine the
        // personality spread that each individual NPC empire rolls at spawn.
        $settings = [
            'npc_empires_enabled'      => '1',
            'npc_max_count'            => '10',
            'npc_attack_players_enabled' => '1',
            // Extra resources on top of normal production, as a fraction.
            'npc_development_speed_min' => '0.5',
            'npc_development_speed_max' => '2.5',
            // Aggression weight 0-100: higher probes and attacks players more.
            'npc_aggression_min'        => '20',
            'npc_aggression_max'        => '80',
            // Multiplier for expansion cadence.
            'npc_expansion_rate_min'    => '0.5',
            'npc_expansion_rate_max'    => '2.0',
        ];

        foreach ($settings as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'updated_at' => now()]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'npc_empires_enabled',
            'npc_max_count',
            'npc_attack_players_enabled',
            'npc_development_speed_min',
            'npc_development_speed_max',
            'npc_aggression_min',
            'npc_aggression_max',
            'npc_expansion_rate_min',
            'npc_expansion_rate_max',
        ])->delete();
    }
};
