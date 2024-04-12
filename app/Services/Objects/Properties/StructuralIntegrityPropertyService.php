<?php

namespace OGame\Services\Objects\Properties;

use Exception;
use OGame\Services\Objects\ObjectService;
use OGame\Services\Objects\Properties\Abstracts\ObjectPropertyService;
use OGame\Services\PlanetService;

/**
 * Class StructuralIntegrityPropertyService.
 *
 * @package OGame\Services
 */
class StructuralIntegrityPropertyService extends ObjectPropertyService
{
    protected string $propertyName = 'structural_integrity';

    /**
     * @inheritdoc
     * @throws Exception
     */
    protected function getBonusPercentage(PlanetService $planet): int
    {
        $armor_technology_level = $planet->getPlayer()->getResearchLevel('armor_technology');
        // Every level of armor technology gives 10% bonus.
        return $armor_technology_level * 10;
    }
}
