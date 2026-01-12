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

        // With the new architecture, time_arrival includes hold time
        // Calculate physical arrival time to check if fleet has arrived
        $settingsService = app(SettingsService::class);
        $actualHoldingTime = (int)($outboundMission->time_holding / $settingsService->fleetSpeedHolding());
        $physicalArrivalTime = $outboundMission->time_arrival - $actualHoldingTime;

        // Fleet must have physically arrived and still be holding (hold hasn't expired)
        if ($physicalArrivalTime > $currentTime || $currentTime >= $outboundMission->time_arrival) {
            return false;
        }

        // Check if the ORIGINAL hold time was at least 1 hour (in GAME time)
        // This prevents extending fleets sent with 0 hours (which become 30 min minimum)
        // We only check time_holding which stores the original/total duration
        if ($outboundMission->time_holding !== null && $outboundMission->time_holding < 3600) {
            // Original hold time was less than 1 hour (game time), don't allow extension
            return false;
        }

        // Calculate when the return mission will depart (for checking if still holding)
        // With the new architecture, time_arrival already includes hold time
        if ($outboundMission->time_holding !== null) {
            $expectedReturnDeparture = $outboundMission->time_arrival;
        } elseif ($returnMission) {
            // If time_holding is not set, calculate from return mission
            $expectedReturnDeparture = $returnMission->time_departure;
        } else {
            // No hold time info available
            return false;
        }

        // Return mission must not have departed yet (check actual or expected time)
        $actualReturnDeparture = $returnMission ? $returnMission->time_departure : $expectedReturnDeparture;
        if ($actualReturnDeparture <= $currentTime) {
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

        $settingsService = app(SettingsService::class);

        // Calculate extension in seconds (game time)
        $extensionSecondsGameTime = $extensionHours * 3600;

        // Convert to real-world time for time_arrival update
        $extensionSecondsRealTime = (int)($extensionSecondsGameTime / $settingsService->fleetSpeedHolding());

        // Update the outbound mission's time_holding (in game time)
        $outboundMission->time_holding += $extensionSecondsGameTime;

        // Update time_arrival since it now includes the hold time
        $outboundMission->time_arrival += $extensionSecondsRealTime;
        $outboundMission->save();

        // NOTE: No need to update return mission - it doesn't exist yet!
        // When the extended hold time expires, the return mission will be created
        // with the correct timing automatically.

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
        $settingsService = app(SettingsService::class);
        $fleetSpeedHolding = $settingsService->fleetSpeedHolding();

        // Get all ACS Defend missions that have physically arrived at the target
        // and are still holding (mission not yet processed)
        // Only get outbound missions (parent_id IS NULL), not return missions
        $allMissions = FleetMission::where('mission_type', 5)
            ->where('planet_id_to', $planetId)
            ->where('processed', 0) // Not yet processed
            ->whereNull('parent_id') // Only outbound missions, not returns
            ->where('canceled', 0)
            ->get();

        $holdingFleets = [];
        foreach ($allMissions as $outboundMission) {
            $returnMission = $this->getReturnMission($outboundMission);

            // The hold time stored in the mission is "game time" (e.g., 3600 seconds = 1 hour).
            // We need to apply the fleet_speed_holding multiplier to convert to real-world time.
            $actualHoldingTime = (int)($outboundMission->time_holding / $fleetSpeedHolding);

            // Calculate physical arrival time (time_arrival includes hold time)
            $physicalArrivalTime = $outboundMission->time_arrival - $actualHoldingTime;

            // Check if fleet is currently holding (physically arrived but hold hasn't expired)
            if ($physicalArrivalTime <= $currentTime && $currentTime < $outboundMission->time_arrival) {
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
