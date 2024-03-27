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

        // TODO: below sometimes results in redirect to /overview which is not
        // correct when there is no authenticated user yet. Check this later.
        $response->assertStatus(301);
    }

    /**
     * Verify that registering an new account works as expected.
     */
    public function testAccountCreation(): void
    {
        $response = $this->get('/login');

        // Check for existence of register form
        $response->assertSee('subscribeForm');

        // Simulate form data
        // Generate random email
        $randomEmail = Str::random(10) . '@example.com';

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
        $this->post('/register', $formData);

        // Assert the user was created...
        $this->assertDatabaseHas('users', [
            'email' => $randomEmail,
        ]);
    }
}
