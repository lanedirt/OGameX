<?php

namespace OGame\Contracts\Modules;

use OGame\Services\PlanetService;

/**
 * Contract for modules that inject planet-level calculations,
 * such as additional resource production (population, food, artifacts).
 *
 * Register implementations through the Extensions facade:
 *
 *   Extensions::module('lifeforms', function (ModuleExtension $module): void {
 *       $module->extendPlanet(PopulationProduction::class);
 *   });
 */
interface ExtendsPlanetService
{
    /**
     * Called during planet resource production calculation.
     * Modify the planet state or accumulate production values as needed.
     */
    public function extendResourceProduction(PlanetService $planet): void;
}
