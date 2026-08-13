<?php

namespace Modules\HelloWorld\Tests\Feature;

use Nwidart\Modules\Facades\Module;
use OGame\Services\ModuleSlotService;
use Tests\AccountTestCase;

/** This is intentionally module-local, so contributors can copy it. */
class HelloWorldRouteTest extends AccountTestCase
{
    private string $statusesFile;

    private string $originalStatuses;

    protected function setUp(): void
    {
        parent::setUp();

        ModuleSlotService::resetSlots();

        $this->statusesFile = base_path('modules_statuses.json');
        $this->originalStatuses = file_get_contents($this->statusesFile);

        // The reference module is disabled by default. Enable it and re-boot the
        // application so its routes, views, and slots are actually registered.
        Module::findOrFail('HelloWorld')->enable();
        $this->reloadApplication();
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
