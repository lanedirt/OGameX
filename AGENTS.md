# AGENTS.md

OGameX is an open-source PHP/Laravel recreation of OGame, a browser-based space strategy MMO. It runs in Docker with MariaDB.

## Agent rules

- **Read [CONTRIBUTING.md](CONTRIBUTING.md) before preparing any pull request.** Its guidelines apply in full to agent-assisted work.
- **Help your contributor pace their pull requests.** One issue, one PR. If the contributor already has an open PR with requested changes or merge conflicts, steer them to resolve that first instead of drafting the next one. Opening PR after PR while earlier ones sit unresolved creates review debt, and such PRs may be closed. Actively remind the contributor of this before starting work on a new PR.
- **Prefer running commands through Docker.** The recommended dev setup runs everything in containers. Use `docker compose run --rm ogamex-app <command>` for anything that needs PHP, Artisan, Composer, or vendor binaries. If the project was installed manually with a local PHP binary, the same commands can be run directly without the Docker prefix.
- **Always run the full tool chain after code changes**, in this order:
  1. `composer run rector`
  2. `composer run cs`
  3. `composer run stan`
  4. `composer run tests`
- **Game accuracy matters.** OGameX faithfully recreates OGame's mechanics. Before implementing or changing any game mechanic (combat, fleet timing, resource production, building/research requirements, expeditions), verify how it works in the original game. Incorrect mechanics will not be accepted regardless of code quality.
- **Database schema changes go through new Laravel migrations.** Never edit a migration that has already been merged; add a new migration instead.
- **Do not run `composer update` or modify lock files** unless the task is explicitly a dependency upgrade.
- **After changing JS or CSS, compile the assets** with `npm run build`.

## Code style

- Use `Date::createFromTimestamp()`, not `Carbon::createFromTimestamp()` (Rector enforces this).
- Use fully qualified class name imports, not inline `\OGame\...` references (Rector enforces this).
- Use `Type|null` for nullable return types, not `?Type` (PHPStan rule).
- Match existing conventions in the file you are editing. Consistency over personal preference.

## Translations

- All user-facing strings must be translatable. Do not hardcode display text in views, controllers, or services.
- The established convention is `__('t_<file>.<key>')`, with translation files in `resources/lang/<locale>/` prefixed with `t_` (for example `t_ingame.php`, `t_resources.php`, `t_messages.php`). The `t_` prefix is a project convention that predates current contributors. Keep using it, even though it differs from typical Laravel naming.
- A smaller number of strings use literal English text as the key (for example `__('Ok')`), resolved through per-locale JSON files such as `resources/lang/fr.json`. When editing existing code, match the style already used in that area; for new code, prefer the `t_` file keys.
- When adding a new key, define it in the `en` locale files at minimum. Other locales fall back to English when a key is missing.
- **Translation content must be OGame accurate.** For English this is non-negotiable: strings must match the original OGame text exactly, including its linguistic quirks, unusual punctuation, and outright errors. Do not "correct" or polish original OGame wording. When in doubt, verify against the original game rather than writing what sounds right.
- Non-English translations may be AI generated, but treat them as placeholders until they can be replaced with the accurate text from the original OGame localization for that language.
- Mark AI translated strings so they can be found later. In `t_*.php` files, add `// AI-TRANSLATED: verify against original OGame text` above the string, or a single file header comment if the whole file is machine translated. Remove the marker only when the text has been checked against the original OGame localization. JSON translation files cannot contain comments; avoid adding AI translated strings there.

## Battle engine

- There are two battle engine implementations in `app/GameMissions/BattleEngine/`: `PhpBattleEngine` and `RustBattleEngine`, both extending the shared `BattleEngine` base class. **Any change to combat mechanics must be applied to both implementations.** Patching only one causes the engines to drift and produce different battle outcomes.
- Combat involves randomness (for example hull explosion chances use `rand()`). Tests must not hardcode expected battle outcomes such as survivor counts. Capture the actual outcome (for example the unit counts on the return mission) and assert consistency from there.

## Testing

- Fleet mission tests live in `tests/Feature/FleetDispatch/` and extend the `FleetDispatchTestCase` base class (`tests/FleetDispatchTestCase.php`). Follow the patterns in existing tests for dispatching fleets and processing arrivals.
- Fleet logic is timestamp driven. Mock time in tests with Carbon test helpers (`Carbon::setTestNow()`, `travel()`) instead of sleeping or relying on wall-clock time.
- Anything touching fleet missions, unit queues, or other state processed by concurrent requests or queue workers can race. Consider concurrency in the design, and run the race condition test commands in `app/Console/Commands/Test/` (`php artisan ogamex:test:race-condition-unitqueue`, `php artisan ogamex:test:race-condition-game-mission`) when relevant.
- Be careful with state cached in service singletons: queue jobs and long-running processes can otherwise operate on stale data.

## Repository layout

```
app/
  Actions/              - single-purpose action classes
  Console/Commands/     - Artisan commands
    Admin/              - admin utilities
    Dev/                - development helpers
    Scheduler/          - scheduled task definitions
    Test/               - race condition test commands
  Enums/                - PHP enums
  Events/               - Laravel event classes
  Facades/              - Laravel facades
  Factories/            - object factories
  GameConstants/        - game balance constants and formulas
  GameMessages/         - in-game messaging
  GameMissions/         - fleet mission logic (core gameplay)
    Abstracts/          - GameMission base class
    BattleEngine/       - combat simulation (PHP and Rust implementations)
    Models/             - mission-related enums and value objects
    [Mission].php       - one file per mission type
  GameObjects/          - game entity definitions
    BuildingObjects/    - buildings
    CivilShipObjects/   - civil ships
    DefenseObjects/     - defense units
    MilitaryShipObjects/- military ships
    ResearchObjects/    - research technologies
    StationObjects/     - station facilities
  Http/Controllers/     - request handlers
    Admin/              - admin panel controllers
  Jobs/                 - queue jobs (fleet processing, etc.)
  Models/               - Eloquent models (FleetMission, Planet, User, etc.)
  Observers/            - model event observers
  Providers/            - service providers
  Services/             - business logic services
  ViewModels/           - view model classes

tests/
  Feature/              - feature tests
    FleetDispatch/      - fleet mission tests (one per mission type)
  Unit/                 - unit tests

resources/views/
  ingame/               - game interface templates
  outgame/              - login/registration templates
```

## Key files by area

**Fleet missions** are the most complex part of the codebase:
- `app/GameMissions/Abstracts/GameMission.php` (base class, ~32K) defines shared mission behavior including `startReturn()`, `cancelMission()`, fleet event scheduling.
- Each mission type has its own class: `AttackMission`, `AcsDefendMission`, `EspionageMission`, `ExpeditionMission`, `TransportMission`, `ColonisationMission`, `DeploymentMission`, `RecycleMission`, `MissileMission`, `MoonDestructionMission`.
- `app/Services/FleetMissionService.php` (~49K) handles mission dispatch, fleet event processing, and queue management.
- `app/GameMissions/BattleEngine/` contains combat simulation with both PHP and Rust backends.
- Tests: `tests/Feature/FleetDispatch/` has one test class per mission type plus multi-defender battle scenarios.

**Planet and resource management:**
- `app/Services/PlanetService.php` (~87K) is the largest service, handles resource calculation, building, and planet state.
- `app/GameObjects/` defines all buildable/researchable objects with their requirements and stats.
- `app/GameConstants/` contains formulas and balance values.

**Controllers:**
- `app/Http/Controllers/FleetController.php` (~60K) handles fleet dispatch UI flow.
- `app/Http/Controllers/FleetEventsController.php` handles the fleet event widget.
- `app/Http/Controllers/GalaxyController.php` handles the galaxy view.

## Docker notes

- The database is exposed on port 3306 by default. If that port is already taken on your machine, set `DB_EXTERNAL_PORT` in `.env` to another port (e.g. 3308).
- Use `--no-deps` when running single-service commands to avoid restarting the database container unnecessarily.

## Composer scripts reference

| Script | Purpose |
|--------|---------|
| `composer run rector` | Automated refactoring |
| `composer run cs` | PSR-12 code style (fix) |
| `composer run cs -- --test` | PSR-12 code style (check only) |
| `composer run stan` | Static analysis |
| `composer run tests` | Run test suite |
