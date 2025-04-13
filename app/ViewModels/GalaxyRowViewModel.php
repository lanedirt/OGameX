<?php

namespace OGame\ViewModels;

use OGame\Services\PlanetService;

class GalaxyRowViewModel
{
    public PlanetService|null $planet;
    public int $position;

    public function __construct(int $position, PlanetService|null $planet)
    {
        $this->planet = $planet;
        $this->position = $position;
    }
}
