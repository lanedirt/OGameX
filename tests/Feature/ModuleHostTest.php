<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Nwidart\Modules\Facades\Module;
use OGame\Services\ModuleSlotService;
use Tests\TestCase;

/**
 * Proves the module host: discovery, activation, route registration, and that
 * a disabled module has no effect on the application.
 */
class ModuleHostTest extends TestCase
{
    private string $statusesFile;

    private string $originalStatuses;

    protected function setUp(): void
    {
        parent::setUp();

        ModuleSlotService::resetSlots();

        $this->statusesFile = base_path('modules_statuses.json');
        $this->originalStatuses = (string) file_get_contents($this->statusesFile);
    }

    protected function tearDown(): void
    {
        ModuleSlotService::resetSlots();

        // Restore the exact statuses file the suite started with.
        file_put_contents($this->statusesFile, $this->originalStatuses);

        parent::tearDown();
    }

    public function test_module_is_discovered_but_disabled_by_default(): void
    {
        $this->assertTrue(Module::has('HelloWorld'));
        $this->assertTrue(Module::findOrFail('HelloWorld')->isDisabled());

        // A disabled module must not register any of its routes.
        $this->assertFalse(Route::has('helloworld.index'));
    }

    public function test_enabling_a_module_registers_its_routes_and_slots(): void
    {
        Module::findOrFail('HelloWorld')->enable();
        $this->refreshApplication();

        $this->assertTrue(Route::has('helloworld.index'));
        $this->assertStringContainsString('/admin/hello-world', ModuleSlotService::render('admin.nav'));
    }

    public function test_disabling_a_module_removes_its_routes(): void
    {
        Module::findOrFail('HelloWorld')->enable();
        $this->refreshApplication();
        $this->assertTrue(Route::has('helloworld.index'));

        Module::findOrFail('HelloWorld')->disable();
        $this->refreshApplication();

        // A disabled module must not register routes, views, or providers.
        $this->assertFalse(Route::has('helloworld.index'));
    }
}
