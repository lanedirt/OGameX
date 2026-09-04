# OGameX modules

Modules are optional Laravel features under `Modules/<Name>`. Keep a feature in
its module when it owns its routes, views, data, and tests.

[`Modules/HelloWorld`](../Modules/HelloWorld) is the reference module. It is
disabled by default and includes an admin route, a view, configuration, a view
slot, and module-local tests.

## Package

OGameX uses [`nwidart/laravel-modules`](https://github.com/nWidart/laravel-modules)
`^13.0`. The package handles module discovery, enabled state, service-provider
loading, and module resource loading. Do not add another module loader or status
system.

Module `composer.json` files are merged into the root Composer configuration.
Run `composer dump-autoload` after changing a module's Composer metadata.

## Create a module

Run these commands from the repository root:

```bash
php artisan module:make MyFeature
composer dump-autoload
php artisan module:enable MyFeature
php artisan module:list
```

Replace `MyFeature` with a StudlyCase name. Disable it with
`php artisan module:disable MyFeature`. The enabled state is stored in
`modules_statuses.json`.

## Module layout

```text
Modules/MyFeature/
├── app/
│   ├── Console/
│   ├── Http/Controllers/
│   ├── Jobs/
│   ├── Listeners/
│   ├── Models/
│   ├── Providers/
│   └── Services/
├── config/
├── database/migrations/
├── resources/views/
├── routes/web.php
├── tests/Feature/
├── tests/Unit/
├── composer.json
└── module.json
```

Use normal Laravel classes inside the module. Keep module domain logic and
module-owned migrations in the module. Keep providers focused on registration.

## Metadata and autoloading

`module.json` must identify the module provider:

```json
{
    "name": "MyFeature",
    "alias": "myfeature",
    "description": "What the module does.",
    "priority": 0,
    "providers": [
        "Modules\\MyFeature\\Providers\\MyFeatureServiceProvider"
    ]
}
```

The module `composer.json` must map its namespace to `app/`:

```json
{
    "autoload": {
        "psr-4": {
            "Modules\\MyFeature\\": "app/"
        }
    }
}
```

Run `composer dump-autoload` after changing Composer metadata.

The alias must match in `module.json`, `$nameLower`, view namespaces, config,
and route names.

## Provider

The main provider must call `parent::boot()`. This loads the module's config,
views, migrations, commands, schedules, and route provider.

```php
<?php

namespace Modules\MyFeature\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class MyFeatureServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'MyFeature';

    protected string $nameLower = 'myfeature';

    protected array $providers = [
        RouteServiceProvider::class,
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
```

## Routes, controllers, and views

Define routes in `routes/web.php`. The module route provider loads this file.
Use namespaced URLs and route names. For an admin page:

```php
use Illuminate\Support\Facades\Route;
use Modules\MyFeature\Http\Controllers\MyFeatureController;

Route::middleware(['auth', 'banned', 'globalgame', 'locale', 'firstlogin', 'admin'])
    ->prefix('admin/my-feature')
    ->name('myfeature.')
    ->group(function (): void {
        Route::get('/', [MyFeatureController::class, 'index'])->name('index');
    });
```

Put behavior in a controller or service:

```php
namespace Modules\MyFeature\Http\Controllers;

use Illuminate\View\View;
use OGame\Http\Controllers\OGameController;

class MyFeatureController extends OGameController
{
    public function index(): View
    {
        return view('myfeature::index', [
            'title' => config('myfeature.title', 'My Feature'),
        ]);
    }
}
```

Put the view at `resources/views/index.blade.php` and load it as
`myfeature::index`. User-facing strings must use the project's translation
conventions.

## Config and migrations

Put config in `config/config.php` and read it through the lowercase alias:

```php
return ['title' => 'My Feature'];
```

```php
config('myfeature.title', 'My Feature');
```

Put module-owned migrations in `database/migrations` and run them with the
normal Laravel command while the module is enabled:

```bash
php artisan migrate
```

Do not edit a merged migration. Add a new one.

## View slots

The supported `admin.nav` slot appends content to the admin sidebar. Register a
renderer in the module provider:

```php
use OGame\Services\ModuleSlotService;

ModuleSlotService::register('admin.nav', static function (array $data): string {
    return view('myfeature::partials.admin-nav')->render();
});
```

Slots append content. They do not replace core markup or provide general script
injection. Discuss new extension points before adding them.

## Tests

Keep module tests under `Modules/MyFeature/tests`. Run the module suite with:

```bash
php artisan test --testsuite=Modules --filter=MyFeature
```

The `HelloWorld` test shows how to enable a module before application boot. Use
an isolated status file in tests. Do not modify the tracked
`modules_statuses.json` from a test.

## Checklist

- `php artisan module:list` discovers the module.
- Enabled modules register their resources. Disabled modules have no effect.
- The alias is consistent everywhere.
- Module migrations and focused tests pass.
- User-facing strings are translatable.
- Module code does not change core schema or templates without a clear reason.

See [`CONTRIBUTING.md`](../CONTRIBUTING.md) for the full pull request checks.

## Troubleshooting

**Routes are missing:** Check that the module is enabled, the provider calls
`parent::boot()`, and `RouteServiceProvider` is listed in `$providers`.

**Views or config are missing:** Check the lowercase alias and its namespaces.

**Classes are not found:** Check the PSR-4 mapping, then run:

```bash
composer dump-autoload
```

**Caches are stale:** Run:

```bash
php artisan optimize:clear
```
