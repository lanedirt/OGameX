<?php

namespace OGame\Services\Objects\Properties\Models;

class ObjectPropertyDetails
{

    public int $rawValue;
    public int $bonusValue;
    public int $totalValue;
    public array $breakdown;

    /**
     * @param int $rawValue
     * @param int $bonusValue
     * @param int $totalValue
     * @param array $breakdown
     */
    public function __construct(int $rawValue, int $bonusValue, int $totalValue, array $breakdown = [])
    {
        $this->rawValue = $rawValue;
        $this->bonusValue = $bonusValue;
        $this->totalValue = $totalValue;
        $this->breakdown = $breakdown;
    }
}