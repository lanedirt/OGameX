<?php

namespace OGame\Contracts\Modules;

use OGame\Services\PlanetService;

/**
 * Contract for modules that inject planet-level calculations,
 * such as additional resource production (population, food, artifacts).
 *
 * Register implementations via app()->tag() in bootModule():
 *   app()->tag(MyPlanetExtension::class, 'module.planet_extensions');
 */
interface ExtendsPlanetService
{
    /**
     * Called during planet resource production calculation.
     * Modify the planet state or accumulate production values as needed.
     */
    public function extendResourceProduction(PlanetService $planet): void;
}
