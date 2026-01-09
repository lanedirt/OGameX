<?php

namespace OGame\GameObjects\Models\Fields;

use OGame\GameObjects\Services\Properties\Abstracts\ObjectPropertyService;
use OGame\Services\PlayerService;

class GameObjectProperty
{
    /**
     * GameObjectProperty constructor.
     *
     * @param string $name Name of the property used for display.
     * @param int $rawValue Raw value of the property.
     * @param ObjectPropertyService $calculationService Calculation service for the property.
     */
    public function __construct(public string $name, public int $rawValue, public ObjectPropertyService $calculationService)
    {
    }

    /**
     * Calculate the actual value of the property based on user and planet levels.
     *
     * @param PlayerService $player
     * @return GameObjectPropertyDetails
     */
    public function calculate(PlayerService $player): GameObjectPropertyDetails
    {
        return $this->calculationService->calculateProperty($player);
    }
}
