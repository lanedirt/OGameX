<?php

namespace OGame\GameObjects\Services\Properties;

use OGame\GameObjects\Services\Properties\Abstracts\ObjectPropertyService;
use OGame\Services\PlayerService;

/**
 * Class FuelCapacityPropertyService.
 *
 * Handles fuel capacity calculations for ships. Fuel capacity determines
 * how much deuterium a ship can hold for consumption during flight,
 * separate from cargo capacity used for transporting resources.
 *
 * @package OGame\Services
 */
class FuelCapacityPropertyService extends ObjectPropertyService
{
    protected string $propertyName = 'fuel_capacity';

    /**
     * @inheritDoc
     */
    protected function getBonusPercentage(PlayerService $player): int
    {
        // Fuel capacity uses the same bonus as cargo capacity (hyperspace technology)
        $hyperspace_technology_level = $player->getResearchLevel('hyperspace_technology');
        return 5 * $hyperspace_technology_level;
    }
}
