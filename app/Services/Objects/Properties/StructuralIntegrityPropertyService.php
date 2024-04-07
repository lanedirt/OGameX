<?php

namespace OGame\Services\Objects\Properties;

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
     */
    public function __construct(ObjectService $objects, PlanetService $planet)
    {
        parent::__construct($objects, $planet);
    }

    /**
     * @inheritdoc
     */
    protected function getBonusPercentage($object_id): int
    {
        $armor_technology_level = $this->planet->getPlayer()->getResearchLevel(111);
        // Every level of armor technology gives 10% bonus.
        return $armor_technology_level * 10;
    }
}
