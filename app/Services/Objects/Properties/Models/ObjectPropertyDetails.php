<?php

namespace OGame\Services\Objects\Properties\Models;

class ObjectPropertyDetails
{
    public int $rawValue;
    public int $bonusValue;
    public int $totalValue;
    public array $breakdown;

    public function __construct($rawValue, $bonusValue, $totalValue, $breakdown = [])
    {
        $this->rawValue = $rawValue;
        $this->bonusValue = $bonusValue;
        $this->totalValue = $totalValue;
        $this->breakdown = $breakdown;
    }
}