<?php

namespace OGame\ViewModels;

use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;

class FleetEventRowViewModel
{
    public int $id;
    public int $mission_type;
    public string $mission_label;
    public int $mission_time_arrival;
    public int $time_departure;
    public bool $is_return_trip;
    public int $active_recall_time;

    /**
     * @var bool Whether the fleet can be recalled or not. E.g. enemy missions show up in the event box but can't be recalled.
     */
    public bool $is_recallable;

    public string $friendly_status;

    public bool $has_return_trip = false;

    public ?int $return_time_arrival = null;

    public bool $is_at_destination = false;

    public int $timer_time;

    public int $remaining_time = 0;

    public int $duration = 1;

    public int $return_remaining_time = 0;

    public string $origin_planet_name;
    public Coordinate $origin_planet_coords;
    public PlanetType $origin_planet_type;
    public string $origin_planet_image_type = '1';
    public string $origin_planet_biome_type = 'normaltemp';
    public string $destination_planet_name;
    public Coordinate $destination_planet_coords;
    public PlanetType $destination_planet_type;
    public string $destination_planet_image_type = '1';
    public string $destination_planet_biome_type = 'normaltemp';
    public int $fleet_unit_count;
    public UnitCollection $fleet_units;
    public Resources $resources;
}
