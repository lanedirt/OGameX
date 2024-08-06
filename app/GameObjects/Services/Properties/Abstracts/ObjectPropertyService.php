<?php

namespace OGame\GameObjects\Services\Properties\Abstracts;

use OGame\GameObjects\Models\Abstracts\GameObject;
use OGame\GameObjects\Models\Fields\GameObjectPropertyDetails;
use OGame\Services\PlayerService;

/**
 * Class ObjectPropertyService.
 *
 * @package OGame\Services
 */
abstract class ObjectPropertyService
{
    /**
     * This is a placeholder for the property name set by the child class.
     *
     * @var string
     */
    protected string $propertyName = '';
    protected int $base_value;
    protected GameObject $parent_object;

    public function __construct(GameObject $parentObject, int $baseValue)
    {
        $this->parent_object = $parentObject;
        $this->base_value = $baseValue;
    }

    /**
     * Get the bonus percentage for a property.
     *
     * @return int
     *  Bonus percentage as integer (e.g. 10 for 10% bonus, 110 for 110% bonus, etc.)
     */
    abstract protected function getBonusPercentage(PlayerService $player): int;

    /**
     * Calculate the total value of a property.
     *
     * @param PlayerService $player
     * @return GameObjectPropertyDetails
     */
    public function calculateProperty(PlayerService $player): GameObjectPropertyDetails
    {
        $bonusPercentage = $this->getBonusPercentage($player);
        $bonusValue = (($this->base_value / 100) * $bonusPercentage);

        $totalValue = $this->base_value + $bonusValue;

        // Prepare the breakdown for future-proofing (assuming more components might be added)
        // TODO: add model for breakdown
        // TODO: Add more components to the breakdown if necessary like class bonuses, premium member
        // bonuses, item bonuses etc.
        $breakdown = [
            'rawValue' => $this->base_value,
            'bonuses' => [
                [
                    'type' => 'Research bonus',
                    'value' => $bonusValue,
                    'percentage' => $bonusPercentage,
                ],
            ],
            'totalValue' => $totalValue,
        ];

        return new GameObjectPropertyDetails($this->base_value, $bonusValue, $totalValue, $breakdown);
    }
}
