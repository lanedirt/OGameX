<?php

namespace Tests\Feature;

use OGame\Services\SettingsService;
use Tests\AccountTestCase;

/**
 * Test that the admin attack block save/clear endpoint works as expected.
 */
class AdminAttackBlockTest extends AccountTestCase
{
    /**
     * Assign admin role before each test so the route is accessible.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $user = auth()->user();
        if ($user === null) {
            $this->fail('No authenticated user found.');
        }
        $this->artisan('ogamex:admin:assign-role', ['username' => $user->username]);
    }

    /**
     * Reset attack block setting after each test to avoid state leaking between tests.
     */
    protected function tearDown(): void
    {
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('attack_block_until', 0);
        parent::tearDown();
    }

    /**
     * Assert that an admin can set an active attack block via the server administration form.
     */
    public function testAdminCanSetAttackBlock(): void
    {
        $response = $this->post(route('admin.server-administration.attack-block'), [
            'attack_block_until' => '01.01.2099 12:00',
        ]);

        $response->assertRedirect(route('admin.server-administration.index'));
        $response->assertSessionHas('status');

        $settingsService = resolve(SettingsService::class);
        $this->assertTrue($settingsService->attackBlockActive());
    }

    /**
     * Assert that an admin can clear an active attack block.
     */
    public function testAdminCanClearAttackBlock(): void
    {
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('attack_block_until', time() + 3600);

        $response = $this->post(route('admin.server-administration.attack-block'), [
            'clear_attack_block' => '1',
        ]);

        $response->assertRedirect(route('admin.server-administration.index'));
        $response->assertSessionHas('status');

        $settingsService = resolve(SettingsService::class);
        $this->assertFalse($settingsService->attackBlockActive());
    }

    /**
     * Assert that submitting a past end timestamp is rejected and the attack block remains inactive.
     */
    public function testAdminAttackBlockRejectsTimestampInPast(): void
    {
        $response = $this->post(route('admin.server-administration.attack-block'), [
            'attack_block_until' => '01.01.2000 12:00',
        ]);

        $response->assertRedirect(route('admin.server-administration.index'));
        $response->assertSessionHas('error');

        $settingsService = resolve(SettingsService::class);
        $this->assertFalse($settingsService->attackBlockActive());
    }
}
