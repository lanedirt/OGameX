<?php

namespace OGame\Facades;

use Illuminate\Support\Facades\Facade;
use OGame\Extensions\ExtensionRegistry;

/**
 * Facade for the OGameX module extension registry.
 *
 * Modules register everything they contribute to OGameX through this facade:
 *
 *   Extensions::module('lifeforms', function (ModuleExtension $module): void {
 *       $module->objects(...);
 *   });
 *
 * @method static \OGame\Extensions\ModuleExtension module(string $alias, callable $registration)
 * @method static array<int, \OGame\GameObjects\Models\Abstracts\GameObject> objects()
 * @method static string|null objectOwner(\OGame\GameObjects\Models\Abstracts\GameObject $object)
 * @method static array<string, array<callable>> objectExtensions()
 * @method static array<int, class-string<\OGame\GameMissions\Abstracts\GameMission>> missions()
 * @method static array<string, class-string<\OGame\GameMessages\Abstracts\GameMessage>> messages()
 * @method static array<string, \OGame\Extensions\SettingDefinition> settings()
 * @method static array<string, array<class-string>> listeners()
 * @method static array<class-string> planetExtensions()
 * @method static array<class-string> playerExtensions()
 * @method static array<class-string> queueProcessors()
 * @method static array<class-string> highscoreCategories()
 *
 * @see ExtensionRegistry
 */
class Extensions extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ExtensionRegistry::class;
    }
}
