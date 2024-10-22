<?php

namespace OGame\ViewModels;

use OGame\GameObjects\Models\Abstracts\GameObject;

class UnitViewModel
{
    public int $count;
    public GameObject $object;
    public int $amount;
    public bool $requirements_met;
    public bool $enough_resources;
    public int $max_build_amount;
    public bool $currently_building;
    public int $currently_building_amount;
}
