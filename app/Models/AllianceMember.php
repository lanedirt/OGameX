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
 * @property int|null $rank_id
 * @property Carbon $joined_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Alliance $alliance
 * @property-read User $user
 * @property-read AllianceRank|null $rank
 * @method static Builder|AllianceMember newModelQuery()
 * @method static Builder|AllianceMember newQuery()
 * @method static Builder|AllianceMember query()
 * @method static Builder|AllianceMember whereId($value)
 * @method static Builder|AllianceMember whereAllianceId($value)
 * @method static Builder|AllianceMember whereUserId($value)
 * @method static Builder|AllianceMember whereRankId($value)
 * @method static Builder|AllianceMember whereJoinedAt($value)
 * @method static Builder|AllianceMember whereCreatedAt($value)
 * @method static Builder|AllianceMember whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AllianceMember extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'alliance_id',
        'user_id',
        'rank_id',
        'joined_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'joined_at' => 'datetime',
    ];

    /**
     * Get the alliance this member belongs to.
     */
    public function alliance(): BelongsTo
    {
        return $this->belongsTo(Alliance::class);
    }

    /**
     * Get the user for this membership.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the rank of this member.
     */
    public function rank(): BelongsTo
    {
        return $this->belongsTo(AllianceRank::class);
    }

    /**
     * Check if this member is the founder.
     */
    public function isFounder(): bool
    {
        return $this->alliance->founder_user_id === $this->user_id;
    }

    /**
     * Check if this member has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        // Founder has all permissions
        if ($this->isFounder()) {
            return true;
        }

        // Check if rank has the permission
        if ($this->rank) {
            $permissions = $this->rank->permissions ?? [];
            return in_array($permission, $permissions, true);
        }

        return false;
    }
}
