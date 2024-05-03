<?php

namespace OGame\GameObjects\Models\Fields;

class GameObjectPropertyDetails
{
    public int $rawValue;
    public int $bonusValue;
    public int $totalValue;

    /**
     * @var array<string,array<int, array<string, float|int|string>>|float|int>
     */
    public array $breakdown;
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
    public function __construct(int $rawValue, int $bonusValue, int $totalValue, array $breakdown = [])
    {
        $this->rawValue = $rawValue;
        $this->bonusValue = $bonusValue;
        $this->totalValue = $totalValue;
        $this->breakdown = $breakdown;
    }
}
