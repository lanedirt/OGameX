<?php

namespace OGame\Services;

use Illuminate\Support\Collection;
use OGame\Models\AcsGroup;
use OGame\Models\AcsFleetMember;
use OGame\Models\AcsInvitation;
use OGame\Models\FleetMission;

class ACSService
{
    /**
     * Create a new ACS group for an attack
     */
    public static function createGroup(
        int $creatorId,
        string $name,
        int $galaxyTo,
        int $systemTo,
        int $positionTo,
        int $typeTo,
        int $arrivalTime
    ): AcsGroup {
        return AcsGroup::create([
            'name' => $name,
            'creator_id' => $creatorId,
            'galaxy_to' => $galaxyTo,
            'system_to' => $systemTo,
            'position_to' => $positionTo,
            'type_to' => $typeTo,
            'arrival_time' => $arrivalTime,
            'status' => 'pending',
        ]);
    }

    /**
     * Add a fleet to an ACS group
     */
    public static function addFleetToGroup(AcsGroup $group, FleetMission $fleetMission, int $playerId): AcsFleetMember
    {
        return AcsFleetMember::create([
            'acs_group_id' => $group->id,
            'fleet_mission_id' => $fleetMission->id,
            'player_id' => $playerId,
        ]);
    }

    /**
     * Get all fleets in an ACS group
     */
    public static function getGroupFleets(AcsGroup $group): Collection
    {
        return $group->fleetMembers()->with('fleetMission')->get();
    }

    /**
     * Check if all fleets in ACS group have arrived
     */
    public static function allFleetsArrived(AcsGroup $group): bool
    {
        $fleets = self::getGroupFleets($group);
        $currentTime = time();

        foreach ($fleets as $member) {
            // Check if this fleet's arrival time has passed
            if ($member->fleetMission->time_arrival > $currentTime) {
                return false;
            }
        }

        return true;
    }

    /**
     * Mark ACS group as active
     */
    public static function activateGroup(AcsGroup $group): void
    {
        $group->status = 'active';
        $group->save();
    }

    /**
     * Mark ACS group as completed
     */
    public static function completeGroup(AcsGroup $group): void
    {
        $group->status = 'completed';
        $group->save();
    }

    /**
     * Cancel an ACS group
     */
    public static function cancelGroup(AcsGroup $group): void
    {
        $group->status = 'cancelled';
        $group->save();
    }

    /**
     * Get all active ACS groups for a specific target
     */
    public static function getGroupsForTarget(int $galaxy, int $system, int $position, int $type): Collection
    {
        return AcsGroup::where('galaxy_to', $galaxy)
            ->where('system_to', $system)
            ->where('position_to', $position)
            ->where('type_to', $type)
            ->whereIn('status', ['pending', 'active'])
            ->get();
    }

    /**
     * Get ACS groups created by a player
     */
    public static function getPlayerGroups(int $playerId): Collection
    {
        return AcsGroup::where('creator_id', $playerId)
            ->whereIn('status', ['pending', 'active'])
            ->orderBy('arrival_time', 'asc')
            ->get();
    }

    /**
     * Find an ACS group by ID
     */
    public static function findGroup(int $groupId): ?AcsGroup
    {
        return AcsGroup::find($groupId);
    }

    /**
     * Check if a player can join an ACS group
     */
    public static function canJoinGroup(AcsGroup $group, int $playerId): bool
    {
        // Group must be pending or active
        if (!in_array($group->status, ['pending', 'active'])) {
            return false;
        }

        // Player must not already be in this group
        $existingMember = AcsFleetMember::where('acs_group_id', $group->id)
            ->where('player_id', $playerId)
            ->exists();

        return !$existingMember;
    }

    /**
     * Invite a player to an ACS group
     */
    public static function invitePlayer(AcsGroup $group, int $playerId): ?AcsInvitation
    {
        // Check if already invited
        $existing = AcsInvitation::where('acs_group_id', $group->id)
            ->where('invited_player_id', $playerId)
            ->first();

        if ($existing) {
            return null;
        }

        return AcsInvitation::create([
            'acs_group_id' => $group->id,
            'invited_player_id' => $playerId,
            'status' => 'pending',
        ]);
    }

    /**
     * Get pending invitations for a player
     */
    public static function getPlayerInvitations(int $playerId): Collection
    {
        return AcsInvitation::where('invited_player_id', $playerId)
            ->where('status', 'pending')
            ->with('acsGroup')
            ->get();
    }

    /**
     * Accept an invitation
     */
    public static function acceptInvitation(AcsInvitation $invitation): bool
    {
        if ($invitation->status !== 'pending') {
            return false;
        }

        $invitation->status = 'joined';
        $invitation->save();

        return true;
    }

    /**
     * Decline an invitation
     */
    public static function declineInvitation(AcsInvitation $invitation): bool
    {
        if ($invitation->status !== 'pending') {
            return false;
        }

        $invitation->status = 'declined';
        $invitation->save();

        return true;
    }
}
