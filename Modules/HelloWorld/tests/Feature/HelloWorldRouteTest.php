<?php

namespace Modules\HelloWorld\Tests\Feature;

use Illuminate\Foundation\Application;
use OGame\Services\ModuleSlotService;
use Tests\IsolatedAccountTestCase;

/** This is intentionally module-local, so contributors can copy it. */
class HelloWorldRouteTest extends IsolatedAccountTestCase
{
    private string $statusesFile;

    private string $originalStatuses;

    /**
     * Enable the reference module before the application boots so its routes,
     * views, and slots are registered during boot. Doing this here (instead of a
     * mid-test `refreshApplication()`) keeps the test compatible with the
     * `DatabaseTransactions` isolation used by `IsolatedAccountTestCase`.
     */
    public function createApplication(): Application
    {
        // base_path() is unavailable until the app is created, so resolve the
        // project root from this file's location (Modules/HelloWorld/tests/Feature).
        $this->statusesFile = dirname(__DIR__, 4) . '/modules_statuses.json';
        $this->originalStatuses = (string) file_get_contents($this->statusesFile);

        $statuses = json_decode($this->originalStatuses, true);
        $statuses['HelloWorld'] = true;
        file_put_contents($this->statusesFile, json_encode($statuses, JSON_PRETTY_PRINT));

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
        // Restore the exact statuses file the suite started with.
        file_put_contents($this->statusesFile, $this->originalStatuses);

        ModuleSlotService::resetSlots();

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
