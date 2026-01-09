<?php

namespace OGame\Services;

use Illuminate\Support\Facades\Date;
use OGame\Models\FleetMission;

class AllianceDepotService
{
    /**
     * Deuterium consumption per ship type per hour (in deuterium).
     *
     * @var array<string, int|float>
     */
    private const SHIP_CONSUMPTION_PER_HOUR = [
        'small_cargo' => 1,
        'large_cargo' => 5,
        'light_fighter' => 2,
        'heavy_fighter' => 7,
        'cruiser' => 30,
        'battle_ship' => 60,
        'bomber' => 100,
        'destroyer' => 100,
        'battlecruiser' => 25,
        'deathstar' => 0.1,
        'espionage_probe' => 0.1,
        'recycler' => 30,
        'colony_ship' => 100,
    ];

    /**
     * Calculate deuterium cost to extend a fleet's hold time by a given duration.
     *
     * @param FleetMission $mission
     * @param int $extensionHours Number of hours to extend
     * @return int Total deuterium cost
     */
    public function calculateSupplyRocketCost(FleetMission $mission, int $extensionHours): int
    {
        $totalCost = 0.0;

        foreach (self::SHIP_CONSUMPTION_PER_HOUR as $shipType => $costPerHour) {
            $shipAmount = (int)$mission->$shipType;
            if ($shipAmount > 0) {
                $totalCost += $shipAmount * $costPerHour * $extensionHours;
            }
        }

        return (int)ceil($totalCost);
    }

    /**
     * Check if a fleet can have its hold time extended.
     * Only fleets holding for 1 hour or more can be extended.
     *
     * @param FleetMission $outboundMission The outbound ACS Defend mission
     * @param FleetMission|null $returnMission The return mission (null if not created yet)
     * @return bool
     */
    public function canExtendHoldTime(FleetMission $outboundMission, FleetMission|null $returnMission): bool
    {
        $currentTime = Date::now()->timestamp;

        // Mission must be an ACS Defend mission (type 5)
        if ($outboundMission->mission_type !== 5) {
            return false;
        }

        // If return mission exists, it must also be ACS Defend
        if ($returnMission && $returnMission->mission_type !== 5) {
            return false;
        }

        // Fleet must have arrived
        if ($outboundMission->time_arrival > $currentTime) {
            return false;
        }

        // Calculate expected return departure time
        $expectedReturnDeparture = $outboundMission->time_arrival + $outboundMission->time_holding;

        // Return mission must not have departed yet (check actual or expected time)
        $actualReturnDeparture = $returnMission ? $returnMission->time_departure : $expectedReturnDeparture;
        if ($actualReturnDeparture <= $currentTime) {
            return false;
        }

        // Calculate original hold duration
        $originalHoldDuration = $actualReturnDeparture - $outboundMission->time_arrival;

        // Must be holding for at least 1 hour (3600 seconds)
        if ($originalHoldDuration < 3600) {
            return false;
        }

        return true;
    }

    /**
     * Extend a fleet's hold time by sending a supply rocket.
     *
     * @param FleetMission $outboundMission The outbound ACS Defend mission
     * @param FleetMission|null $returnMission The return mission (null if not created yet)
     * @param int $extensionHours Number of hours to extend
     * @return bool Success status
     */
    public function extendHoldTime(FleetMission $outboundMission, FleetMission|null $returnMission, int $extensionHours): bool
    {
        if (!$this->canExtendHoldTime($outboundMission, $returnMission)) {
            return false;
        }

        if ($extensionHours < 1 || $extensionHours > 32) {
            return false;
        }

        // Calculate extension in seconds
        $extensionSeconds = $extensionHours * 3600;

        if ($returnMission) {
            // Return mission exists - update its times
            $returnMission->time_departure += $extensionSeconds;
            $returnMission->time_arrival += $extensionSeconds;
            $returnMission->save();
        } else {
            // Return mission doesn't exist yet - extend the hold time on outbound mission
            $outboundMission->time_holding += $extensionSeconds;
            $outboundMission->save();
        }

        return true;
    }

    /**
     * Get the return mission for a given outbound ACS Defend mission.
     *
     * @param FleetMission $outboundMission
     * @return FleetMission|null
     */
    public function getReturnMission(FleetMission $outboundMission): FleetMission|null
    {
        return FleetMission::where('planet_id_from', $outboundMission->planet_id_to)
            ->where('planet_id_to', $outboundMission->planet_id_from)
            ->where('mission_type', 5)
            ->where('time_departure', '>=', $outboundMission->time_arrival)
            ->where('canceled', 0)
            ->orderBy('time_departure', 'asc')
            ->first();
    }

    /**
     * Get all holding fleets at a planet with their return missions.
     *
     * @param int $planetId
     * @return array<int, array<string, mixed>>
     */
    public function getHoldingFleetsWithReturnMissions(int $planetId): array
    {
        $currentTime = (int)Date::now()->timestamp;

        // Get all ACS Defend missions that have arrived and are holding
        // ACS Defend missions are processed when time_arrival + time_holding is reached
        // During the hold time, processed = 0
        $outboundMissions = FleetMission::where('mission_type', 5)
            ->where('planet_id_to', $planetId)
            ->where('time_arrival', '<=', $currentTime)
            ->where('canceled', 0)
            ->get();

        $holdingFleets = [];

        foreach ($outboundMissions as $outboundMission) {
            $returnMission = $this->getReturnMission($outboundMission);

            // Calculate expected return departure time
            $expectedReturnDeparture = $outboundMission->time_arrival + $outboundMission->time_holding;

            // Only include if:
            // 1. Return mission exists and hasn't departed yet (still holding), OR
            // 2. No return mission yet but fleet is still holding (expected return time is in future)
            if (($returnMission && $returnMission->time_departure > $currentTime) ||
                (!$returnMission && $expectedReturnDeparture > $currentTime)) {
                $holdingFleets[] = [
                    'outbound_mission' => $outboundMission,
                    'return_mission' => $returnMission,
                    'can_extend' => $this->canExtendHoldTime($outboundMission, $returnMission),
                ];
            }
        }

        return $holdingFleets;
    }

    /**
     * Get the maximum supply capacity per hour for a given Alliance Depot level.
     *
     * @param int $depotLevel
     * @return int Capacity in deuterium per hour
     */
    public function getSupplyCapacity(int $depotLevel): int
    {
        return $depotLevel * 10000;
    }
}
