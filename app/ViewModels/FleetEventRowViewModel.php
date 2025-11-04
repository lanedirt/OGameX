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
    public bool $is_return_trip;

    /**
     * @var bool Whether the fleet can be recalled or not. E.g. enemy missions show up in the event box but can't be recalled.
     */
    public bool $is_recallable;

    /**
     * @var string Whether the mission is friendly, hostile, or neutral (friendly/hostile/neutral)
     */
    public string $mission_status = 'neutral';

    public string $origin_planet_name;
    public Coordinate $origin_planet_coords;
    public PlanetType $origin_planet_type;
    public string $destination_planet_name;
    public Coordinate $destination_planet_coords;
    public PlanetType $destination_planet_type;
    public int $fleet_unit_count;
    public UnitCollection $fleet_units;
    public Resources $resources;

    /**
     * @var int|null ACS group ID if this fleet is part of an ACS attack
     */
    public ?int $acs_group_id = null;

    /**
     * @var string|null ACS group name
     */
    public ?string $acs_group_name = null;

    /**
     * @var int Number of fleets in the ACS group
     */
    public int $acs_fleet_count = 0;

    /**
     * @var array Fleet participants in the ACS group
     */
    public array $acs_participants = [];

    /**
     * @var bool Whether the current player is the creator of the ACS group
     */
    public bool $is_acs_group_creator = false;
}
