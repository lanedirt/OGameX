<?php

namespace Tests\Feature;

use Illuminate\Foundation\Application;
use Nwidart\Modules\Facades\Module;
use Tests\IsolatedAccountTestCase;

class AdminModulesPageTest extends IsolatedAccountTestCase
{
    private string $statusesFile;

    /**
     * Point the module activator at a throwaway statuses file (never the tracked
     * `modules_statuses.json`) so toggling a module cannot leave the repository
     * dirty, and so toggled state does not leak between tests.
     */
    public function createApplication(): Application
    {
        if (!isset($this->statusesFile)) {
            $trackedFile = dirname(__DIR__, 2) . '/modules_statuses.json';
            $this->statusesFile = sys_get_temp_dir() . '/modules_statuses_' . uniqid('', true) . '.json';
            copy($trackedFile, $this->statusesFile);
        }

        putenv('MODULES_STATUSES_FILE=' . $this->statusesFile);

        return parent::createApplication();
    }

    protected function tearDown(): void
    {
        if (isset($this->statusesFile) && is_file($this->statusesFile)) {
            unlink($this->statusesFile);
        }
        putenv('MODULES_STATUSES_FILE');

        parent::tearDown();
    }

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

    public function test_admin_can_enable_a_module(): void
    {
        $this->artisan('ogamex:admin:assign-role', ['username' => $this->currentUsername]);

        $this->assertTrue(Module::findOrFail('HelloWorld')->isDisabled());

        $response = $this->post('/admin/modules/toggle', ['module' => 'HelloWorld']);

        $response->assertRedirect('/admin/modules');
        $response->assertSessionHas('success', 'Module enabled.');
        $this->assertTrue(Module::findOrFail('HelloWorld')->isEnabled());
    }

    public function test_admin_can_disable_a_module(): void
    {
        $this->artisan('ogamex:admin:assign-role', ['username' => $this->currentUsername]);

        Module::findOrFail('HelloWorld')->enable();
        $this->assertTrue(Module::findOrFail('HelloWorld')->isEnabled());

        $response = $this->post('/admin/modules/toggle', ['module' => 'HelloWorld']);

        $response->assertRedirect('/admin/modules');
        $response->assertSessionHas('success', 'Module disabled.');
        $this->assertTrue(Module::findOrFail('HelloWorld')->isDisabled());
    }

    public function test_toggle_rejects_an_invalid_module_name(): void
    {
        $this->artisan('ogamex:admin:assign-role', ['username' => $this->currentUsername]);

        $response = $this->post('/admin/modules/toggle', ['module' => 'DoesNotExist']);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Invalid module name.');
    }

    public function test_non_admin_cannot_toggle_a_module(): void
    {
        $response = $this->post('/admin/modules/toggle', ['module' => 'HelloWorld']);

        $response->assertRedirect('/overview');
    }
}
