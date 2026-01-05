<?php

namespace OGame\ViewModels;

use OGame\Services\PlanetService;

class GalaxyRowViewModel
{
    public function __construct(public int $position, public PlanetService|null $planet)
    {
    }
}
