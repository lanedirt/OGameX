<?php

namespace OGame\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcsFleetMember extends Model
{
    protected $fillable = [
        'acs_group_id',
        'fleet_mission_id',
        'player_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the ACS group this fleet belongs to
     */
    public function acsGroup(): BelongsTo
    {
        return $this->belongsTo(AcsGroup::class);
    }

    /**
     * Get the fleet mission
     */
    public function fleetMission(): BelongsTo
    {
        return $this->belongsTo(FleetMission::class);
    }

    /**
     * Get the player who owns this fleet
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_id');
    }
}
