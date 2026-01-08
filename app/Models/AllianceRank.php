<?php

namespace OGame\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 *
 *
 * @property int $id
 * @property int $alliance_id
 * @property string $rank_name
 * @property array<int, string> $permissions
 * @property int $sort_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Alliance $alliance
 * @property-read Collection<int, AllianceMember> $members
 * @property-read int|null $members_count
 * @method static Builder|AllianceRank newModelQuery()
 * @method static Builder|AllianceRank newQuery()
 * @method static Builder|AllianceRank query()
 * @method static Builder|AllianceRank whereId($value)
 * @method static Builder|AllianceRank whereAllianceId($value)
 * @method static Builder|AllianceRank whereRankName($value)
 * @method static Builder|AllianceRank wherePermissions($value)
 * @method static Builder|AllianceRank whereSortOrder($value)
 * @method static Builder|AllianceRank whereCreatedAt($value)
 * @method static Builder|AllianceRank whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AllianceRank extends Model
{
    use HasFactory;

    /**
     * Permission constants
     */
    public const PERMISSION_SEE_APPLICATIONS = 'see_applications';
    public const PERMISSION_EDIT_APPLICATIONS = 'edit_applications';
    public const PERMISSION_SEE_MEMBERS = 'see_members';
    public const PERMISSION_KICK_USER = 'kick_user';
    public const PERMISSION_SEE_MEMBER_ONLINE_STATUS = 'see_member_online_status';
    public const PERMISSION_SEND_CIRCULAR_MSG = 'send_circular_msg';
    public const PERMISSION_DELETE_ALLY = 'delete_ally';
    public const PERMISSION_MANAGE_ALLY = 'manage_ally';
    public const PERMISSION_RIGHT_HAND = 'right_hand';
    public const PERMISSION_MANAGE_CLASSES = 'manage_classes';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'alliance_id',
        'rank_name',
        'permissions',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'permissions' => 'array',
    ];

    /**
     * Get the alliance this rank belongs to.
     */
    public function alliance(): BelongsTo
    {
        return $this->belongsTo(Alliance::class);
    }

    /**
     * Get all members with this rank.
     */
    public function members(): HasMany
    {
        return $this->hasMany(AllianceMember::class, 'rank_id');
    }

    /**
     * Check if this rank has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions ?? [], true);
    }

    /**
     * Add a permission to this rank.
     */
    public function addPermission(string $permission): void
    {
        $permissions = $this->permissions ?? [];
        if (!in_array($permission, $permissions, true)) {
            $permissions[] = $permission;
            $this->permissions = $permissions;
        }
    }

    /**
     * Remove a permission from this rank.
     */
    public function removePermission(string $permission): void
    {
        $permissions = $this->permissions ?? [];
        $this->permissions = array_values(array_filter($permissions, fn ($p) => $p !== $permission));
    }
}
