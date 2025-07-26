<?php

namespace OGame\GameMissions;

class MoonDestructionMission
{
    // TODO: a moon destruction mission first causes a combat to happen where the attacking fleet (including death star) needs to win the battle first.
    // If the battle is lost, the fleet is destroyed like normally and moon destruction mission is cancelled/failed.
    // If the battle is won, the moon destruction outcome is calculated which can either win, fail (and return), or fail and destroy whole attacking fleet.

    // TODO: this is currently a placeholder for the moon destruction mission outcomes (actual mission logic needs to be implemented later)

    /**
     * Returns a list of possible outcomes for a moon destruction mission.
     *
     * @return array<array{success: bool, subject: string, message: string}>
     */
    protected static function getOutcomes(): array
    {
        return [
        // Success
        [
            'success' => false,
            'subject' => 'Moon attack',
            'message' => 'Your fleet from [planet_name] [planet_coords] has arrived at the moon [moon_name] [moon_coords]. The weapons of the deathstar fire an alternating graviton shock at the moon, building up to a massive quake and finally tearing the satellite apart. All buildings on the moon were destroyed. A complete success! The fleet is returning to its home planet.

Moon destruction chance: 7%, DS destruction chance: 46%',
        ],
        // Failure (fleet survives):
        [
            'success' => false,
            'subject' => 'Moon attack',
            'message' => 'Your fleet from [planet_name] [planet_coords] has arrived at the moon [moon_name] [moon_coords]. The structure of the moon was not sufficiently weakened, and the fleet is returning to its home planet.

Moon destruction chance: 7%, DS destruction chance: 46%',
        ],
        // Failures (fleet destroyed):
        [
            'success' => false,
            'subject' => 'Moon attack',
            'message' => 'Your fleet from [planet_name] [planet_coords] has arrived at the moon [moon_name] [moon_coords]. Your deathstar aims its alternating graviton shock cannon at the satellite. Light quakes shake the surface of the moon. But something is wrong. The graviton cannon causes the deathstar to vibrate. There is feedback. The deathstar ruptures into millions of pieces. The resulting shock waves destroy your entire fleet.

Moon destruction chance: 7%, DS destruction chance: 46%',
        ],
        // Failure (no moon at target):
        [
            'success' => false,
            'subject' => 'Fleet Mission Failed',
            'message' => 'This action cannot be carried out on the chosen target. The fleet had to turn back without completing its mission.',
        ],
        ];
    }
}
