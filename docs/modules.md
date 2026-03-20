# OGameX Module System

Modules allow features that are not part of the core OGameX codebase to be distributed as standalone Composer packages and installed on demand. The core repository provides the infrastructure; all module code lives in separate repositories.

---

## Enabling a Module

Install the module via Composer:

```bash
composer require ogamex-modules/my-module
```

Then add its ServiceProvider class to `config/modules.php`:

```php
return [
    'enabled' => [
        OGame\Modules\MyModule\MyModuleServiceProvider::class,
    ],
];
```

Modules are only activated when both the Composer package is installed **and** the class appears in the `enabled` list. This keeps production deployments intentional.

---

## Module Package Structure

```
ogamex-modules/my-module/
├── composer.json
├── module.json                         # Human-readable manifest
├── src/
│   ├── MyModuleServiceProvider.php     # Extends OGame\Modules\ModuleServiceProvider
│   ├── GameObjects/                    # New GameObject definitions
│   ├── Services/                       # Custom services (e.g. own queue service)
│   ├── Queue/                          # ProvidesQueueProcessor implementations
│   ├── Models/                         # Eloquent models
│   └── Http/
│       ├── Controllers/
│       └── ViewComposers/
├── database/
│   └── migrations/
├── resources/
│   ├── views/
│   └── lang/
├── routes/
│   └── web.php
└── tests/
```

### `module.json`

```json
{
  "name": "My Module",
  "id": "my-module",
  "description": "Description of what this module adds.",
  "version": "1.0.0",
  "ogamex_core_min": "1.0.0",
  "requires": []
}
```

### `composer.json`

```json
{
  "name": "ogamex-modules/my-module",
  "require": {
    "php": "^8.5",
    "laravel/framework": "^12.0"
  },
  "autoload": {
    "psr-4": {
      "OGame\\Modules\\MyModule\\": "src/"
    }
  }
}
```

Do not use Laravel package auto-discovery (`extra.laravel.providers`). Modules must be explicitly enabled in `config/modules.php`.

---

## Creating a ServiceProvider

All modules extend `OGame\Modules\ModuleServiceProvider` and implement three methods:

```php
namespace OGame\Modules\MyModule;

use OGame\Modules\ModuleServiceProvider;

class MyModuleServiceProvider extends ModuleServiceProvider
{
    public function moduleId(): string
    {
        return 'my-module';
    }

    protected function modulePath(string $relative): string
    {
        return dirname(__DIR__) . '/' . $relative;
    }

    public function bootModule(): void
    {
        // Register game objects, view slots, bonus modifiers, queue processors, etc.
    }
}
```

The base class automatically loads routes, migrations, views, and translations from the module package root when `boot()` is called.

---

## Registering Game Objects

To add new ships, defense, buildings, or research objects, register them with `ObjectService`:

```php
use OGame\Services\ObjectService;

public function bootModule(): void
{
    ObjectService::registerModuleObjects([
        ...MyShipObjects::get(),
        ...MyDefenseObjects::get(),
    ]);
}
```

New ships using `GameObjectType::Ship` and defense using `GameObjectType::Defense` automatically participate in the battle engine, debris calculation, and unit queue — no further core changes needed.

Planet storage uses a DB column per `machine_name` (e.g. `$planet->my_new_ship`). Provide a migration in `database/migrations/` to add these columns.

Use `$subType` on `GameObject` to semantically distinguish module objects that share a core type:

```php
$object->type    = GameObjectType::Building;
$object->subType = 'lifeform_building';
```

---

## Registering Property Bonus Modifiers

To add bonuses to ship or unit properties (attack, shield, structural integrity, cargo capacity, fuel), register a callable with `ObjectPropertyService`:

```php
use OGame\GameObjects\Services\Properties\Abstracts\ObjectPropertyService;
use OGame\GameObjects\Models\ShipObject;

public function bootModule(): void
{
    // Add 3% attack bonus per level of my_combat_tech, ships only
    ObjectPropertyService::registerBonusModifier('attack',
        function (PlayerService $player, GameObject $object): int {
            if (!($object instanceof ShipObject)) {
                return 0;
            }
            return $player->getResearchLevel('my_combat_tech') * 3;
        }
    );
}
```

The callable receives `(PlayerService $player, GameObject $object)` and returns an **int percentage**. The core applies it as `intdiv(base_value * percentage, 100)`, additive alongside the research bonus — matching the same pattern as character class bonuses.

Available property names: `attack`, `shield`, `structural_integrity`, `capacity`, `fuel`, `fuel_capacity`.

---

## Injecting into Core Views (`@moduleSlot`)

Core Blade views contain `@moduleSlot(...)` directives at agreed extension points. Register a renderer callable to inject HTML into a slot:

```php
use OGame\Services\ModuleSlotService;

public function bootModule(): void
{
    ModuleSlotService::register('layout.resources_bar', function (array $data): string {
        return view('my-module::layout.resource-tile', $data)->render();
    });
}
```

### Available Slots

| Slot name | View file | Data available |
|-----------|-----------|----------------|
| `layout.resources_bar` | `ingame/layouts/main.blade.php` | `currentPlanet`, `currentPlayer` |
| `layout.resources_bar_js` | `ingame/layouts/main.blade.php` | `currentPlanet` |
| `resources.building_section` | `ingame/resources/index.blade.php` | `planet`, `buildings` |
| `resources.production_box` | `ingame/resources/index.blade.php` | `planet` |
| `overview.planet_info` | `ingame/overview/index.blade.php` | _(none)_ |
| `admin.nav` | `ingame/layouts/admin-menu.blade.php` | _(none)_ |

---

## Bringing Your Own Queue Service

For modules with complex queue behavior (custom costs, cooldowns, slot selection), bring a fully self-contained queue service rather than routing through the core queues.

The core calls `processQueue()` on every page load during the normal queue processing cycle. Tag your implementation in `bootModule()`:

```php
use OGame\Contracts\Modules\ProvidesQueueProcessor;

// In bootModule():
app()->tag(MyBuildingQueueProcessor::class, 'module.queue_processors');
```

Implement the contract:

```php
use OGame\Contracts\Modules\ProvidesQueueProcessor;
use OGame\Services\PlanetService;

class MyBuildingQueueProcessor implements ProvidesQueueProcessor
{
    public function processQueue(PlanetService $planet): void
    {
        // Retrieve and process finished queue items for this planet
    }
}
```

For modules that only need pre-validation before an item enters a core queue (e.g. a cost check), implement `processQueue()` as a no-op and handle validation in your controller before calling the core queue service.

---

## Available Hook Contracts

All contracts live under `OGame\Contracts\Modules\`.

| Interface | Tag | Purpose |
|-----------|-----|---------|
| `ProvidesGameObjects` | _(call directly)_ | Register GameObject instances with ObjectService |
| `ExtendsPlanetService` | `module.planet_extensions` | Planet-level production calculations |
| `ExtendsPlayerService` | `module.player_extensions` | Player-level data injection |
| `ProvidesQueueProcessor` | `module.queue_processors` | Own queue processing cycle |
| `ProvidesHighscoreCategory` | `module.highscore_categories` | Add highscore categories |

---

## Admin Panel Modules

A module can provide a fully independent admin panel with its own layout, route group, and controllers. The only core integration needed is injecting nav links via the `admin.nav` slot:

```php
ModuleSlotService::register('admin.nav', function (): string {
    return view('my-module::admin.nav-links')->render();
});
```

Routes should be grouped under a prefix like `/admin-panel/...` and protected with admin middleware.
