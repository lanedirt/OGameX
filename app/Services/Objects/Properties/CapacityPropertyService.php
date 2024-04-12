<?php

namespace OGame\Services\Objects\Properties;

use OGame\Services\Objects\ObjectService;
use OGame\Services\Objects\Properties\Abstracts\ObjectPropertyService;
use OGame\Services\PlanetService;

/**
 * Class CapacityPropertyService.
 *
 * @package OGame\Services
 */
class CapacityPropertyService extends ObjectPropertyService
{
    protected string $propertyName = 'capacity';

    /**
     * @inheritdoc
     */
    protected function getBonusPercentage(PlanetService $planet): int
    {
        // TODO: implement capacity bonus calculation per object id.
        return 0;
    }
}
