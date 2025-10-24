<?php

namespace OGame\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $buddy_id
 * @property \Illuminate\Support\Carbon $created_at
 */
class Buddy extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'buddy_id',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Get the user who owns this buddy relationship
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the buddy user
     */
    public function buddyUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buddy_id');
    }
}
