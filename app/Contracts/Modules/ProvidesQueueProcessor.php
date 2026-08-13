<?php

namespace OGame\Contracts\Modules;

use OGame\Services\PlanetService;

/**
 * Contract for modules that bring their own queue service (buildings, tech, etc.)
 *
 * The core calls processQueue() on every page load at the same point it processes
 * its own building, research, and unit queues. Register implementations through
 * the Extensions facade:
 *
 *   Extensions::module('lifeforms', function (ModuleExtension $module): void {
 *       $module->queueProcessor(PopulationQueueProcessor::class);
 *   });
 *
 * The implementation is responsible for retrieving and processing any finished
 * queue items for the given planet.
 */
interface ProvidesQueueProcessor
{
    /**
     * Process any finished module queue items for the given planet.
     * Called on every page load during the normal queue processing cycle.
     */
    public function processQueue(PlanetService $planet): void;
}
