<?php

namespace Modules\HelloWorld\Tests\Feature;

use Illuminate\Foundation\Application;
use OGame\Services\ModuleSlotService;
use Tests\IsolatedAccountTestCase;

/** This is intentionally module-local, so contributors can copy it. */
class HelloWorldRouteTest extends IsolatedAccountTestCase
{
    private string $statusesFile;

    /**
     * Enable the reference module before the application boots so its routes,
     * views, and slots are registered during boot. Doing this here (instead of a
     * mid-test `refreshApplication()`) keeps the test compatible with the
     * `DatabaseTransactions` isolation used by `IsolatedAccountTestCase`.
     *
     * The enabled status is written to a throwaway file (never the tracked
     * `modules_statuses.json`), so a mid-class crash can never leave the
     * repository dirty.
     */
    public function createApplication(): Application
    {
        // base_path() is unavailable until the app is created, so resolve the
        // project root from this file's location (Modules/HelloWorld/tests/Feature).
        $trackedFile = dirname(__DIR__, 4) . '/modules_statuses.json';
        $statuses = json_decode((string) file_get_contents($trackedFile), true);
        $statuses['HelloWorld'] = true;

        $this->statusesFile = sys_get_temp_dir() . '/modules_statuses_' . uniqid('', true) . '.json';
        file_put_contents($this->statusesFile, json_encode($statuses, JSON_PRETTY_PRINT));

        // Point the module activator at the throwaway file for this boot.
        putenv('MODULES_STATUSES_FILE=' . $this->statusesFile);

        return parent::createApplication();
    }

    protected function setUp(): void
    {
        // Clear any slot renderers leaked by a previous test before the module
        // re-registers its own during application boot below.
        ModuleSlotService::resetSlots();

        parent::setUp();
    }

    protected function tearDown(): void
    {
        ModuleSlotService::resetSlots();

        // Remove the throwaway statuses file and clear the env override.
        if (is_file($this->statusesFile)) {
            unlink($this->statusesFile);
        }
        putenv('MODULES_STATUSES_FILE');

        parent::tearDown();
    }

    public function test_admin_can_open_the_module_reference_page(): void
    {
        $this->artisan('ogamex:admin:assign-role', ['username' => $this->currentUsername]);

        $response = $this->get('/admin/hello-world');

        $response->assertOk();
        $response->assertSee('HelloWorld reference module');
        $response->assertSee('Hello from the OGameX HelloWorld module!');
    }

    public function test_module_adds_a_link_to_the_admin_navigation(): void
    {
        $this->artisan('ogamex:admin:assign-role', ['username' => $this->currentUsername]);

        $response = $this->get('/admin/server-settings');

        $response->assertOk();
        $response->assertSee('HelloWorld example');
        $response->assertSee('/admin/hello-world');
    }
}
