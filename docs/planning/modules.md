# OGameX Module Foundation and Planning

> This is the architecture and planning reference. Contributors should start
> with [OGameX Modules](../modules.md).

## Purpose

The module foundation allows OGameX to gain substantial features without
turning every feature into core code. It uses Laravel Modules for module
structure and Laravel lifecycle; OGameX supplies explicit integration points
where game orchestration must remain authoritative.

The intended balance is:

- Modules own their Laravel code, data, migrations, jobs, UI, tests, and
  releases.
- Core owns game transitions, ordering, validation, and shared performance
  paths.
- Defined extension points make common game integrations stable and visible.
- Modules retain normal Laravel/public-core access when deeper integration is
  genuinely required.

The module system is not a PHP sandbox. Compatibility comes from a small public
contract, focused tests, and disciplined migrations rather than fake isolation.

## Ownership model

| Concern | Owner |
|---|---|
| Module directories, activation, routes, views, translations, migrations, commands, schedules | Laravel Modules |
| Module models, jobs, listeners, policies, HTTP endpoints, module data | Module author |
| Game objects, game settings, missions, messages, player/planet hooks, highscore categories, view slots, game events | OGameX extension contract |
| Resource production, construction, fleet processing, battle, authoritative game transitions | OGameX core |

Enabled state is owned by Laravel Modules and stored in `modules_statuses.json`.
There is deliberately no second OGameX module status or permission layer.

## Boot and lifecycle model

An enabled module provider runs during normal application boot. Its parent
provider loads module configuration, views, migrations, commands, and schedules.
The module then registers OGameX contributions through the extension registry.

The registry stores registrations by module alias in module priority and
registration order. Core services consume them at named lifecycle boundaries:

| Contribution | Core consumer | When it runs |
|---|---|---|
| Game objects and object extensions | Object service | Object assembly and lookup |
| Player extension | Player service | After user, technology, and planets load |
| Planet extension | Planet service | Planet production update |
| Queue processor | Planet service | Inside planet update transaction |
| Mission/message | Standard factories | Mission/message resolution |
| Setting | Settings service + admin settings | Validation, persistence, display |
| Listener | Laravel event dispatcher | After authoritative commit |
| Slot | Blade directive | Explicit render position |
| Highscore category | Highscore service | Category and score discovery |

Disabled modules contribute none of these registrations after a fresh boot.
Disabling does not delete module data; retention and removal are an operator and
module migration policy.

## Structure and autoloading

Modules live under `Modules/<StudlyName>`. `module.json` provides display name,
lowercase alias, priority, and providers. Each module has its own Composer
autoload definition. The root Composer merge plugin merges these definitions,
so changes to a module’s Composer metadata require `composer dump-autoload`.

The standard Laravel Modules layout is the OGameX layout: application code in
`app`, migrations in `database/migrations`, views in `resources/views`, routes
in `routes`, and tests in `tests`. OGameX intentionally does not add a second
directory convention.

## Object foundation

Core game objects retain their established hot-path columns. Module objects use
normalized state. The registry identifies the module that owns an object, and
PlanetService/PlayerService route reads and writes accordingly.

The normalized object identity consists of module alias, scope, owner ID, and
machine name. Planet scope covers buildings, stations, ships, and defence;
player scope covers research. The stored amount represents either an object
level or unit quantity. Object identifiers are retained for diagnostics and
indexing. Production-capable module objects store their percentage in namespaced
state and default to one hundred percent.

This removes the need for Lifeforms-style modules to add core-table columns for
every new object. It also prevents independent modules from colliding when they
add objects, while preserving existing core-object storage and behavior.

### Performance constraint

Core object reads remain Eloquent model attribute reads. A module-object state
read is currently a database lookup. Correctness comes first, but a UI or
calculation that asks for many module objects can create an N+1 query pattern.

Before shipping an object-heavy module, add request-scoped bulk loading or a
cache of module-object rows per planet/player. It must remain coherent after
writes and preserve the authoritative database behavior. This is a targeted
optimization, not a reason to reintroduce per-object core columns.

## State, settings, and data modelling

The general module-state table is intended for small namespaced JSON values at
server, player, and planet scope: flags, cursors, cooldowns, and compact
snapshots. It is not a generic entity-attribute-value replacement.

Relational, query-heavy, historical, or reportable data needs normal
module-owned models and tables. Examples include Lifeform species/history, AI
profiles, decisions, memory, diplomacy, reports, and analytics.

Module settings are server-wide operator configuration. They are namespaced by
alias, declared in the registry, shown only for enabled modules, and validated
by the admin settings flow. Settings are not per-player or per-planet state.

## Jobs, schedules, and queues

There are three separate mechanisms.

### Laravel jobs

Module jobs are normal Laravel queue jobs. The deployment runs a queue worker,
and standard retry, backoff, batching, and queued-listener semantics apply. Jobs
should be idempotent and avoid serializing stale models.

The configured database queue does not globally enable after-commit dispatch.
Jobs created from a transaction must explicitly use Laravel after-commit support
so workers cannot observe a transaction that later rolls back.

### Schedules

Modules can register commands and schedules through their provider, and the
deployment runs a scheduler. Scheduled tasks that cannot overlap must use an
overlap lock. AI planning, maintenance, and batch orchestration belong here.

### Planet-bound queues

The module queue table contains scheduled planet-owned work. A registered queue
processor is invoked during the planet update transaction. Due rows are locked;
the processor makes one deterministic transition and completes each row.

This is for game-timed state tied to update ordering, such as population growth
or local construction. It is not a replacement for a Laravel background worker.
Processors must be synchronous, fast, deterministic, idempotent, and free of
remote I/O.

## Transactions and events

Planet updates lock the planet and process construction, resources, unit queues,
production, storage, planet extensions, and module processors in order. A hook
inside that flow is part of the authoritative transaction.

External calls, slow work, and uncommitted-state job dispatch do not belong in
that path. Modules instead react through typed after-commit events and jobs.
OGameX currently exposes after-commit events for building/research completion,
colonization, fleet departure/arrival/return, and mission resolution.

Module unit removal can participate in a standard unit collection alongside core
units. Core units use their optimized planet-table update; normalized module
units are decremented in the same transaction. This prevents fleet or batch
logic from assuming a module unit is a physical planet column.

## Views and direct core access

Modules build pages with normal Laravel routes, controllers, middleware,
policies, and Blade views. Slots provide additive, explicit render boundaries in
core screens. They are intentionally not arbitrary template overrides because
unrestricted replacement makes upgrades and module interactions ambiguous.

An advanced module may still use public core services or alter a core table in a
normal module migration. It owns migration ordering, reversibility, conflicts,
and upgrade compatibility. A documented OGameX extension point remains the
preferred route where it matches the need.

## Complex-module direction

### Lifeforms

Lifeforms can register their game objects, use normalized object state, own
species/population tables, extend production, run a planet-bound processor, add
settings/pages, and react to colonization. Its primary scaling concern is bulk
loading of module object state and deterministic population processing.

### AI players

An AI module can own profiles, strategies, cooldowns, decision logs, and memory
in module tables. Scheduled commands and regular Laravel jobs perform planning;
normal game services execute legal actions; after-commit events trigger replans.

The remaining core prerequisites for a robust AI are a public player-state
advancement service for non-HTTP ticks and stable public APIs for discovering
legal available actions. An AI should not bypass domain services by directly
writing fleet or construction queues when a core service exists.

## Testing and operational rules

Core tests own shared extension contracts and consumers: registry, object
routing, normalized state, queues, settings, and standard service behavior.
Module-local tests own provider declarations, migrations, pages, policies, jobs,
listeners, and module domain behavior.

Operationally, deployments must run migrations, the scheduler, and queue
workers. Modules must keep aliases consistent, refresh Composer autoload after
metadata changes, make transaction-bound work idempotent, and treat disabled
module data retention as an explicit policy.

## Planned foundation work

1. Bulk/request-scoped loading for object-heavy module state.
2. Public mission-ID allocation policy before third-party mission modules ship.
3. Stable player-state advancement service for non-HTTP game progression.
4. Stable available-action APIs for AI and automation modules.
5. Focused integration coverage for each extension point before it is documented.
