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
    public function __construct(ObjectService $objects, PlanetService $planet)
    {
        parent::__construct($objects, $planet);
    }

    /**
     * @inheritdoc
     */
    protected function getBonusPercentage($object_id): int
    {
        $shielding_technology_level = $this->planet->getPlayer()->getResearchLevel(110);
        // Every level technology gives 10% bonus.
        return $shielding_technology_level * 10;
    }
}
