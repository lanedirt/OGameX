<?php

namespace Feature;

use Tests\AccountTestCase;

/**
 * Verify that the admin panel works as expected.
 */
class AdminTest extends AccountTestCase
{
    /**
     * The paths that are only accessible by admins which will be tested.
     */
    private const ADMIN_PATHS = [
        '/admin/server-settings',
        '/admin/developer-shortcuts',
    ];

    /**
     * Verify that a normal user cannot access the admin panel.
     */
    public function testNormalUserAdminAccessDenied(): void
    {
        // Remove the admin role from the current user if it has it.
        $this->artisan('ogamex:remove-admin-role', ['username' => auth()->user()->username]);

        // Verify that on overview page the admin bar doesn't show up.
        $response = $this->get('/overview');
        $response->assertDontSee('Server admin');
        $response->assertDontSee('Server settings');

        // Verify that for all admin routes the user is redirected to the overview page
        foreach (self::ADMIN_PATHS as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/overview');
        }
    }

    /**
     * Verify that an admin user can access the admin panel.
     */
    public function testAdminUserAdminAccessGranted(): void
    {
        // Create a new user and assign the admin role
        $this->artisan('ogamex:assign-admin-role', ['username' => auth()->user()->username]);

        // Verify that on overview page the admin bar shows up.
        $response = $this->get('/overview');
        $response->assertSee('Server admin');
        $response->assertSee('Server settings');

        // Verify that for all admin routes the user can access them with response 200.
        foreach (self::ADMIN_PATHS as $route) {
            $response = $this->get($route);
            $response->assertStatus(200);
        }
    }
}
