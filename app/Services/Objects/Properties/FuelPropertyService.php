<?php

namespace OGame\Services\Objects\Properties;

use OGame\Services\Objects\ObjectService;
use OGame\Services\Objects\Properties\Abstracts\ObjectPropertyService;
use OGame\Services\PlanetService;

/**
 * Class FuelPropertyService.
 *
 * @package OGame\Services
 */
class FuelPropertyService extends ObjectPropertyService
{
    protected string $propertyName = 'fuel';

    /**
     * @inheritdoc
     */
    protected function getBonusPercentage(PlanetService $planet): int
    {
        // TODO: implement fuel bonus/extra calculation per object id.
        return 0;
    }
}
