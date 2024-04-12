<?php

namespace OGame\Services\Objects\Properties;

use OGame\Services\Objects\ObjectService;
use OGame\Services\Objects\Properties\Abstracts\ObjectPropertyService;
use OGame\Services\PlanetService;

/**
 * Class AttackPropertyService.
 *
 * @package OGame\Services
 */
class AttackPropertyService extends ObjectPropertyService
{
    protected string $propertyName = 'attack';

    /**
     * @inheritdoc
     */
    protected function getBonusPercentage(PlanetService $planet): int
    {
        $weapons_technology_level = $planet->getPlayer()->getResearchLevel('weapon_technology');
        // Every level technology gives 10% bonus.
        return $weapons_technology_level * 10;
    }
}
