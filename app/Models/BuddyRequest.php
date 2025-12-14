<?php

namespace OGame\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 *
 *
 * @property int $id
 * @property int $sender_user_id
 * @property int $receiver_user_id
 * @property int $status
 * @property string|null $message
 * @property bool $viewed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $sender
 * @property-read User $receiver
 * @method static \Illuminate\Database\Eloquent\Builder|BuddyRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuddyRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuddyRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder|BuddyRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuddyRequest whereSenderUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuddyRequest whereReceiverUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuddyRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuddyRequest whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuddyRequest whereViewed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuddyRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuddyRequest whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuddyRequest extends Model
{
    use HasFactory;

    /**
     * Status constants
     */
    public const STATUS_PENDING = 0;
    public const STATUS_ACCEPTED = 1;
    public const STATUS_REJECTED = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'sender_user_id',
        'receiver_user_id',
        'status',
        'message',
        'viewed',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'viewed' => 'boolean',
    ];

    /**
     * Get the user who sent the buddy request.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    /**
     * Get the user who received the buddy request.
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_user_id');
    }

    /**
     * Check if the request is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the request is accepted.
     */
    public function isAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    /**
     * Check if the request is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }
}
