<?php

namespace OGame\Services;

use InvalidArgumentException;
use OGame\Models\Planet;
use OGame\Models\User;

/**
 * Entry point for small, namespaced module state.
 *
 * Use a module's own migrations for rich relational data such as AI actors,
 * diplomacy, or population history; use this service for scalar settings,
 * cursors, flags, and JSON snapshots.
 */
class ModuleStateService
{
    public function module(string $alias): ModuleStateNamespace
    {
        return new ModuleStateNamespace($alias);
    }
}

class ModuleStateNamespace
{
    public function __construct(private readonly string $alias)
    {
        if (!preg_match('/^[a-z][a-z0-9_-]*$/', $alias)) {
            throw new InvalidArgumentException('Module state requires a valid lowercase module alias.');
        }
    }

    public function server(): ModuleStateScope
    {
        return new ModuleStateScope($this->alias, 'server', 0);
    }

    public function forPlanet(Planet|int $planet): ModuleStateScope
    {
        return new ModuleStateScope($this->alias, 'planet', $planet instanceof Planet ? $planet->id : $planet);
    }

    public function forPlayer(User|int $player): ModuleStateScope
    {
        return new ModuleStateScope($this->alias, 'player', $player instanceof User ? $player->id : $player);
    }
}
