<?php

namespace OGame\ViewModels;

use OGame\GameObjects\Models\UnitCollection;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;

class FleetEventRowViewModel
{
    public int $id;
    public int $mission_type;
    public string $mission_label;
    public int $mission_time_arrival;
    public bool $is_return_trip;
    public string $origin_planet_name;
    public Coordinate $origin_planet_coords;
    public string $destination_planet_name;
    public Coordinate $destination_planet_coords;
    public int $fleet_unit_count;
    public UnitCollection $fleet_units;
    public Resources $resources;
}