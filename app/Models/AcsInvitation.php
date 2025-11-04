<?php

namespace OGame\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcsInvitation extends Model
{
    protected $fillable = [
        'acs_group_id',
        'invited_player_id',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the ACS group this invitation is for
     */
    public function acsGroup(): BelongsTo
    {
        return $this->belongsTo(AcsGroup::class);
    }

    /**
     * Get the invited player
     */
    public function invitedPlayer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_player_id');
    }
}
