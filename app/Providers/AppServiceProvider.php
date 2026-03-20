<?php

namespace OGame\Providers;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use OGame\Exceptions\Handler;
use OGame\Factories\PlanetServiceFactory;
use OGame\Factories\PlayerServiceFactory;
use OGame\Models\User;
use OGame\Observers\UserObserver;
use OGame\Services\ModuleSlotService;
use OGame\Services\SettingsService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    final public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Register composer file for the main ingame layout.
        view()->composer('ingame.layouts.main', 'OGame\Http\ViewComposers\IngameMainComposer');

        // Register model observers
        User::observe(UserObserver::class);

        // Register @moduleSlot Blade directive for module view injection
        Blade::directive('moduleSlot', function (string $expression): string {
            return "<?php echo \\OGame\\Services\\ModuleSlotService::render({$expression}); ?>";
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    final public function register(): void
    {
        $this->app->singleton(function ($app): SettingsService {
            return new SettingsService();
        });

        $this->app->singleton(function ($app): PlayerServiceFactory {
            return new PlayerServiceFactory();
        });

        $this->app->singleton(function ($app): PlanetServiceFactory {
            return new PlanetServiceFactory(
                $app->make(SettingsService::class),
                $app->make(PlayerServiceFactory::class)
            );
        });

        $this->app->singleton(ExceptionHandler::class, Handler::class);

        // Register bundled modules listed in config/modules.php.
        // Composer-installed modules are auto-discovered via their composer.json.
        // The ModuleServiceProvider base class handles the enabled/disabled check in boot().
        foreach (config('modules.enabled', []) as $providerClass) {
            if (is_string($providerClass) && class_exists($providerClass)) {
                $this->app->register($providerClass);
            }
        }
    }
}
