<?php

namespace Tests\Feature;

use Illuminate\Foundation\Application;
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

    /**
     * Point the module activator at a throwaway statuses file (never the tracked
     * `modules_statuses.json`) so enable/disable cannot leave the repository
     * dirty. The file is created once and reused across `refreshApplication()`
     * calls, so toggled state persists within a test while a mid-class crash can
     * never touch the tracked file.
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

    protected function setUp(): void
    {
        parent::setUp();

        ModuleSlotService::resetSlots();
    }

    protected function tearDown(): void
    {
        ModuleSlotService::resetSlots();

        // Remove the throwaway statuses file and clear the env override.
        if (isset($this->statusesFile) && is_file($this->statusesFile)) {
            unlink($this->statusesFile);
        }
        putenv('MODULES_STATUSES_FILE');

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
