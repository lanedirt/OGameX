<?php

namespace OGame\Extensions;

use Illuminate\Support\Facades\Event;
use InvalidArgumentException;
use OGame\Contracts\Modules\ExtendsPlanetService;
use OGame\Contracts\Modules\ExtendsPlayerService;
use OGame\Contracts\Modules\ProvidesHighscoreCategory;
use OGame\Contracts\Modules\ProvidesQueueProcessor;
use OGame\GameMessages\Abstracts\GameMessage;
use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameObjects\Models\Abstracts\GameObject;
use OGame\Services\ModuleSlotService;

/**
 * Fluent builder for everything a single module contributes to OGameX.
 *
 * Instances are created and owned by the ExtensionRegistry. A module provider
 * receives one in the Extensions::module() callback and describes its
 * contributions declaratively. The alias is supplied by the registry and must
 * match the module's module.json alias.
 */
class ModuleExtension
{
    /** @var array<GameObject> */
    private array $objects = [];

    /** @var array<string, array<callable>> */
    private array $objectExtensions = [];

    /** @var array<int, class-string<GameMission>> */
    private array $missions = [];

    /** @var array<string, class-string<GameMessage>> */
    private array $messages = [];

    /** @var array<string, SettingDefinition> Keyed by the namespaced "{alias}.{key}" setting key. */
    private array $settings = [];

    /** @var array<string, array<class-string>> */
    private array $listeners = [];

    /** @var array<class-string> */
    private array $planetExtensions = [];

    /** @var array<class-string> */
    private array $playerExtensions = [];

    /** @var array<class-string> */
    private array $queueProcessors = [];

    /** @var array<class-string> */
    private array $highscoreCategories = [];

    public function __construct(public readonly string $alias)
    {
    }

    /**
     * Register new game objects. Each object must be a GameObject subclass with
     * a globally unique numeric ID and machine name.
     *
     * @param array<GameObject> $objects
     */
    public function objects(array $objects): static
    {
        foreach ($objects as $object) {
            if (!$object instanceof GameObject) {
                throw new InvalidArgumentException(sprintf(
                    'Module [%s] registered an invalid game object: expected %s, got %s.',
                    $this->alias,
                    GameObject::class,
                    get_debug_type($object),
                ));
            }

            if ($object->id < 10000) {
                throw new InvalidArgumentException(sprintf(
                    'Module [%s] registered object ID [%d]. Module object IDs must be 10000 or higher to leave core IDs available.',
                    $this->alias,
                    $object->id,
                ));
            }

            if (!preg_match('/^[a-z][a-z0-9_]*$/', $object->machine_name)) {
                throw new InvalidArgumentException(sprintf(
                    'Module [%s] registered invalid machine name [%s]. Use lowercase snake_case.',
                    $this->alias,
                    $object->machine_name,
                ));
            }

            $this->objects[] = $object;
        }

        return $this;
    }

    /**
     * Mutate a documented, supported aspect of an existing object.
     * The callable receives the fully assembled GameObject instance.
     */
    public function extendObject(string $machineName, callable $extension): static
    {
        if ($machineName === '') {
            throw new InvalidArgumentException(sprintf('Module [%s] cannot extend an object with an empty machine name.', $this->alias));
        }

        $this->objectExtensions[$machineName][] = $extension;

        return $this;
    }

    /**
     * Append HTML at a named, documented core view slot.
     */
    public function slot(string $name, callable $renderer): static
    {
        ModuleSlotService::register($name, $renderer);

        return $this;
    }

    /**
     * Register a new mission type resolved by the standard mission factory.
     *
     * @param class-string<GameMission> $missionClass
     */
    public function mission(int $id, string $missionClass): static
    {
        $this->assertClassImplements($missionClass, GameMission::class, 'mission');

        if (isset($this->missions[$id])) {
            throw new InvalidArgumentException(sprintf('Module [%s] already registered mission ID [%d].', $this->alias, $id));
        }

        $this->missions[$id] = $missionClass;

        return $this;
    }

    /**
     * Register a new game message resolved by the standard message factory.
     *
     * @param class-string<GameMessage> $messageClass
     */
    public function message(string $key, string $messageClass): static
    {
        if ($key === '') {
            throw new InvalidArgumentException(sprintf('Module [%s] cannot register a message with an empty key.', $this->alias));
        }

        $this->assertClassImplements($messageClass, GameMessage::class, 'message');

        if (isset($this->messages[$key])) {
            throw new InvalidArgumentException(sprintf('Module [%s] already registered message key [%s].', $this->alias, $key));
        }

        $this->messages[$key] = $messageClass;

        return $this;
    }

    /**
     * Declare a module-scoped server setting. The returned definition is
     * registered immediately and can be further configured fluently.
     */
    public function setting(string $key): SettingDefinition
    {
        if ($key === '' || str_starts_with($key, '.') || str_ends_with($key, '.')) {
            throw new InvalidArgumentException(sprintf('Module [%s] registered an invalid setting key [%s].', $this->alias, $key));
        }

        $namespacedKey = $this->alias . '.' . $key;

        if (isset($this->settings[$namespacedKey])) {
            throw new InvalidArgumentException(sprintf('Module [%s] already registered setting key [%s].', $this->alias, $key));
        }

        $definition = new SettingDefinition($namespacedKey);

        $this->settings[$namespacedKey] = $definition;

        return $definition;
    }

    /**
     * Listen for a typed OGameX domain event.
     *
     * @param class-string       $event
     * @param class-string       $listener
     */
    public function listen(string $event, string $listener): static
    {
        if (!class_exists($event)) {
            throw new InvalidArgumentException(sprintf('Module [%s] registered an unknown event class [%s].', $this->alias, $event));
        }

        if (!class_exists($listener)) {
            throw new InvalidArgumentException(sprintf('Module [%s] registered an unknown listener class [%s].', $this->alias, $listener));
        }

        $this->listeners[$event][] = $listener;
        Event::listen($event, $listener);

        return $this;
    }

    /**
     * Register a planet-level extension.
     *
     * @param class-string $extensionClass
     */
    public function extendPlanet(string $extensionClass): static
    {
        $this->assertClassImplements($extensionClass, ExtendsPlanetService::class, 'planet extension');

        $this->planetExtensions[] = $extensionClass;

        return $this;
    }

    /**
     * Register a player-level extension.
     *
     * @param class-string $extensionClass
     */
    public function extendPlayer(string $extensionClass): static
    {
        $this->assertClassImplements($extensionClass, ExtendsPlayerService::class, 'player extension');

        $this->playerExtensions[] = $extensionClass;

        return $this;
    }

    /**
     * Register a planet-bound queue processor.
     *
     * @param class-string $processorClass
     */
    public function queueProcessor(string $processorClass): static
    {
        $this->assertClassImplements($processorClass, ProvidesQueueProcessor::class, 'queue processor');

        $this->queueProcessors[] = $processorClass;

        return $this;
    }

    /**
     * Register a highscore category.
     *
     * @param class-string $categoryClass
     */
    public function highscoreCategory(string $categoryClass): static
    {
        $this->assertClassImplements($categoryClass, ProvidesHighscoreCategory::class, 'highscore category');

        $this->highscoreCategories[] = $categoryClass;

        return $this;
    }

    /**
     * @return array<GameObject>
     */
    public function getObjects(): array
    {
        return $this->objects;
    }

    /**
     * @return array<string, array<callable>>
     */
    public function getObjectExtensions(): array
    {
        return $this->objectExtensions;
    }

    /**
     * @return array<int, class-string<GameMission>>
     */
    public function getMissions(): array
    {
        return $this->missions;
    }

    /**
     * @return array<string, class-string<GameMessage>>
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @return array<string, SettingDefinition>
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @return array<string, array<class-string>>
     */
    public function getListeners(): array
    {
        return $this->listeners;
    }

    /**
     * @return array<class-string>
     */
    public function getPlanetExtensions(): array
    {
        return $this->planetExtensions;
    }

    /**
     * @return array<class-string>
     */
    public function getPlayerExtensions(): array
    {
        return $this->playerExtensions;
    }

    /**
     * @return array<class-string>
     */
    public function getQueueProcessors(): array
    {
        return $this->queueProcessors;
    }

    /**
     * @return array<class-string>
     */
    public function getHighscoreCategories(): array
    {
        return $this->highscoreCategories;
    }

    /**
     * Fail during module boot with an actionable error instead of deferring a
     * malformed registration to a game request or queue update.
     *
     * @param class-string $expected
     */
    private function assertClassImplements(string $class, string $expected, string $capability): void
    {
        if (!class_exists($class) || !is_a($class, $expected, true)) {
            throw new InvalidArgumentException(sprintf(
                'Module [%s] registered [%s] as a %s; it must extend or implement [%s].',
                $this->alias,
                $class,
                $capability,
                $expected,
            ));
        }
    }
}
