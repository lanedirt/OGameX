<?php

namespace Modules\HelloWorld\Tests\Unit;

use OGame\Extensions\ExtensionRegistry;
use OGame\Services\ModuleSlotService;
use Tests\TestCase;

/** Module-local reference tests for contributors to copy. */
class HelloWorldModuleTest extends TestCase
{
    public function test_enabled_module_registers_its_setting_and_admin_slot(): void
    {
        $extensions = app(ExtensionRegistry::class);

        $this->assertArrayHasKey('helloworld.greeting', $extensions->settings());
        $this->assertStringContainsString('/admin/hello-world', ModuleSlotService::render('admin.nav'));
    }
}
