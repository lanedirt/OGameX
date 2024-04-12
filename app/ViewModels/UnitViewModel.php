<?php

namespace OGame\ViewModels;

use OGame\Services\Objects\Models\GameObject;

class UnitViewModel
{
    public int $count;
    public GameObject $object;
    public int $amount;
    public bool $requirements_met;
    public bool $enough_resources;
    public bool $currently_building;
}