<?php

namespace Tests\Feature\NpcEmpire;

use OGame\Models\User;
use Tests\IsolatedAccountTestCase;

/**
 * Verify the admin NPC empires page: access control, spawning and
 * removal flows.
 */
class NpcEmpireAdminTest extends IsolatedAccountTestCase
{
    /**
     * Verify that a normal (non-admin) user cannot access the NPC page.
     */
    public function testNormalUserAccessDenied(): void
    {
        $authUser = auth()->user();
        if ($authUser === null) {
            $this->fail('Not authenticated.');
        }
        $this->artisan('ogamex:admin:remove-role', ['username' => $authUser->username]);

        $response = $this->get('/admin/npc-empires');
        $response->assertRedirect('/overview');
    }

    /**
     * Verify that an admin can open the NPC empires page.
     */
    public function testAdminAccessGranted(): void
    {
        $authUser = auth()->user();
        if ($authUser === null) {
            $this->fail('Not authenticated.');
        }
        $this->artisan('ogamex:admin:assign-role', ['username' => $authUser->username]);

        $response = $this->get('/admin/npc-empires');
        $response->assertStatus(200);
        $response->assertSee(__('t_ingame.admin.npc_page_title'));
    }

    /**
     * Verify that the spawn form creates new NPC empires.
     */
    public function testSpawnFormCreatesNpcEmpires(): void
    {
        $authUser = auth()->user();
        if ($authUser === null) {
            $this->fail('Not authenticated.');
        }
        $this->artisan('ogamex:admin:assign-role', ['username' => $authUser->username]);

        $before = User::query()->where('is_npc', true)->count();

        $response = $this->post('/admin/npc-empires/spawn', ['count' => 2]);
        $response->assertRedirect('/admin/npc-empires');
        $response->assertSessionHas('status');

        $this->assertSame(
            $before + 2,
            User::query()->where('is_npc', true)->count(),
            'Spawning via admin form must create exactly the requested NPC count.'
        );
    }

    /**
     * Verify that the remove form deletes an NPC empire.
     */
    public function testRemoveFormDeletesNpcEmpire(): void
    {
        $authUser = auth()->user();
        if ($authUser === null) {
            $this->fail('Not authenticated.');
        }
        $this->artisan('ogamex:admin:assign-role', ['username' => $authUser->username]);

        $manager = resolve(\OGame\Services\NpcEmpire\NpcEmpireManager::class);
        $empire = $manager->spawn();
        $removeId = $empire->user_id;

        $response = $this->post('/admin/npc-empires/remove', ['user_id' => $removeId]);
        $response->assertRedirect('/admin/npc-empires');
        $response->assertSessionHas('status');

        $this->assertDatabaseMissing('users', ['id' => $removeId]);
    }

    /**
     * Verify that removing a non-NPC user id fails cleanly.
     */
    public function testRemoveNonNpcUserFails(): void
    {
        $authUser = auth()->user();
        if ($authUser === null) {
            $this->fail('Not authenticated.');
        }
        $this->artisan('ogamex:admin:assign-role', ['username' => $authUser->username]);

        $response = $this->post('/admin/npc-empires/remove', ['user_id' => $authUser->id]);
        $response->assertRedirect('/admin/npc-empires');
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $authUser->id]);
    }
}
