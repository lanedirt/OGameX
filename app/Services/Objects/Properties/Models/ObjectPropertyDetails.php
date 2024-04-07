<?php

namespace OGame\Services\Objects\Properties\Models;

class ObjectPropertyDetails
{
    public int $rawValue;
    public int $bonusValue;
    public int $totalValue;

    public function __construct($rawValue, $bonusValue, $totalValue, $details = [])
    {
        $this->rawValue = $rawValue;
        $this->bonusValue = $bonusValue;
        $this->totalValue = $totalValue;
    }
}