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

        // Allow the same player to join multiple times from different planets
        // This is valid in OGame - a player can coordinate attacks from multiple planets
        // The only restriction is that arrival time hasn't passed
        if ($group->arrival_time <= time()) {
            return false;
        }

        // Check ACS limits before allowing join
        $fleetCount = self::getGroupFleets($group)->count();
        $uniquePlayers = self::getGroupFleets($group)->pluck('player_id')->unique()->toArray();
        $playerCount = count($uniquePlayers);
        $playerAlreadyInGroup = in_array($playerId, $uniquePlayers);

        // OGame limits: 16 fleets max
        if ($fleetCount >= 16) {
            return false;
        }

        // OGame limits: 5 unique players max (only count new players)
        if (!$playerAlreadyInGroup && $playerCount >= 5) {
            return false;
        }

        // Check if player is the creator (always allowed, assuming limits not exceeded)
        if ($group->creator_id === $playerId) {
            return true;
        }

        // Check if player is buddy or alliance member of the creator
        if (!self::isBuddyOrAllianceMember($group->creator_id, $playerId)) {
            return false;
        }

        return true;
    }

    /**
     * Check if two players are buddies or in the same alliance
     */
    public static function isBuddyOrAllianceMember(int $player1Id, int $player2Id): bool
    {
        // Check if they are buddies (bidirectional check)
        $areBuddies = \OGame\Models\Buddy::where(function ($query) use ($player1Id, $player2Id) {
            $query->where('user_id', $player1Id)->where('buddy_id', $player2Id);
        })->orWhere(function ($query) use ($player1Id, $player2Id) {
            $query->where('user_id', $player2Id)->where('buddy_id', $player1Id);
        })->exists();

        if ($areBuddies) {
            return true;
        }

        // Check if they are in the same alliance
        $player1Alliance = \OGame\Models\AllianceMember::where('user_id', $player1Id)->first();
        $player2Alliance = \OGame\Models\AllianceMember::where('user_id', $player2Id)->first();

        if ($player1Alliance && $player2Alliance && $player1Alliance->alliance_id === $player2Alliance->alliance_id) {
            return true;
        }

        return false;
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

    /**
     * Get the number of unique players in an ACS group
     */
    public static function getGroupPlayerCount(AcsGroup $group): int
    {
        return self::getGroupFleets($group)->pluck('player_id')->unique()->count();
    }

    /**
     * Get the number of fleets in an ACS group
     */
    public static function getGroupFleetCount(AcsGroup $group): int
    {
        return self::getGroupFleets($group)->count();
    }

    /**
     * Check if an ACS group is full (16 fleets or 5 players)
     */
    public static function isGroupFull(AcsGroup $group, int $newPlayerId): bool
    {
        $fleetCount = self::getGroupFleetCount($group);
        if ($fleetCount >= 16) {
            return true;
        }

        $uniquePlayers = self::getGroupFleets($group)->pluck('player_id')->unique()->toArray();
        $playerAlreadyInGroup = in_array($newPlayerId, $uniquePlayers);

        // If player is new and would exceed 5 players, group is full
        if (!$playerAlreadyInGroup && count($uniquePlayers) >= 5) {
            return true;
        }

        return false;
    }
}
