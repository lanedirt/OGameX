<?php

namespace Tests\Feature;

use OGame\Models\User;
use Tests\IsolatedAccountTestCase;

/**
 * Test the isolated account user-creation behavior.
 */
class IsolatedAccountUserTest extends IsolatedAccountTestCase
{
    /**
     * Verify that factory-created users are not silently promoted to admin.
     *
     * The User model's `created` hook promotes the first non-Legor user to admin.
     * `IsolatedAccountTestCase::createUser()` creates via `User::withoutEvents(...)`
     * to skip that hook, so factory users must never come back as admin.
     */
    public function testFactoryCreatedUserIsNotAdmin(): void
    {
        $user = $this->createUser();

        $this->assertInstanceOf(User::class, $user);
        $this->assertFalse($user->hasRole('admin'), 'Factory user should not be an admin.');
    }
}
