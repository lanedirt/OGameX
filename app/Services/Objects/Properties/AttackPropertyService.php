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
    public function __construct(ObjectService $objects, PlanetService $planet)
    {
        parent::__construct($objects, $planet);
    }

    /**
     * @inheritdoc
     */
    protected function getBonusPercentage($object_id): int
    {
        $weapons_technology_level = $this->planet->getPlayer()->getResearchLevel(109);
        // Every level technology gives 10% bonus.
        return $weapons_technology_level * 10;
    }
}
