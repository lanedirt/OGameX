<?php

namespace OGame\Services\Objects\Properties\Models;

class ObjectPropertyDetails
{

    public int $rawValue;
    public int $bonusValue;
    public int $totalValue;

    /**
     * @var array<string, string|int|array<string,string|int>>
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
     * @param array<string, string|int|array<string,string|int>> $breakdown
     */
    public function __construct(int $rawValue, int $bonusValue, int $totalValue, array $breakdown = [])
    {
        $this->get() = $rawValue;
        $this->bonusValue = $bonusValue;
        $this->totalValue = $totalValue;
        $this->breakdown = $breakdown;
    }
}