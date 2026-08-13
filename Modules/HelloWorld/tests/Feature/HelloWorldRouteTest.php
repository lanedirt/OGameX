<?php

namespace Modules\HelloWorld\Tests\Feature;

use Tests\AccountTestCase;

/** This is intentionally module-local, so contributors can copy it. */
class HelloWorldRouteTest extends AccountTestCase
{
    public function test_admin_can_open_the_module_reference_page(): void
    {
        $this->artisan('ogamex:admin:assign-role', ['username' => $this->currentUsername]);

        $response = $this->get('/admin/hello-world');

        $response->assertOk();
        $response->assertSee('HelloWorld reference module');
        $response->assertSee('Hello from the OGameX HelloWorld module!');
    }

    public function test_admin_sees_the_module_setting_in_server_settings(): void
    {
        $this->artisan('ogamex:admin:assign-role', ['username' => $this->currentUsername]);

        $response = $this->get('/admin/server-settings');

        $response->assertOk();
        $response->assertSee('HelloWorld greeting');
        $response->assertSee('name="helloworld_greeting"', false);
    }
}
