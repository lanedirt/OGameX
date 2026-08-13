<?php

namespace OGame\Extensions;

use InvalidArgumentException;
use OGame\GameMessages\Abstracts\GameMessage;
use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameObjects\Models\Abstracts\GameObject;
use OGame\Services\ModuleSlotService;
use RuntimeException;

/**
 * Central registry for everything modules contribute to OGameX.
 *
 * Bound as a singleton in the application container and exposed to modules via
 * the Extensions facade. Core services consume the flattened registrations at
 * their documented lifecycle points, which keeps modules declarative and the
 * core in charge of orchestration.
 */
class ExtensionRegistry
{
    /** @var array<string, ModuleExtension> */
    private array $modules = [];

    /**
     * Scope a set of registrations to a module alias and run them immediately.
     */
    public function module(string $alias, callable $registration): ModuleExtension
    {
        if (!preg_match('/^[a-z][a-z0-9_-]*$/', $alias)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid module alias [%s]. Use the lowercase module.json alias (letters, numbers, hyphens, and underscores).',
                $alias,
            ));
        }

        $module = $this->modules[$alias] ??= new ModuleExtension($alias);

        $registration($module);

        return $module;
    }

    /**
     * @return array<string, ModuleExtension>
     */
    public function modules(): array
    {
        return $this->modules;
    }

    /**
     * All module-contributed game objects in deterministic registration order.
     *
     * @return array<GameObject>
     */
    public function objects(): array
    {
        $objects = [];
        $ownersById = [];
        $ownersByName = [];

        foreach ($this->modules as $alias => $module) {
            foreach ($module->getObjects() as $object) {
                $this->assertObjectUnique($object, $alias, $ownersById, $ownersByName);
                $objects[] = $object;
            }
        }

        return $objects;
    }

    /**
     * Return the alias that owns a module game object, or null for a core
     * object. Object state uses this namespace instead of adding a column to a
     * core table for every object a module introduces.
     */
    public function objectOwner(GameObject $object): ?string
    {
        foreach ($this->modules as $alias => $module) {
            foreach ($module->getObjects() as $registeredObject) {
                if ($registeredObject->id === $object->id
                    && $registeredObject->machine_name === $object->machine_name) {
                    return $alias;
                }
            }
        }

        return null;
    }

    /**
     * Object-extension callables keyed by machine name, in registration order.
     *
     * @return array<string, array<callable>>
     */
    public function objectExtensions(): array
    {
        $extensions = [];

        foreach ($this->modules as $module) {
            foreach ($module->getObjectExtensions() as $machineName => $callbacks) {
                foreach ($callbacks as $callback) {
                    $extensions[$machineName][] = $callback;
                }
            }
        }

        return $extensions;
    }

    /**
     * Module mission types keyed by mission ID.
     *
     * @return array<int, class-string<GameMission>>
     */
    public function missions(): array
    {
        $missions = [];
        $owners = [];

        foreach ($this->modules as $alias => $module) {
            foreach ($module->getMissions() as $id => $missionClass) {
                if (isset($owners[$id])) {
                    throw new RuntimeException(sprintf(
                        'Duplicate mission ID [%d] registered by modules "%s" and "%s".',
                        $id,
                        $owners[$id],
                        $alias,
                    ));
                }

                $owners[$id] = $alias;
                $missions[$id] = $missionClass;
            }
        }

        return $missions;
    }

    /**
     * Module game-message classes keyed by message key.
     *
     * @return array<string, class-string<GameMessage>>
     */
    public function messages(): array
    {
        $messages = [];
        $owners = [];

        foreach ($this->modules as $alias => $module) {
            foreach ($module->getMessages() as $key => $messageClass) {
                if (isset($owners[$key])) {
                    throw new RuntimeException(sprintf(
                        'Duplicate message key [%s] registered by modules "%s" and "%s".',
                        $key,
                        $owners[$key],
                        $alias,
                    ));
                }

                $owners[$key] = $alias;
                $messages[$key] = $messageClass;
            }
        }

        return $messages;
    }

    /**
     * Module setting definitions keyed by their namespaced key.
     *
     * @return array<string, SettingDefinition>
     */
    public function settings(): array
    {
        $settings = [];

        foreach ($this->modules as $alias => $module) {
            foreach ($module->getSettings() as $key => $definition) {
                if (isset($settings[$key])) {
                    throw new RuntimeException(sprintf(
                        'Duplicate setting key [%s] registered by module "%s".',
                        $key,
                        $alias,
                    ));
                }

                $settings[$key] = $definition;
            }
        }

        return $settings;
    }

    /**
     * Listener classes keyed by event class.
     *
     * @return array<string, array<class-string>>
     */
    public function listeners(): array
    {
        $listeners = [];

        foreach ($this->modules as $module) {
            foreach ($module->getListeners() as $event => $moduleListeners) {
                foreach ($moduleListeners as $listener) {
                    $listeners[$event][] = $listener;
                }
            }
        }

        return $listeners;
    }

    /**
     * @return array<class-string>
     */
    public function planetExtensions(): array
    {
        return $this->flatten('getPlanetExtensions');
    }

    /**
     * @return array<class-string>
     */
    public function playerExtensions(): array
    {
        return $this->flatten('getPlayerExtensions');
    }

    /**
     * @return array<class-string>
     */
    public function queueProcessors(): array
    {
        return $this->flatten('getQueueProcessors');
    }

    /**
     * @return array<class-string>
     */
    public function highscoreCategories(): array
    {
        return $this->flatten('getHighscoreCategories');
    }

    /**
     * Clear every registration. Intended for use in tests only.
     */
    public function flush(): void
    {
        $this->modules = [];
        ModuleSlotService::resetSlots();
    }

    /**
     * @return array<int, mixed>
     */
    private function flatten(string $method): array
    {
        $items = [];

        foreach ($this->modules as $module) {
            array_push($items, ...$module->{$method}());
        }

        return $items;
    }

    /**
     * @param array<int, string> $ownersById
     * @param array<string, string> $ownersByName
     */
    private function assertObjectUnique(GameObject $object, string $alias, array &$ownersById, array &$ownersByName): void
    {
        if (isset($ownersById[$object->id])) {
            throw new RuntimeException(sprintf(
                'Duplicate game object ID [%d] registered by modules "%s" and "%s".',
                $object->id,
                $ownersById[$object->id],
                $alias,
            ));
        }

        if (isset($ownersByName[$object->machine_name])) {
            throw new RuntimeException(sprintf(
                'Duplicate game object machine name [%s] registered by modules "%s" and "%s".',
                $object->machine_name,
                $ownersByName[$object->machine_name],
                $alias,
            ));
        }

        $ownersById[$object->id] = $alias;
        $ownersByName[$object->machine_name] = $alias;
    }
}
