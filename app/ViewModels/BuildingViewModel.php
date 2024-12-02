<?php

namespace OGame\ViewModels;

use OGame\GameObjects\Models\Abstracts\GameObject;

class BuildingViewModel
{
    public GameObject $object;
    public int $current_level;
    public bool $requirements_met;
    public bool $valid_planet_type;
    public int $count;
    public bool $enough_resources;
    public bool $currently_building;
}
