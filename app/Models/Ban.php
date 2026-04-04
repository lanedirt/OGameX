<?php

namespace OGame\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $reason
 * @property Carbon|null $banned_until
 * @property bool $canceled
 * @property Carbon|null $canceled_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 */
class Ban extends Model
{
    protected $table = 'bans';

    protected $fillable = [
        'user_id',
        'reason',
        'banned_until',
        'canceled',
        'canceled_at',
    ];

    protected $casts = [
        'banned_until' => 'datetime',
        'canceled'     => 'boolean',
        'canceled_at'  => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
