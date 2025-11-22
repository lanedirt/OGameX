<?php

namespace OGame\Services;

use OGame\Factories\GameMissionFactory;
use OGame\Factories\PlayerServiceFactory;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\FleetMission;
use OGame\Models\Planet;
use OGame\Models\Planet\Coordinate;

/**
 * Class PhalanxService
 *
 * Handles Sensor Phalanx functionality including range calculation,
 * target validation, and fleet scanning.
 *
 * @package OGame\Services
 */
class PhalanxService
{
    /**
     * Deuterium cost per scan
     */
    private const SCAN_COST = 5000;

    private PlayerServiceFactory $playerServiceFactory;

    /**
     * PhalanxService constructor.
     */
    public function __construct(PlayerServiceFactory $playerServiceFactory)
    {
        $this->playerServiceFactory = $playerServiceFactory;
    }

    /**
     * Calculate the range of a sensor phalanx based on its level.
     *
     * Formula: Range = (level^2) - 1
     * Level 1: 0 systems (can only scan same system)
     * Level 2: 3 systems
     * Level 3: 8 systems
     * Level 4: 15 systems
     *
     * @param int $phalanx_level
     * @return int Number of systems that can be scanned in each direction
     */
    public function calculatePhalanxRange(int $phalanx_level): int
    {
        return ($phalanx_level * $phalanx_level) - 1;
    }

    /**
     * Check if a target coordinate can be scanned from the given moon.
     *
     * @param int $moon_galaxy Moon's galaxy
     * @param int $moon_system Moon's system
     * @param int $phalanx_level Sensor phalanx level
     * @param Coordinate $target_coordinate The target planet coordinates
     * @return bool True if target is within range
     */
    public function canScanTarget(int $moon_galaxy, int $moon_system, int $phalanx_level, Coordinate $target_coordinate): bool
    {
        if ($phalanx_level === 0) {
            return false;
        }

        // Calculate range
        $max_range = $this->calculatePhalanxRange($phalanx_level);

        // Must be in same galaxy
        if ($moon_galaxy !== $target_coordinate->galaxy) {
            return false;
        }

        // Calculate system distance
        $system_distance = abs($moon_system - $target_coordinate->system);

        return $system_distance <= $max_range;
    }

    /**
     * Check if moon has enough deuterium for a scan.
     *
     * @param int|float $moon_deuterium Current deuterium on moon
     * @return bool True if enough deuterium available
     */
    public function hasEnoughDeuterium(int|float $moon_deuterium): bool
    {
        return $moon_deuterium >= self::SCAN_COST;
    }

    /**
     * Get the deuterium cost for a phalanx scan.
     *
     * @return int Deuterium cost
     */
    public function getScanCost(): int
    {
        return self::SCAN_COST;
    }

    /**
     * Scan a planet for fleet movements.
     *
     * Returns all fleet missions that are:
     * - Departing from the target planet
     * - Arriving at the target planet
     * - Not yet processed
     * - Not targeting debris fields (type_to != 2)
     * - Not targeting moons (type_to != 3)
     *
     * @param int $target_planet_id The planet ID to scan
     * @param int $scanner_player_id The player ID performing the scan
     * @return array<int, array<string, mixed>> Array of fleet mission details
     */
    public function scanPlanetFleets(int $target_planet_id, int $scanner_player_id): array
    {
        // Get all active fleet missions involving this planet
        $fleet_missions = FleetMission::where(function ($query) use ($target_planet_id) {
            $query->where('planet_id_from', $target_planet_id)
                  ->orWhere('planet_id_to', $target_planet_id);
        })
        ->where('processed', 0)
        ->orderBy('time_arrival', 'asc')
        ->get();

        $scan_results = [];

        foreach ($fleet_missions as $mission) {
            // Determine if this is incoming to the scanned planet
            $is_incoming = $mission->planet_id_to === $target_planet_id;

            // Phalanx ONLY shows incoming fleets (fleets arriving at the scanned planet)
            // It does NOT show outgoing fleets (fleets leaving from the scanned planet)
            if (!$is_incoming) {
                // This fleet is leaving the scanned planet, not arriving - skip it
                // But we may need to show its return trip if it will come back to this planet
                // Skip return missions - they don't have their own return trips
                if (empty($mission->parent_id) && $this->missionHasReturnTrip($mission->mission_type)) {
                    // Check if return trip will come back to the scanned planet
                    if ($mission->planet_id_from === $target_planet_id) {
                        // Yes! This fleet left from the scanned planet and will return to it
                        // Add the predicted return trip
                        $return_time_arrival = $mission->time_arrival +
                                             ($mission->time_arrival - $mission->time_departure) +
                                             ($mission->time_holding ?? 0);

                        $ships = $this->getFleetShips($mission);
                        $fleet_speed = $this->getFleetSpeed($mission);
                        $mission_type_name = $this->getMissionTypeName($mission->mission_type);
                        $fleet_direction = $this->getFleetDirectionLabel($mission->user_id, $scanner_player_id, $mission->mission_type);

                        $scan_results[] = [
                            'mission_id' => $mission->id + 999999,
                            'mission_type' => $mission->mission_type,
                            'mission_type_name' => $mission_type_name,
                            'is_incoming' => true, // Return trip is incoming
                            'is_return_trip' => true,
                            'time_departure' => $mission->time_arrival,
                            'time_arrival' => $return_time_arrival,
                            'fleet_direction' => $fleet_direction,
                            'fleet_icon' => '014a5d88b102d4b47ab5146d4807c6.gif',
                            'display_time' => $return_time_arrival,
                            'origin' => [
                                'galaxy' => $mission->galaxy_to,
                                'system' => $mission->system_to,
                                'position' => $mission->position_to,
                            ],
                            'destination' => [
                                'galaxy' => $mission->galaxy_from,
                                'system' => $mission->system_from,
                                'position' => $mission->position_from,
                            ],
                            'ships' => $ships,
                            'ship_count' => array_sum($ships),
                            'fleet_speed' => $fleet_speed,
                        ];
                    }
                }
                continue; // Skip the outgoing mission itself
            }

            // Skip missions to debris fields or moons (OGame rules)
            if ($mission->type_to == 2 || $mission->type_to == 3) {
                continue;
            }

            // Get mission type name
            $mission_type_name = $this->getMissionTypeName($mission->mission_type);

            // Check if this is a return trip
            $is_return_trip = !empty($mission->parent_id);

            // Get ship counts
            $ships = $this->getFleetShips($mission);

            // Calculate fleet speed (with research bonuses)
            $fleet_speed = $this->getFleetSpeed($mission);

            // For return trips, recalculate the arrival time from the parent mission
            // This ensures we show the correct return time even if database has wrong value
            $display_time_arrival = $mission->time_arrival;
            if ($is_return_trip && $mission->parent_id) {
                // Load parent mission to get accurate times
                $parent_mission = FleetMission::find($mission->parent_id);
                if ($parent_mission) {
                    // Calculate return arrival time same way as FleetEventsController does
                    // Return arrival = parent arrival + travel duration + holding time
                    $display_time_arrival = $parent_mission->time_arrival +
                                          ($parent_mission->time_arrival - $parent_mission->time_departure) +
                                          ($parent_mission->time_holding ?? 0);
                }
            }

            // Calculate display properties for Blade template
            $fleet_direction = $this->getFleetDirectionLabel($mission->user_id, $scanner_player_id, $mission->mission_type);
            $fleet_icon = $is_return_trip ? '014a5d88b102d4b47ab5146d4807c6.gif' : 'f9cb590cdf265f499b0e2e5d91fc75.gif'; // Left for return, right for incoming

            // Add the incoming mission to results
            $scan_results[] = [
                'mission_id' => $mission->id,
                'mission_type' => $mission->mission_type,
                'mission_type_name' => $mission_type_name,
                'is_incoming' => $is_incoming,
                'is_return_trip' => $is_return_trip,
                'time_departure' => $mission->time_departure,
                'time_arrival' => $display_time_arrival,
                'fleet_direction' => $fleet_direction,
                'fleet_icon' => $fleet_icon,
                'display_time' => $display_time_arrival,
                'origin' => [
                    'galaxy' => $mission->galaxy_from,
                    'system' => $mission->system_from,
                    'position' => $mission->position_from,
                ],
                'destination' => [
                    'galaxy' => $mission->galaxy_to,
                    'system' => $mission->system_to,
                    'position' => $mission->position_to,
                ],
                'ships' => $ships,
                'ship_count' => array_sum($ships),
                'fleet_speed' => $fleet_speed,
            ];
        }

        return $scan_results;
    }

    /**
     * Get mission type name from mission type ID.
     *
     * @param int $mission_type
     * @return string Mission type name
     */
    private function getMissionTypeName(int $mission_type): string
    {
        try {
            // Use GameMissionFactory for centralized mission definitions
            return GameMissionFactory::getMissionById($mission_type, [])->getName();
        } catch (\RuntimeException $e) {
            return 'Unknown';
        }
    }

    /**
     * Get fleet direction label based on ownership and mission type.
     *
     * @param int $fleet_owner_id The player ID who owns the fleet
     * @param int $scanner_player_id The player ID performing the scan
     * @param int $mission_type The mission type ID
     * @return string Fleet direction label
     */
    private function getFleetDirectionLabel(int $fleet_owner_id, int $scanner_player_id, int $mission_type): string
    {
        // If scanner owns the fleet
        if ($fleet_owner_id === $scanner_player_id) {
            return 'Your fleet';
        }

        // If attack mission (type 1 = Attack)
        if ($mission_type === 1) {
            return 'Enemy fleet';
        }

        // All other missions
        return 'Friendly fleet';
    }

    /**
     * Check if a mission type has a return trip.
     *
     * @param int $mission_type
     * @return bool True if mission has return trip
     */
    private function missionHasReturnTrip(int $mission_type): bool
    {
        try {
            // Use GameMissionFactory for centralized mission definitions
            return GameMissionFactory::getMissionById($mission_type, [])->hasReturnMission();
        } catch (\RuntimeException $e) {
            // Fallback for unimplemented missions (2, 5, 9) - all have return trips
            return true;
        }
    }

    /**
     * Extract ship counts from a fleet mission.
     *
     * @param FleetMission $mission
     * @return array<string, int> Ship counts indexed by ship type
     */
    private function getFleetShips(FleetMission $mission): array
    {
        $ship_types = [];

        // Dynamically get all ship objects from the game system
        foreach (ObjectService::getShipObjects() as $ship) {
            $amount = $mission->{$ship->machine_name} ?? 0;
            if ($amount > 0) {
                $ship_types[$ship->machine_name] = $amount;
            }
        }

        return $ship_types;
    }

    /**
     * Get the actual fleet speed including player research bonuses.
     * Uses the existing game system to calculate speed based on player's drive research.
     *
     * @param FleetMission $mission The fleet mission
     * @return int The speed of the slowest ship in the fleet (with research bonuses)
     */
    private function getFleetSpeed(FleetMission $mission): int
    {
        // Get the player who owns this fleet
        $player = $this->playerServiceFactory->make($mission->user_id);

        // Build unit collection from mission ships
        $units = $this->buildUnitCollectionFromMission($mission);

        // If no units, return 0
        if (empty($units->units)) {
            return 0;
        }

        // Get slowest ship speed (includes research bonuses)
        return $units->getSlowestUnitSpeed($player);
    }

    /**
     * Build a UnitCollection from a FleetMission's ship data.
     *
     * @param FleetMission $mission
     * @return UnitCollection
     */
    private function buildUnitCollectionFromMission(FleetMission $mission): UnitCollection
    {
        $units = new UnitCollection();

        foreach (ObjectService::getShipObjects() as $ship) {
            $amount = $mission->{$ship->machine_name};
            if ($amount > 0) {
                $units->addUnit($ship, $amount);
            }
        }

        return $units;
    }
}
