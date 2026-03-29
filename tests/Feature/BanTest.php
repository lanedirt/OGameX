<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use OGame\Models\Ban;
use OGame\Models\User;
use Tests\AccountTestCase;

/**
 * Test ban and unban functionality for the Server Administration panel.
 */
class BanTest extends AccountTestCase
{
    /** @var array<int, int> User IDs created during tests, deleted in tearDown. */
    private array $createdUserIds = [];

    protected function tearDown(): void
    {
        if (!empty($this->createdUserIds)) {
            // Banning a user triggers PlayerService::load() which creates a users_tech record.
            // Delete it before the user to avoid FK constraint violations.
            DB::table('users_tech')->whereIn('user_id', $this->createdUserIds)->delete();
            User::whereIn('id', $this->createdUserIds)->delete();
        }

        parent::tearDown();
    }

    /**
     * Creates a factory user and tracks it for cleanup in tearDown.
     *
     * @param array<string, mixed> $attributes
     */
    private function createTrackedUser(array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $this->createdUserIds[] = $user->id;
        return $user;
    }

    /**
     * Test that an admin can ban a player with a timed duration.
     */
    public function testBanPlayerWithDuration(): void
    {
        $this->artisan('ogamex:admin:assign-role', ['username' => auth()->user()->username]);

        $target = $this->createTrackedUser();

        $response = $this->post(route('admin.server-administration.ban'), [
            'username' => $target->username,
            'reason'   => 'Test ban reason',
            'duration' => '86400', // 1 day
        ]);

        $response->assertRedirect(route('admin.server-administration.index'));
        $response->assertSessionHas('status');

        $target->refresh();
        $ban = $target->currentBan();
        $this->assertNotNull($ban);
        $this->assertEquals('Test ban reason', $ban->reason);
        $this->assertNotNull($ban->banned_until);
        $this->assertTrue($ban->banned_until->isFuture());
        $this->assertTrue($target->isBanned());
        $this->assertTrue((bool) $target->vacation_mode);
    }

    /**
     * Test that an admin can ban a player permanently.
     */
    public function testBanPlayerPermanently(): void
    {
        $this->artisan('ogamex:admin:assign-role', ['username' => auth()->user()->username]);

        $target = $this->createTrackedUser();

        $response = $this->post(route('admin.server-administration.ban'), [
            'username' => $target->username,
            'reason'   => 'Permanent ban test',
            'duration' => 'permanent',
        ]);

        $response->assertRedirect(route('admin.server-administration.index'));

        $target->refresh();
        $ban = $target->currentBan();
        $this->assertNotNull($ban);
        $this->assertEquals('Permanent ban test', $ban->reason);
        $this->assertNull($ban->banned_until);
        $this->assertTrue($target->isBanned());
        $this->assertFalse((bool) $target->vacation_mode);
    }

    /**
     * Test that an admin cannot be banned via the ban endpoint.
     */
    public function testAdminCannotBeBanned(): void
    {
        $this->artisan('ogamex:admin:assign-role', ['username' => auth()->user()->username]);

        $targetAdmin = $this->createTrackedUser();
        $this->artisan('ogamex:admin:assign-role', ['username' => $targetAdmin->username]);

        $response = $this->post(route('admin.server-administration.ban'), [
            'username' => $targetAdmin->username,
            'reason'   => 'Trying to ban an admin',
            'duration' => 'permanent',
        ]);

        $response->assertRedirect(route('admin.server-administration.index'));
        $response->assertSessionHas('error');

        $targetAdmin->refresh();
        $this->assertNull($targetAdmin->currentBan());
        $this->assertFalse($targetAdmin->isBanned());
    }

    /**
     * Test that an admin can unban a player.
     */
    public function testUnbanPlayer(): void
    {
        $this->artisan('ogamex:admin:assign-role', ['username' => auth()->user()->username]);

        $target = $this->createTrackedUser();
        Ban::create(['user_id' => $target->id, 'reason' => 'Some violation', 'banned_until' => null, 'canceled' => false]);

        $response = $this->post(route('admin.server-administration.unban'), [
            'user_id' => $target->id,
        ]);

        $response->assertRedirect(route('admin.server-administration.index'));
        $response->assertSessionHas('status');

        $target->refresh();
        $this->assertFalse($target->isBanned());
    }

    /**
     * Test that vacation mode is not cleared when a player is unbanned.
     * The player must disable vacation mode manually after the ban ends.
     */
    public function testVacationModeRemainsAfterUnban(): void
    {
        $this->artisan('ogamex:admin:assign-role', ['username' => auth()->user()->username]);

        $target = $this->createTrackedUser();
        Ban::create(['user_id' => $target->id, 'reason' => 'Some violation', 'banned_until' => null, 'canceled' => false]);
        $target->vacation_mode = true;
        $target->vacation_mode_activated_at = now();
        $target->save();

        $this->post(route('admin.server-administration.unban'), [
            'user_id' => $target->id,
        ]);

        $target->refresh();
        $this->assertFalse($target->isBanned());
        $this->assertTrue((bool) $target->vacation_mode);
    }

    /**
     * Test that a banned user accessing an ingame page is logged out and redirected to login.
     */
    public function testBannedUserBlockedByMiddleware(): void
    {
        $bannedUser = $this->createTrackedUser();
        Ban::create(['user_id' => $bannedUser->id, 'reason' => 'Cheating', 'banned_until' => null, 'canceled' => false]);

        $this->be($bannedUser);

        $response = $this->get('/overview');
        $response->assertRedirect(route('login'));
    }

    /**
     * Test that a banned user cannot log in via the login form.
     */
    public function testBannedUserCannotLogin(): void
    {
        $this->post('/logout');

        $password = 'test-password-123';
        $target = $this->createTrackedUser([
            'password' => Hash::make($password),
        ]);
        Ban::create(['user_id' => $target->id, 'reason' => 'Banned for testing', 'banned_until' => null, 'canceled' => false]);

        $response = $this->post('/login', [
            'email'    => $target->email,
            'password' => $password,
        ]);

        $response->assertSessionHasErrors('email');
    }
}
