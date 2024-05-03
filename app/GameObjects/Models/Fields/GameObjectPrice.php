<?php

namespace OGame\GameObjects\Models\Fields;

use OGame\Models\Resources;

class GameObjectPrice
{
    public Resources $resources;
    public float $factor = 1;
    public bool $roundNearest100 = false;

    public function __construct(int $metal, int $crystal, int $deuterium, int $energy, float $factor = 1, bool $roundNearest100 = false)
    {
        $resources = new Resources($metal, $crystal, $deuterium, $energy);
        $this->resources = $resources;
        $this->factor = $factor;
        $this->roundNearest100 = $roundNearest100;
    }
}
