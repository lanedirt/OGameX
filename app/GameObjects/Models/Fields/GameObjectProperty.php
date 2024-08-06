<?php

namespace OGame\GameObjects\Models\Fields;

use OGame\GameObjects\Services\Properties\Abstracts\ObjectPropertyService;
use OGame\Services\PlayerService;

class GameObjectProperty
{
    /**
     * Name of the property used for display.
     *
     * @var string
     */
    public string $name;
    public int $rawValue;
    public ObjectPropertyService $calculationService;

    // construct
    public function __construct(string $name, int $rawValue, ObjectPropertyService $calculationService)
    {
        $this->name = $name;
        $this->rawValue = $rawValue;
        $this->calculationService = $calculationService;
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
