<?php

namespace OGame\ViewModels;

class BuildingViewModel extends QueueViewModel
{
    public bool $research_in_progress;
    public bool $ship_or_defense_in_progress = false;
    public bool $valid_planet_type;
    public bool $character_class_met = true;
    public ?int $target_level = null;
    public bool $is_downgrade = false;
    public bool $uses_last_field = false;
    public bool $fields_exceeded = false;
}
