<?php

namespace OGame\Contracts\Modules;

use OGame\GameObjects\Models\Abstracts\GameObject;

/**
 * Contract for modules that contribute additional game objects
 * (buildings, ships, defense, research, etc.) to the ObjectService registry.
 *
 * Register the objects through the Extensions facade:
 *
 *   Extensions::module('lifeforms', function (ModuleExtension $module): void {
 *       $module->objects($this->getGameObjects());
 *   });
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
