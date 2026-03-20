<?php

namespace OGame\Contracts\Modules;

use OGame\GameObjects\Models\Abstracts\GameObject;

/**
 * Contract for modules that contribute additional game objects
 * (buildings, ships, defense, research, etc.) to the ObjectService registry.
 *
 * Call ObjectService::registerModuleObjects($this->getGameObjects()) in bootModule().
 *
 * @see \OGame\Services\ObjectService::registerModuleObjects()
 */
interface ProvidesGameObjects
{
    /**
     * Return the array of GameObject instances provided by this module.
     *
     * @return array<GameObject>
     */
    public function getGameObjects(): array;
}
