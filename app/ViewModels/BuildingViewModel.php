<?php

namespace OGame\ViewModels;

use OGame\GameObjects\Models\GameObject;

class BuildingViewModel
{
    public GameObject $object;
    public int $current_level;
    public bool $requirements_met;
    public int $count;
    public bool $enough_resources;
    public bool $currently_building;
}