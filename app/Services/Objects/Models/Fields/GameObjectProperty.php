<?php

namespace OGame\Services\Objects\Models\Fields;

use OGame\Services\Objects\Properties\Abstracts\ObjectPropertyService;
use OGame\Services\Objects\Properties\Models\ObjectPropertyDetails;
use OGame\Services\PlanetService;

class GameObjectProperty
{
    public int $rawValue;
    public ObjectPropertyService $calculationService;

    // construct
    public function __construct(int $rawValue, ObjectPropertyService $calculationService)
    {
        $this->rawValue = $rawValue;
        $this->calculationService = $calculationService;
    }

    /**
     * Calculate the actual value of the property based on user and planet levels.
     *
     * @param PlanetService $planet
     * @return ObjectPropertyDetails
     */
    public function calculate(PlanetService $planet) : ObjectPropertyDetails
    {
        return $this->calculationService->calculateProperty($planet);
    }
}