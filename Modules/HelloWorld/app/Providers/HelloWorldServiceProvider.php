<?php

namespace Modules\HelloWorld\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use OGame\Services\ModuleSlotService;

/**
 * Reference implementation for OGameX module contributors.
 *
 * Call parent::boot() so Laravel Modules can load this module's routes,
 * views, configuration, migrations, commands, and schedules.
 */
class HelloWorldServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'HelloWorld';

    protected string $nameLower = 'helloworld';

    protected array $providers = [
        RouteServiceProvider::class,
    ];

    public function boot(): void
    {
        parent::boot();

        // Additive view slots let a module enhance a documented core view
        // without replacing the template or editing core routes.
        ModuleSlotService::register('admin.nav', static function (array $data): string {
            return view('helloworld::partials.admin-nav')->render();
        });
    }
}
