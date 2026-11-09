<?php

namespace Tests\Feature\NpcEmpire;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use OGame\Console\Commands\Admin\ManageNpcEmpires;
use OGame\Models\User;
use OGame\Services\NpcEmpire\NpcEmpireManager;
use OGame\Services\SettingsService;
use Tests\TestCase;

/**
 * Verify the admin CLI for managing NPC empires.
 */
class ManageNpcEmpiresCommandTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Verify that `ogamex:admin:npc spawn` creates NPC empires.
     */
    public function testSpawnCommandCreatesNpcEmpires(): void
    {
        // @phpstan-ignore-next-line
        $this->artisan(ManageNpcEmpires::class, ['action' => 'spawn', '--count' => 2])->expectsOutputToContain('Spawned NPC empire')->assertSuccessful();

        $this->assertSame(2, User::query()->where('is_npc', true)->count());
    }

    /**
     * Verify that `ogamex:admin:npc list` shows the empire table.
     */
    public function testListCommandShowsEmpires(): void
    {
        $manager = resolve(NpcEmpireManager::class);
        $empire = $manager->spawn('ListedNpc');

        // @phpstan-ignore-next-line
        $this->artisan(ManageNpcEmpires::class, ['action' => 'list'])->expectsOutputToContain($empire->user->username)->assertSuccessful();
    }

    /**
     * Verify that `ogamex:admin:npc remove --user=all` removes every NPC
     * empire but not regular users.
     */
    public function testRemoveAllCommandOnlyRemovesNpcUsers(): void
    {
        $manager = resolve(NpcEmpireManager::class);
        $empire = $manager->spawn();
        $regularUser = User::factory()->create(['is_npc' => false]);

        // @phpstan-ignore-next-line
        $this->artisan(ManageNpcEmpires::class, ['action' => 'remove', '--user' => 'all', '--force' => true])->assertSuccessful();

        $this->assertDatabaseMissing('users', ['id' => $empire->user_id]);
        $this->assertDatabaseHas('users', ['id' => $regularUser->id]);
    }

    /**
     * Verify that removing an unknown id fails cleanly.
     */
    public function testRemoveUnknownIdFails(): void
    {
        // @phpstan-ignore-next-line
        $this->artisan(ManageNpcEmpires::class, ['action' => 'remove', '--user' => '999999999', '--force' => true])->assertFailed();
    }

    /**
     * Verify that an invalid action fails.
     */
    public function testInvalidActionFails(): void
    {
        // @phpstan-ignore-next-line
        $this->artisan(ManageNpcEmpires::class, ['action' => 'explode'])->assertFailed();
    }

    /**
     * Verify that the scheduler command is a no-op when the feature is
     * disabled, and runs without errors when enabled.
     */
    public function testSchedulerCommandRespectsSetting(): void
    {
        $manager = resolve(NpcEmpireManager::class);
        $empire = $manager->spawn();

        // Disabled: nothing happens.
        resolve(SettingsService::class)->set('npc_empires_enabled', '0');
        // @phpstan-ignore-next-line
        $this->artisan('ogamex:scheduler:npc-empires')->assertSuccessful();
        $this->assertNull($empire->refresh()->last_ai_decision_at);

        // Enabled: empire gets processed.
        resolve(SettingsService::class)->set('npc_empires_enabled', '1');
        // @phpstan-ignore-next-line
        $this->artisan('ogamex:scheduler:npc-empires')->assertSuccessful();
        $this->assertNotNull($empire->refresh()->last_ai_decision_at);
    }
}
