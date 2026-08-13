<?php

namespace Tests\Feature;

use Tests\AccountTestCase;

class AdminModulesPageTest extends AccountTestCase
{
    public function test_admin_can_list_installed_modules(): void
    {
        $this->artisan('ogamex:admin:assign-role', ['username' => $this->currentUsername]);

        $response = $this->get('/admin/modules');

        $response->assertOk();
        $response->assertSee('Installed Modules');
        $response->assertSee('HelloWorld');
    }

    public function test_non_admin_is_redirected_away_from_the_modules_page(): void
    {
        $response = $this->get('/admin/modules');

        $response->assertRedirect('/overview');
    }
}
