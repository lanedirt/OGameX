<?php

namespace OGame\Modules;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * Abstract base class for all OGameX module service providers.
 *
 * Modules must extend this class, implement the three abstract methods,
 * and be listed in config/modules.php to be activated.
 */
abstract class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Registry of all discovered module providers (class names), populated on construction.
     * Used by the Modules admin page to list every installed module regardless of how it was
     * registered (config/modules.php or Composer auto-discovery).
     *
     * @var string[]
     */
    private static array $discovered = [];

    /**
     * Register this provider in the discovery registry as soon as it is instantiated.
     */
    public function __construct($app)
    {
        parent::__construct($app);
        self::$discovered[static::class] = static::class;
    }

    /**
     * Return all discovered module provider class names.
     *
     * @return string[]
     */
    public static function allDiscovered(): array
    {
        return array_values(self::$discovered);
    }

    /**
     * Clear the discovery registry. Intended for use in tests only.
     */
    public static function resetDiscovered(): void
    {
        self::$discovered = [];
    }

    /**
     * Unique module ID (snake_case), e.g. 'lifeforms'.
     * Used as the namespace for views and translations.
     */
    abstract public function moduleId(): string;

    /**
     * Module boot logic — register game objects, hook implementations,
     * view slots, property bonus modifiers, etc.
     */
    abstract public function bootModule(): void;

    /**
     * Resolve a path relative to the module package root.
     * Implement as: return __DIR__ . '/' . $relative;
     */
    abstract public function modulePath(string $relative): string;

    /**
     * Whether this module has been disabled by the admin.
     */
    public function isDisabled(): bool
    {
        $file = storage_path('app/modules-disabled.json');
        if (!file_exists($file)) {
            return false;
        }
        $disabled = json_decode((string) file_get_contents($file), true) ?? [];
        return in_array(static::class, $disabled, true);
    }

    /**
     * Return the module's manifest data from module.json.
     * Falls back to minimal data derived from moduleId() if file is absent.
     */
    public function getModuleManifest(): array
    {
        $path = $this->modulePath('module.json');
        if (file_exists($path)) {
            return json_decode((string) file_get_contents($path), true) ?? [];
        }
        return ['name' => $this->moduleId(), 'description' => '', 'version' => '?'];
    }

    /**
     * Boot the module: load routes, migrations, views, translations,
     * then delegate to bootModule() for module-specific logic.
     * Returns early without doing anything if the module is disabled.
     */
    public function boot(): void
    {
        if ($this->isDisabled()) {
            return;
        }

        if (file_exists($routes = $this->modulePath('routes/web.php'))) {
            Route::middleware('web')->group($routes);
        }

        if (is_dir($migrations = $this->modulePath('database/migrations'))) {
            $this->loadMigrationsFrom($migrations);
        }

        if (is_dir($views = $this->modulePath('resources/views'))) {
            $this->loadViewsFrom($views, $this->moduleId());
        }

        if (is_dir($lang = $this->modulePath('resources/lang'))) {
            $this->loadTranslationsFrom($lang, $this->moduleId());
        }

        $this->bootModule();
    }
}
