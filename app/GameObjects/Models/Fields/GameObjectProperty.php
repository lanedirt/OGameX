<?php

namespace OGame\GameObjects\Models\Fields;

use OGame\GameObjects\Services\Properties\Abstracts\ObjectPropertyService;
use OGame\Services\PlayerService;

class GameObjectProperty
{
    // construct
    public function __construct(
        /**
         * Name of the property used for display.
         */
        public string $name,
        public int $rawValue,
        public ObjectPropertyService $calculationService
    )
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
