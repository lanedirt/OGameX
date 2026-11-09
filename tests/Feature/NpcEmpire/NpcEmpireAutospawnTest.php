<?php

namespace Tests\Feature\NpcEmpire;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use OGame\Models\NpcEmpire;
use OGame\Services\NpcEmpire\NpcEmpireManager;
use OGame\Services\SettingsService;
use Tests\TestCase;

/**
 * Verify the automatic spawn-to-max behavior of NPC empires.
 */
class NpcEmpireAutospawnTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Verify that auto-spawn is a no-op when disabled (default state).
     */
    public function testAutospawnDisabledDoesNothing(): void
    {
        resolve(SettingsService::class)->set('npc_autospawn_enabled', '0');

        $manager = resolve(NpcEmpireManager::class);
        $manager->spawn();
        $before = NpcEmpire::count();

        $this->backdateNewestEmpire(72);
        $manager->processAll();

        $this->assertSame($before, NpcEmpire::count(), 'Disabled auto-spawn must not spawn.');
    }

    /**
     * Verify that auto-spawn fills up to the maximum count when the
     * interval has elapsed.
     */
    public function testAutospawnSpawnsWhenIntervalElapsed(): void
    {
        $settings = resolve(SettingsService::class);
        $settings->set('npc_autospawn_enabled', '1');
        $settings->set('npc_autospawn_interval_hours', 24);
        $settings->set('npc_max_count', 10);

        $manager = resolve(NpcEmpireManager::class);
        $manager->spawn();
        $before = NpcEmpire::count();

        $this->backdateNewestEmpire(48);
        $manager->processAll();

        $this->assertSame(
            $before + 1,
            NpcEmpire::count(),
            'Auto-spawn must add one empire per cycle when below the max and the interval elapsed.'
        );
    }

    /**
     * Verify that no spawn happens while the interval has not elapsed.
     */
    public function testAutospawnRespectsInterval(): void
    {
        $settings = resolve(SettingsService::class);
        $settings->set('npc_autospawn_enabled', '1');
        $settings->set('npc_autospawn_interval_hours', 24);

        $manager = resolve(NpcEmpireManager::class);
        $manager->spawn();
        $before = NpcEmpire::count();

        $this->backdateNewestEmpire(1);
        $manager->processAll();

        $this->assertSame($before, NpcEmpire::count(), 'Auto-spawn must respect the minimum interval.');
    }

    /**
     * Verify that no spawn happens when the maximum count is reached.
     */
    public function testAutospawnStopsAtMax(): void
    {
        $settings = resolve(SettingsService::class);
        $settings->set('npc_autospawn_enabled', '1');
        $settings->set('npc_autospawn_interval_hours', 24);
        $settings->set('npc_max_count', 1);

        $manager = resolve(NpcEmpireManager::class);
        $manager->spawn();
        $before = NpcEmpire::count();

        $this->backdateNewestEmpire(48);
        $manager->processAll();

        $this->assertSame($before, NpcEmpire::count(), 'Auto-spawn must not exceed the maximum count.');
    }

    /**
     * Backdate the newest NPC empire so the spawn interval appears elapsed.
     *
     * @param int $hours
     */
    private function backdateNewestEmpire(int $hours): void
    {
        $newest = NpcEmpire::query()->orderByDesc('created_at')->first();
        if ($newest === null) {
            return;
        }

        DB::table('npc_empires')
            ->where('id', $newest->id)
            ->update(['created_at' => now()->subHours($hours)]);
    }
}
