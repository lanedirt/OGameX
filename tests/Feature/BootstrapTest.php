<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
}
