<?php

namespace OGame\ViewModels;

use OGame\Services\PlanetService;

class GalaxyRowViewModel
{
    public ?PlanetService $planet;
    public int $position;

    public function __construct(int $position, ?PlanetService $planet)
    {
        $this->planet = $planet;
        $this->position = $position;
    }
}
