# OGameX Modules

OGameX uses [nwidart/laravel-modules](https://github.com/nWidart/laravel-modules)
to organize optional, independently shipped features. A module is an ordinary
Laravel application packaged under `Modules/<StudlyName>`: routes, controllers,
models, migrations, jobs, commands, events, policies, views, and tests.

The module host only answers three questions:

1. which modules exist;
2. which modules are enabled;
3. how a module's Laravel code is loaded.

It deliberately does not know anything about OGameX gameplay systems.

## Start here

```bash
php artisan module:make MyFeature
composer dump-autoload
php artisan module:enable MyFeature
php artisan module:list
```

Disable a module with `php artisan module:disable MyFeature`.

Use `Modules/HelloWorld` as the working reference. It contains a provider, an
admin route, a view, module configuration, an `admin.nav` view slot, and
module-local tests.

```bash
php artisan module:enable HelloWorld
php artisan test --testsuite=Modules --filter=HelloWorld
```

## The mental model

An **enabled** module is normal Laravel application code. When a module is
enabled, its service provider boots and its routes, views, configuration,
migrations, commands, and schedules are loaded. A **disabled** module registers
nothing; it has no effect on the application.

Enabled state is owned by Laravel Modules and stored in `modules_statuses.json`.
There is no second OGameX module status or permission layer.

## Structure

```text
Modules/MyFeature/
├── app/
│   ├── Console/              # Commands and scheduled work
│   ├── Http/Controllers/     # Pages and APIs
│   ├── Jobs/                 # Laravel jobs
│   ├── Listeners/            # Event listeners
│   ├── Models/               # Module-owned data
│   ├── Providers/            # Module and route providers
│   └── Services/             # Module domain logic
├── config/
├── database/migrations/
├── resources/views/
├── routes/web.php
├── tests/Feature/
├── tests/Unit/
├── composer.json
└── module.json
```

Keep domain logic in the module. Keep the provider small: register bindings and
Laravel infrastructure.

## Provider and alias

The main provider extends `Nwidart\Modules\Support\ModuleServiceProvider`.
Call `parent::boot()` first; it loads the module's configuration, views,
migrations, commands, and schedules.

The lowercase alias must match across `module.json`, `$nameLower`, and the
module's view/config namespaces.

```php
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

`module.json` provides the display name, lowercase alias, priority, and the
provider classes to register:

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

Each module declares its own PSR-4 autoloading in its `composer.json`. The root
Composer merge plugin merges these definitions, so after changing a module's
Composer metadata run `composer dump-autoload`.

## Routes, views, and configuration

Routes live in `routes/web.php` and are registered by the module's route
provider, exactly like core routes. Views use the `myfeature::` namespace.
Configuration is published under the `myfeature` config namespace and is read
with `config('myfeature.key', $default)`.

A module may register its own migrations under `database/migrations`; they are
run by the normal migration commands while the module is enabled.

## Admin modules page

While signed in as an administrator, `/admin/modules` lists every discovered
module with its name, alias, version, priority, and enabled state. Modules can
be enabled and disabled from this page; the state is written to
`modules_statuses.json` (a plain file, so it is readable before the database is
available during boot).

## View slots

A core Blade view can render a named slot with `@moduleSlot('slot.name')`. A
module provider registers a renderer for that slot, which returns a plain HTML
string appended at the slot position.

This foundation exposes a single, clearly controlled slot:

| Slot | Purpose |
|---|---|
| `admin.nav` | After the existing items in the admin sidebar |

Slots are additive: they append content at a fixed, documented position and do
not replace core markup or inject scripts.

```php
use OGame\Services\ModuleSlotService;

ModuleSlotService::register('admin.nav', function (array $data): string {
    return view('myfeature::partials.admin-nav')->render();
});
```

## Testing

Put module-specific tests inside the module. The `Modules` suite discovers them:

```bash
php artisan test --testsuite=Modules --filter=MyFeature
```

Test the provider, migrations, routes, authorization, and the domain behavior
the module owns. Core tests cover the module host itself: discovery,
enable/disable, route registration, and that a disabled module has no effect.

## Pull-request checklist

- The alias matches across `module.json`, `$nameLower`, view/config namespaces,
  and route names.
- Run `composer dump-autoload` after changing module Composer metadata.
- Run the module migration and focused module tests.
- A disabled module must not register routes, views, or providers.
- Prefer module-owned tables and migrations over changing core schema.
