<?php

namespace OGame\GameObjects\Models\Fields;

class GameObjectPropertyDetails
{
    /*
     * $breakdown = [
            'rawValue' => $rawValue,
            'bonuses' => [
                [
                    'type' => 'Research bonus',
                    'value' => $bonusValue,
                    'percentage' => $bonusPercentage,
                ],
            ],
            'totalValue' => $totalValue,
        ];
     */

    /**
     * @param int $rawValue
     * @param int $bonusValue
     * @param int $totalValue
     * @param array<string,array<int, array<string, float|int|string>>|float|int> $breakdown
     */
    public function __construct(public int $rawValue, public int $bonusValue, public int $totalValue, public array $breakdown = [])
    {
    }
}
