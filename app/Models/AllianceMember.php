<?php

namespace OGame\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Alliance Member Model
 *
 * @property int $id
 * @property int $alliance_id
 * @property int $user_id
 * @property int|null $rank_id
 * @property string|null $application_text
 * @property \Illuminate\Support\Carbon|null $joined_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Alliance $alliance
 * @property-read User $user
 * @property-read AllianceRank|null $rank
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
        'application_text',
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
     *
     * @return BelongsTo
     */
    public function alliance(): BelongsTo
    {
        return $this->belongsTo(Alliance::class);
    }

    /**
     * Get the user associated with this membership.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the rank of this member.
     *
     * @return BelongsTo
     */
    public function rank(): BelongsTo
    {
        return $this->belongsTo(AllianceRank::class, 'rank_id');
    }
}
