<?php

namespace OGame\GameObjects\Services\Properties;

use OGame\GameObjects\Models\Fields\GameObjectPropertyDetails;
use OGame\GameObjects\Services\Properties\Abstracts\ObjectPropertyService;
use OGame\Services\CharacterClassService;
use OGame\Services\PlayerService;

/**
 * Class CapacityPropertyService.
 *
 * @package OGame\Services
 */
class CapacityPropertyService extends ObjectPropertyService
{
    protected string $propertyName = 'capacity';

    /**
     * Calculate the total value of the capacity property including character class bonuses.
     *
     * @param PlayerService $player
     * @return GameObjectPropertyDetails
     */
    public function calculateProperty(PlayerService $player): GameObjectPropertyDetails
    {
        $bonusPercentage = $this->getBonusPercentage($player);
        $bonusValue = (($this->base_value / 100) * $bonusPercentage);

        $totalValue = $this->base_value + $bonusValue;

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

        // Apply character class cargo bonuses (based on base value only, not including research bonuses)
        $classBonus = $this->getCharacterClassCargoBonus($player);
        if ($classBonus > 0) {
            $classBonusValue = (($this->base_value / 100) * $classBonus);
            $totalValue += $classBonusValue;

            $breakdown['bonuses'][] = [
                'type' => 'Character class bonus',
                'value' => $classBonusValue,
                'percentage' => $classBonus,
            ];
            $breakdown['totalValue'] = $totalValue;
        }

        return new GameObjectPropertyDetails($this->base_value, $bonusValue, $totalValue, $breakdown);
    }

    /**
     * @inheritDoc
     */
    protected function getBonusPercentage(PlayerService $player): int
    {
        $hyperspace_technology_level = $player->getResearchLevel('hyperspace_technology');
        return 5 * $hyperspace_technology_level;
    }

    /**
     * Get character class cargo bonus percentage.
     *
     * @param PlayerService $player
     * @return int Percentage bonus (0-100)
     */
    private function getCharacterClassCargoBonus(PlayerService $player): int
    {
        $characterClassService = app(CharacterClassService::class);
        $user = $player->getUser();
        $object = $this->parent_object;

        // Collector: +25% cargo for transporters (Small Cargo: 202, Large Cargo: 203)
        if ($object->id === 202 || $object->id === 203) {
            $multiplier = $characterClassService->getTransporterCargoBonus($user);
            if ($multiplier > 1.0) {
                return (int)(($multiplier - 1.0) * 100);
            }
        }

        // General: +20% cargo for Recycler (209) and Pathfinder (219)
        if ($object->id === 209 || $object->id === 219) {
            $multiplier = $characterClassService->getRecyclerPathfinderCargoBonus($user);
            if ($multiplier > 1.0) {
                return (int)(($multiplier - 1.0) * 100);
            }
        }

        return 0;
    }
}
