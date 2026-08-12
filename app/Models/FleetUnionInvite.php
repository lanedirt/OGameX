<?php

namespace OGame\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $fleet_union_id
 * @property int $user_id
 * @property-read FleetUnion $fleetUnion
 * @property-read User $user
 */
#[Fillable([
    'fleet_union_id',
    'user_id',
])]
#[Table(name: 'fleet_union_invites')]
class FleetUnionInvite extends Model
{
    public function fleetUnion(): BelongsTo
    {
        return $this->belongsTo(FleetUnion::class, 'fleet_union_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
