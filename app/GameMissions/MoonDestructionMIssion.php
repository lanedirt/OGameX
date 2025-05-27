<?php

namespace OGame\GameMissions;

use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Resources;

class MoonDestructionMission
{
    // TODO: a moon destruction mission first causes a combat to happen where the attacking fleet (including death star) needs to win the battle first.
    // If the battle is lost, the fleet is destroyed like normally and moon destruction mission is cancelled/failed.
    // If the battle is won, the moon destruction outcome is calculated which can either win, fail (and return), or fail and destroy whole attacking fleet.

    // TODO: this is currently a placeholder for the moon destruction mission outcomes (actual mission logic needs to be implemented later)
    protected static array $outcomes = [
        // Failure (fleet survives):
        [
            'success' => false,
            'message' => 'Your fleet from [planet_name] [planet_coords] has arrived at the moon [moon_name] [moon_coords]. The structure of the moon was not sufficiently weakened, and the fleet is returning to its home planet.

Moon destruction chance: 7%, DS destruction chance: 46%',
        ],
        // Failures (fleet destroyed):
        [
            'success' => false,
            'message' => 'Your fleet from [planet_name] [planet_coords] has arrived at the moon [moon_name] [moon_coords]. Your deathstar aims its alternating graviton shock cannon at the satellite. Light quakes shake the surface of the moon. But something is wrong. The graviton cannon causes the deathstar to vibrate. There is feedback. The deathstar ruptures into millions of pieces. The resulting shock waves destroy your entire fleet.

Moon destruction chance: 7%, DS destruction chance: 46%',
        ],
    ];
}
