<?php

namespace OGame\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcsGroup extends Model
{
    protected $fillable = [
        'name',
        'creator_id',
        'galaxy_to',
        'system_to',
        'position_to',
        'type_to',
        'arrival_time',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the creator of this ACS group
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Get all fleet members in this ACS group
     */
    public function fleetMembers(): HasMany
    {
        return $this->hasMany(AcsFleetMember::class);
    }

    /**
     * Get all invitations for this ACS group
     */
    public function invitations(): HasMany
    {
        return $this->hasMany(AcsInvitation::class);
    }
}
