<?php

namespace OGame\Services;

use Exception;
use OGame\Models\FleetMission;
use OGame\Models\FleetUnion;

/**
 * Class FleetUnionService.
 *
 * Handles fleet union creation and management for ACS Attack missions.
 *
 * @package OGame\Services
 */
class FleetUnionService
{
    /**
     * Maximum delay percentage (30% of remaining time).
     */
    private const MAX_DELAY_PERCENTAGE = 0.30;

    /**
     * FleetUnionService constructor.
     */
    public function __construct(
        private readonly BuddyService $buddyService,
        private readonly AllianceService $allianceService,
    ) {
    }

    /**
     * Create a new fleet union from an existing attack mission.
     *
     * @param FleetMission $mission The initial attack mission to convert to a union
     * @param string|null $name Optional name for the union
     * @return FleetUnion
     * @throws Exception
     */
    public function createUnion(FleetMission $mission, string|null $name = null): FleetUnion
    {
        // Validate mission type (must be attack - type 1)
        if ($mission->mission_type !== 1) {
            throw new Exception(__('t_acs.error_invalid_mission_type'));
        }

        // Validate mission is still in flight
        if ($mission->processed || $mission->canceled) {
            throw new Exception(__('t_acs.error_mission_not_active'));
        }

        // Validate mission is not already in a union
        if ($mission->isInUnion()) {
            throw new Exception(__('t_acs.error_already_in_union'));
        }

        // Create the union
        $union = FleetUnion::create([
            'user_id' => $mission->user_id,
            'name' => $name,
            'galaxy_to' => $mission->galaxy_to,
            'system_to' => $mission->system_to,
            'position_to' => $mission->position_to,
            'planet_type_to' => $mission->type_to,
            'time_arrival' => $mission->time_arrival,
            'max_fleets' => 16,
            'max_players' => 5,
        ]);

        // Link the mission to the union and convert to ACS Attack
        $mission->union_id = $union->id;
        $mission->union_slot = 1; // Initiator always gets slot 1
        $mission->mission_type = 2; // Convert to ACS Attack
        $mission->save();

        return $union;
    }

    /**
     * Join an existing union with a fleet mission.
     *
     * @param FleetUnion $union The union to join
     * @param FleetMission $mission The fleet mission joining the union
     * @return void
     * @throws Exception
     */
    public function joinUnion(FleetUnion $union, FleetMission $mission): void
    {
        // Validate union hasn't reached max fleets
        if ($union->hasReachedMaxFleets()) {
            throw new Exception(__('t_acs.error_max_fleets_reached'));
        }

        // Validate union hasn't reached max players (if this is a new player)
        $isNewPlayer = !$union->activeFleetMissions()
            ->where('user_id', $mission->user_id)
            ->exists();

        if ($isNewPlayer && $union->hasReachedMaxPlayers()) {
            throw new Exception(__('t_acs.error_max_players_reached'));
        }

        // Validate player is ally or buddy of union creator
        $creatorUserId = $union->user_id;
        $joiningUserId = $mission->user_id;

        if (!$this->isAllyOrBuddy($creatorUserId, $joiningUserId)) {
            throw new Exception(__('t_acs.error_not_buddy_or_ally'));
        }

        // Validate fleet can arrive within delay limit
        $maxArrival = $union->time_arrival + $this->getMaxDelayTime($union);
        if ($mission->time_arrival > $maxArrival) {
            throw new Exception(__('t_acs.error_exceeds_delay_limit'));
        }

        // Get next available slot
        $nextSlot = $union->activeFleetMissions()->max('union_slot') + 1;

        // Link mission to union
        $mission->union_id = $union->id;
        $mission->union_slot = $nextSlot;
        $mission->mission_type = 2; // ACS Attack

        // Adjust arrival time to match union (if fleet arrives earlier)
        if ($mission->time_arrival < $union->time_arrival) {
            $mission->time_arrival = $union->time_arrival;
        } else {
            // Fleet arrives later - update union arrival time (within delay limit)
            $union->time_arrival = $mission->time_arrival;
            $union->save();
        }

        $mission->save();
    }

    /**
     * Get the maximum delay time allowed for joining fleets.
     * This is 30% of the remaining flight time.
     *
     * @param FleetUnion $union
     * @return int Delay time in seconds
     */
    public function getMaxDelayTime(FleetUnion $union): int
    {
        $remainingTime = $union->getRemainingTime();
        return (int) floor($remainingTime * self::MAX_DELAY_PERCENTAGE);
    }

    /**
     * Handle a fleet being recalled from a union.
     *
     * @param FleetMission $mission The mission being recalled
     * @return void
     */
    public function handleFleetRecall(FleetMission $mission): void
    {
        if (!$mission->isInUnion()) {
            return;
        }

        /** @var FleetUnion $union */
        $union = $mission->union;

        // Remove from union
        $mission->union_id = null;
        $mission->union_slot = null;
        $mission->save();

        // Check if union is now empty
        if ($union->activeFleetMissions()->count() === 0) {
            // Delete the empty union
            $union->delete();
        }
    }

    /**
     * Check if two players are allies or buddies.
     *
     * @param int $userId1
     * @param int $userId2
     * @return bool
     */
    private function isAllyOrBuddy(int $userId1, int $userId2): bool
    {
        // Same player is always allowed
        if ($userId1 === $userId2) {
            return true;
        }

        // Check if buddies
        if ($this->buddyService->areBuddies($userId1, $userId2)) {
            return true;
        }

        // Check if in same alliance
        if ($this->allianceService->arePlayersInSameAlliance($userId1, $userId2)) {
            return true;
        }

        return false;
    }
}
