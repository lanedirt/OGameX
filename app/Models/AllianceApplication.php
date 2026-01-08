<?php

namespace OGame\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 *
 *
 * @property int $id
 * @property int $alliance_id
 * @property int $user_id
 * @property string|null $application_message
 * @property int $status
 * @property bool $viewed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Alliance $alliance
 * @property-read User $user
 * @method static Builder|AllianceApplication newModelQuery()
 * @method static Builder|AllianceApplication newQuery()
 * @method static Builder|AllianceApplication query()
 * @method static Builder|AllianceApplication whereId($value)
 * @method static Builder|AllianceApplication whereAllianceId($value)
 * @method static Builder|AllianceApplication whereUserId($value)
 * @method static Builder|AllianceApplication whereApplicationMessage($value)
 * @method static Builder|AllianceApplication whereStatus($value)
 * @method static Builder|AllianceApplication whereViewed($value)
 * @method static Builder|AllianceApplication whereCreatedAt($value)
 * @method static Builder|AllianceApplication whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AllianceApplication extends Model
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
        'alliance_id',
        'user_id',
        'application_message',
        'status',
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
     * Get the alliance this application is for.
     */
    public function alliance(): BelongsTo
    {
        return $this->belongsTo(Alliance::class);
    }

    /**
     * Get the user who submitted this application.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the application is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the application is accepted.
     */
    public function isAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    /**
     * Check if the application is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Accept the application.
     */
    public function accept(): void
    {
        $this->status = self::STATUS_ACCEPTED;
    }

    /**
     * Reject the application.
     */
    public function reject(): void
    {
        $this->status = self::STATUS_REJECTED;
    }
}
