<?php

namespace OGame\ViewModels;

class BuildingViewModel extends QueueViewModel
{
    public bool $research_in_progress;
    public bool $ship_or_defense_in_progress = false;
    public bool $valid_planet_type;
}
