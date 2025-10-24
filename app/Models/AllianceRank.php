<?php

namespace OGame\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Alliance Rank Model
 *
 * @property int $id
 * @property int $alliance_id
 * @property string $name
 * @property bool $can_invite
 * @property bool $can_kick
 * @property bool $can_see_applications
 * @property bool $can_accept_applications
 * @property bool $can_edit_alliance
 * @property bool $can_manage_ranks
 * @property bool $can_send_circular_message
 * @property bool $can_view_member_list
 * @property bool $can_use_alliance_depot
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Alliance $alliance
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AllianceMember> $members
 * @property-read int|null $members_count
 * @mixin \Eloquent
 */
class AllianceRank extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'alliance_id',
        'name',
        'can_invite',
        'can_kick',
        'can_see_applications',
        'can_accept_applications',
        'can_edit_alliance',
        'can_manage_ranks',
        'can_send_circular_message',
        'can_view_member_list',
        'can_use_alliance_depot',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'can_invite' => 'boolean',
        'can_kick' => 'boolean',
        'can_see_applications' => 'boolean',
        'can_accept_applications' => 'boolean',
        'can_edit_alliance' => 'boolean',
        'can_manage_ranks' => 'boolean',
        'can_send_circular_message' => 'boolean',
        'can_view_member_list' => 'boolean',
        'can_use_alliance_depot' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the alliance this rank belongs to.
     *
     * @return BelongsTo
     */
    public function alliance(): BelongsTo
    {
        return $this->belongsTo(Alliance::class);
    }

    /**
     * Get all members with this rank.
     *
     * @return HasMany
     */
    public function members(): HasMany
    {
        return $this->hasMany(AllianceMember::class, 'rank_id');
    }
}
