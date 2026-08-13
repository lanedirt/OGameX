<?php

namespace Modules\HelloWorld\Providers;

use Modules\HelloWorld\Listeners\LogPlanetColonized;
use Nwidart\Modules\Support\ModuleServiceProvider;
use OGame\Events\PlanetColonized;
use OGame\Extensions\ModuleExtension;
use OGame\Facades\Extensions;

/**
 * Reference implementation for OGameX module contributors.
 *
 * The module.json alias and the alias passed to Extensions::module() must
 * match. Call parent::boot() so Laravel Modules can load this module's routes,
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

        Extensions::module($this->nameLower, function (ModuleExtension $module): void {
            // Settings are scoped as "helloworld.greeting" and automatically
            // appear in Server settings while this module is enabled.
            $module->setting('greeting')
                ->string()
                ->default('Hello from the OGameX HelloWorld module!')
                ->label('HelloWorld greeting')
                ->description('Text displayed on the HelloWorld reference page.')
                ->rules(['max:120']);

            // Additive view slots let a module enhance a documented core view
            // without replacing the template or editing core routes.
            $module->slot('admin.nav', static function (array $data): string {
                return view('helloworld::partials.admin-nav')->render();
            });

            // Domain events are dispatched after the transaction commits. Use a
            // listener for reactions rather than patching core game services.
            $module->listen(PlanetColonized::class, LogPlanetColonized::class);
        });
    }
}
