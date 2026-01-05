<?php

namespace OGame\Services;

use OGame\GameConstants\UniverseConstants;
use OGame\Models\Planet;
use OGame\Models\Planet\Coordinate;

/**
 * Class CoordinateDistanceCalculator
 *
 * Helper service for calculating distances between coordinates,
 * taking into account empty and inactive systems.
 *
 * @package OGame\Services
 */
class CoordinateDistanceCalculator
{
    public function __construct(private SettingsService $settingsService)
    {
    }

    /**
     * Get the number of empty systems between two coordinates.
     * Only applies when coordinates are in the same galaxy.
     *
     * @param Coordinate $from
     * @param Coordinate $to
     * @return int
     */
    public function getNumEmptySystems(Coordinate $from, Coordinate $to): int
    {
        // Only calculate if the setting is enabled
        if (!$this->settingsService->ignoreEmptySystemsOn()) {
            return 0;
        }

        // Only applies to same galaxy
        if ($from->galaxy !== $to->galaxy) {
            return 0;
        }

        $diffSystems = abs($from->system - $to->system);

        // Check if donut galaxy wrapping provides a shorter path
        $altDiff = UniverseConstants::MAX_SYSTEM_COUNT - $diffSystems;
        if ($altDiff < $diffSystems) {
            // Path wraps around, split into two segments
            $split1 = new Coordinate($from->galaxy, UniverseConstants::MIN_SYSTEM, UniverseConstants::MAX_PLANET_POSITION);
            $split2 = new Coordinate($to->galaxy, UniverseConstants::MAX_SYSTEM_COUNT, UniverseConstants::MAX_PLANET_POSITION);
            return $this->getNumEmptySystemsAux($split1, $to)
                + $this->getNumEmptySystemsAux($split2, $from);
        }

        return $this->getNumEmptySystemsAux($from, $to);
    }

    /**
     * Helper method to count empty systems in a linear range.
     *
     * @param Coordinate $from
     * @param Coordinate $to
     * @return int
     */
    private function getNumEmptySystemsAux(Coordinate $from, Coordinate $to): int
    {
        $start = min($from->system, $to->system);
        $end = max($from->system, $to->system);

        // Count distinct systems that have at least one planet
        $occupiedSystems = Planet::distinct('system')
            ->where('galaxy', '=', $from->galaxy)
            ->where('system', '>=', $start)
            ->where('system', '<=', $end)
            ->count();

        // Total systems in range minus occupied systems
        $totalSystems = $end - $start + 1;
        return $totalSystems - $occupiedSystems;
    }

    /**
     * Get the number of inactive systems between two coordinates.
     * Only applies when coordinates are in the same galaxy.
     *
     * @param Coordinate $from
     * @param Coordinate $to
     * @return int
     */
    public function getNumInactiveSystems(Coordinate $from, Coordinate $to): int
    {
        // Only calculate if the setting is enabled
        if (!$this->settingsService->ignoreInactiveSystemsOn()) {
            return 0;
        }

        // Only applies to same galaxy
        if ($from->galaxy !== $to->galaxy) {
            return 0;
        }

        $diffSystems = abs($from->system - $to->system);

        // Check if donut galaxy wrapping provides a shorter path
        $altDiff = UniverseConstants::MAX_SYSTEM_COUNT - $diffSystems;
        if ($altDiff < $diffSystems) {
            // Path wraps around, split into two segments
            $split1 = new Coordinate($from->galaxy, UniverseConstants::MIN_SYSTEM, UniverseConstants::MAX_PLANET_POSITION);
            $split2 = new Coordinate($to->galaxy, UniverseConstants::MAX_SYSTEM_COUNT, UniverseConstants::MAX_PLANET_POSITION);
            return $this->getNumInactiveSystemsAux($split1, $to)
                + $this->getNumInactiveSystemsAux($split2, $from);
        }

        return $this->getNumInactiveSystemsAux($from, $to);
    }

    /**
     * Helper method to count inactive systems in a linear range.
     * A system is considered inactive if all planets in it belong to users
     * who haven't been active in the last 7 days.
     *
     * @param Coordinate $from
     * @param Coordinate $to
     * @return int
     */
    private function getNumInactiveSystemsAux(Coordinate $from, Coordinate $to): int
    {
        $start = min($from->system, $to->system);
        $end = max($from->system, $to->system);

        // Count systems where all planets belong to inactive users
        // A user is considered inactive if time is older than 7 days (matching PlayerService::isInactive())
        $inactiveSystems = Planet::selectRaw('planets.system')
            ->selectRaw('SUM(IF(users.time >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 7 DAY)), 1, 0)) AS active_count')
            ->join('users', 'users.id', '=', 'planets.user_id')
            ->where('planets.galaxy', '=', $from->galaxy)
            ->where('planets.system', '>=', $start)
            ->where('planets.system', '<=', $end)
            ->groupBy('planets.system')
            ->having('active_count', '=', 0)
            ->count();

        return $inactiveSystems;
    }
}
