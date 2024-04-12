<?php

namespace OGame\Services\Objects\Properties;

use OGame\Services\Objects\ObjectService;
use OGame\Services\Objects\Properties\Abstracts\ObjectPropertyService;
use OGame\Services\PlanetService;

/**
 * Class ShieldPropertyService.
 *
 * @package OGame\Services
 */
class ShieldPropertyService extends ObjectPropertyService
{
    protected string $propertyName = 'shield';

    /**
     * @inheritdoc
     */
    protected function getBonusPercentage(PlanetService $planet): int
    {
        $shielding_technology_level = $planet->getPlayer()->getResearchLevel('shielding_technology');
        // Every level technology gives 10% bonus.
        return $shielding_technology_level * 10;
    }
}
