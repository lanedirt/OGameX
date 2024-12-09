<?php

namespace OGame\ViewModels;

use OGame\GameObjects\Models\Abstracts\GameObject;

class BuildingViewModel extends QueueViewModel
{
    public bool $research_in_progress;
    public bool $valid_planet_type;
}
