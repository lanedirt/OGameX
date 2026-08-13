# OGameX Modules

Build an OGameX module the same way you build a Laravel feature: routes,
controllers, migrations, models, jobs, commands, events, policies, and tests.
OGameX adds a small, game-specific integration API through `Extensions`.

This is the contributor guide. Read [Module foundation and planning](planning/modules.md)
for lifecycle internals, performance notes, and future-module planning.

## Start here

```bash
php artisan module:make MyFeature
composer dump-autoload
php artisan module:enable MyFeature
php artisan module:list
```

Disable a module with `php artisan module:disable MyFeature`.

Use `Modules/HelloWorld` as the working reference. It contains a provider,
setting, admin slot, route, view, event listener, and module-local tests.

```bash
php artisan module:enable HelloWorld
php artisan test --testsuite=Modules --filter=HelloWorld
```

## The mental model

Every module has two types of work:

1. **Laravel work** belongs in the module: pages, APIs, models, migrations,
   jobs, commands, schedules, policies, and tests.
2. **Game integration** is declared in the provider with `Extensions` when the
   feature needs an OGameX lifecycle point.

An enabled module is normal Laravel application code. There is no extra module
permission system, manifest status, or custom runtime. Modules may use public
OGameX services and normal Laravel migrations; choose the documented OGameX
integration point when it already expresses the behavior you need.

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

Keep domain logic in the module. Keep the provider small: register bindings,
Laravel infrastructure, and game capabilities.

## Provider and alias

The main provider extends `Nwidart\Modules\Support\ModuleServiceProvider`.
Call `parent::boot()` first; it loads the module’s configuration, views,
migrations, commands, and schedules.

The lowercase alias must match across `module.json`, `$nameLower`, view/config
namespaces, settings, and `Extensions::module()`.

```php
use Nwidart\Modules\Support\ModuleServiceProvider;
use OGame\Extensions\ModuleExtension;
use OGame\Facades\Extensions;

class MyFeatureServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'MyFeature';
    protected string $nameLower = 'myfeature';

    public function boot(): void
    {
        parent::boot();

        Extensions::module($this->nameLower, function (ModuleExtension $module): void {
            // Register only the OGameX capabilities this module needs.
        });
    }
}
```

## Choose the right tool

| Need | Use |
|---|---|
| Page, API, model, migration, command, job, policy | Normal Laravel code in the module |
| New building, research, ship, or defence | `$module->objects([...])` |
| Controlled change to an existing game object | `$module->extendObject(...)` |
| Content appended to a core screen | `$module->slot(...)` |
| Reaction to a completed game action | `$module->listen(...)` |
| Server-wide module configuration | `$module->setting(...)` |
| Synchronous planet production/consumption behavior | `$module->extendPlanet(...)` |
| Player behavior after the player loads | `$module->extendPlayer(...)` |
| Planet-bound game-timed work | `$module->queueProcessor(...)` and `ModuleQueues` |
| Fleet mission, game message, highscore category | `$module->mission(...)`, `message(...)`, `highscoreCategory(...)` |

## Game objects and state

Module object IDs start at `10000`; lower IDs are reserved for OGameX core.
Machine names must be globally unique lowercase `snake_case` values.

Module objects work through normal OGameX object lookups, queues, battle/unit
calculations, and views. Their levels and amounts automatically use normalized
module storage—no new `planets` or `users_tech` column is required.

Use the right persistence layer:

| Data | Storage |
|---|---|
| Module building/research level or unit amount | Automatic normalized object state |
| Small flag, cursor, cooldown, or JSON snapshot | `ModuleState` |
| Server-wide configuration | Declared module setting + scoped `SettingsService` |
| Relational, historical, query-heavy data | Module migration + Eloquent model |

`ModuleState` is namespaced by module alias and server/player/planet scope. It
is useful for compact state, not as a replacement for a proper module model.

## Settings

Declare an operator-facing server setting in the provider. OGameX namespaces it
with the module alias, validates it, and shows it in **Server settings** while
the module is enabled.

```php
$module->setting('population.tick_seconds')
    ->integer()
    ->default(60)
    ->min(5)
    ->label('Population tick interval');
```

Read it with the scoped service:

```php
$seconds = $settings->module('lifeforms')->integer('population.tick_seconds');
```

## Jobs, schedules, and queues

### Laravel jobs

Jobs under `app/Jobs` are ordinary Laravel queued jobs. The deployment includes
a scheduler and queue worker, so dispatch, retries, backoff, batching, and
queued listeners work normally.

When dispatching inside a database transaction, use Laravel’s after-commit
support. The queue connection does not enable after-commit dispatch globally;
a worker must not observe data that later rolls back.

### Scheduled commands

Define commands in the module and register schedules through the provider’s
`configureSchedules()` method. Use `withoutOverlapping()` where concurrent work
would be unsafe. AI ticks and maintenance are good scheduled-command use cases.

### Planet-bound queues

Use `ModuleQueues` and a registered `ProvidesQueueProcessor` for deterministic
work that must run in the normal planet update transaction, such as population
growth. Keep processors synchronous, fast, idempotent, and free of remote I/O.
Use a normal Laravel job for expensive or independent background work.

## Events and pages

OGameX emits typed after-commit events for building/research completion,
colonization, fleet departure/arrival/return, and mission resolution. Use a
synchronous listener only for tiny deterministic work; use queued listeners or
jobs for notifications, integrations, analytics, and planning.

Create pages using normal module routes, controllers, middleware, policies, and
views. To append content to a core screen, use an additive slot:

| Slot | Data |
|---|---|
| `layout.resources_bar` | `currentPlanet`, `currentPlayer` |
| `layout.resources_bar_js` | `currentPlanet` |
| `resources.building_section` | `planet`, `buildings` |
| `resources.production_box` | `planet` |
| `overview.planet_info` | none |
| `admin.nav` | none |

Slots append content; they do not replace arbitrary core templates.

## Core access

A module can use public core services and, if necessary, change core tables in
a normal module migration. Keep such migrations narrow and reversible; the
module owns upgrade, rollback, and conflict compatibility. Do not add a core
column merely to store a module game object—the normalized object-state
foundation already does that.

## Testing

Put module-specific tests inside the module. The `Modules` suite discovers them:

```bash
php artisan test --testsuite=Modules --filter=MyFeature
```

Test the provider, migrations, routes, authorization, settings, listeners, jobs,
and the domain behavior the module owns. When changing a shared extension point,
also add focused core integration coverage.

## Pull-request checklist

- Alias, namespaces, settings, views, and routes use the same module alias.
- Run `composer dump-autoload` after changing module Composer metadata.
- Run the module migration and focused module tests.
- Run focused core tests for shared extension points you changed.
- Make jobs, event handlers, and planet queue processors idempotent.
- Prefer module-owned state/tables before changing core schema.

For technical design, lifecycle ordering, performance limits, and Lifeforms/AI
planning, see [Module foundation and planning](planning/modules.md).
