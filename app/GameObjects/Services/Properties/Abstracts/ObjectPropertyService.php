<?php

namespace OGame\GameObjects\Services\Properties\Abstracts;

use OGame\GameObjects\Models\Abstracts\GameObject;
use OGame\GameObjects\Models\Fields\GameObjectPropertyDetails;
use OGame\Services\PlayerService;

/**
 * Class ObjectPropertyService.
 */
abstract class ObjectPropertyService
{
    /**
     * This is a placeholder for the property name set by the child class.
     */
    protected string $propertyName = '';

    public function __construct(protected GameObject $parent_object, protected int $base_value) {}

    /**
     * Get the bonus percentage for a property.
     *
     * @return int
     *             Bonus percentage as integer (e.g. 10 for 10% bonus, 110 for 110% bonus, etc.)
     */
    abstract protected function getBonusPercentage(PlayerService $player): int;

    /**
     * Calculate the total value of a property.
     */
    public function calculateProperty(PlayerService $player): GameObjectPropertyDetails
    {
        // Research bonus applied to base_value
        $researchBonus = $this->getBonusPercentage($player);
        $researchBonusValue = intdiv($this->base_value * $researchBonus, 100);

        $totalValue = $this->base_value + $researchBonusValue;

        $bonuses = [];
        if ($researchBonus > 0) {
            $bonuses[] = [
                'type' => 'Research bonus',
                'value' => $researchBonusValue,
                'percentage' => $researchBonus,
            ];
        }

        $breakdown = [
            'rawValue' => $this->base_value,
            'bonuses' => $bonuses,
            'totalValue' => $totalValue,
        ];

        return new GameObjectPropertyDetails($this->base_value, $researchBonusValue, $totalValue, $breakdown);
    }
}
