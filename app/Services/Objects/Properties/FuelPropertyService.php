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
    public function __construct(ObjectService $objects, PlanetService $planet)
    {
        parent::__construct($objects, $planet);
    }

    /**
     * @inheritdoc
     */
    protected function getBonusPercentage($object_id): int
    {
        // TODO: implement fuel bonus/extra calculation per object id.
        return 0;
    }
}
