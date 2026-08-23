<?php

namespace Tests\Feature;

use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Basic bootstrap test to see if the application can successfully bootstrap.
 */
class BootstrapTest extends TestCase
{
    /**
     * Verify that when accessing the root a redirect is issued to the login page.
     */
    public function testLoginRedirect(): void
    {
        $response = $this->get('/');
        $response->assertStatus(301);
    }

    /**
     * Verify that registering a new account works as expected.
     */
    public function testAccountCreation(): void
    {
        $response = $this->get('/login');

        // Check for existence of register form
        $response->assertSee('subscribeForm');

        // Simulate form data
        // Generate random email
        $randomEmail = strtolower(Str::random(10) . '@example.com');

        $formData = [
            '_token' => csrf_token(),
            'email' => $randomEmail,
            'password' => 'asdasdasd',
            'v' => '3',
            'step' => 'validate',
            'kid' => '',
            'errorCodeOn' => '1',
            'is_utf8' => '1',
            'agb' => 'on',
        ];

        // Submit the registration form
        $response = $this->post('/register', $formData);

        // Assert the user was created...
        $this->assertDatabaseHas('users', [
            'email' => $randomEmail,
        ]);

        // Check that the user is redirected to the overview page
        $response->assertRedirect('/overview');

        // Load overview page
        $response = $this->get('/overview');

        // Check that overview page is correctly rendered without errors.
        $response->assertStatus(200);
    }

    /**
     * Verify that registration validation errors are scoped to the "register" error bag
     * only, so they aren't also duplicated into the default bag.
     */
    public function testRegisterErrorsScopedToOwnBag(): void
    {
        $randomEmail = strtolower(Str::random(10) . '@example.com');

        $formData = [
            '_token' => csrf_token(),
            'email' => $randomEmail,
            // Password below the minimum length requirement to trigger a validation error.
            'password' => 'short',
            'v' => '3',
            'step' => 'validate',
            'kid' => '',
            'errorCodeOn' => '1',
            'is_utf8' => '1',
            'agb' => 'on',
        ];

        $response = $this->post('/register', $formData);

        $response->assertSessionHasErrorsIn('register', ['password']);

        // Assert the default bag stays empty, otherwise the error would render twice.
        $this->assertEmpty(session('errors')->getBag('default')->all());

        $this->assertDatabaseMissing('users', [
            'email' => $randomEmail,
        ]);
    }
}
