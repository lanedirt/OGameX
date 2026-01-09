<?php

namespace OGame\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 *
 *
 * @property int $id
 * @property string $alliance_tag
 * @property string $alliance_name
 * @property int $founder_user_id
 * @property string|null $internal_text
 * @property string|null $external_text
 * @property string|null $application_text
 * @property string|null $logo_url
 * @property string|null $homepage_url
 * @property bool $is_open
 * @property string $founder_rank_name
 * @property string $newcomer_rank_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $founder
 * @property-read Collection<int, AllianceMember> $members
 * @property-read int|null $members_count
 * @property-read Collection<int, AllianceRank> $ranks
 * @property-read int|null $ranks_count
 * @property-read Collection<int, AllianceApplication> $applications
 * @property-read int|null $applications_count
 * @method static Builder|Alliance newModelQuery()
 * @method static Builder|Alliance newQuery()
 * @method static Builder|Alliance query()
 * @method static Builder|Alliance whereId($value)
 * @method static Builder|Alliance whereAllianceTag($value)
 * @method static Builder|Alliance whereAllianceName($value)
 * @method static Builder|Alliance whereFounderUserId($value)
 * @method static Builder|Alliance whereInternalText($value)
 * @method static Builder|Alliance whereExternalText($value)
 * @method static Builder|Alliance whereApplicationText($value)
 * @method static Builder|Alliance whereLogoUrl($value)
 * @method static Builder|Alliance whereHomepageUrl($value)
 * @method static Builder|Alliance whereIsOpen($value)
 * @method static Builder|Alliance whereFounderRankName($value)
 * @method static Builder|Alliance whereNewcomerRankName($value)
 * @method static Builder|Alliance whereCreatedAt($value)
 * @method static Builder|Alliance whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Alliance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'alliance_tag',
        'alliance_name',
        'founder_user_id',
        'internal_text',
        'external_text',
        'application_text',
        'logo_url',
        'homepage_url',
        'is_open',
        'founder_rank_name',
        'newcomer_rank_name',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_open' => 'boolean',
    ];

    /**
     * Get the founder user of this alliance.
     */
    public function founder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'founder_user_id');
    }

    /**
     * Get all members of this alliance.
     */
    public function members(): HasMany
    {
        return $this->hasMany(AllianceMember::class);
    }

    /**
     * Get all ranks for this alliance.
     */
    public function ranks(): HasMany
    {
        return $this->hasMany(AllianceRank::class);
    }

    /**
     * Get all applications to this alliance.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(AllianceApplication::class);
    }

    /**
     * Get the highscore record for this alliance.
     */
    public function highscore(): HasOne
    {
        return $this->hasOne(AllianceHighscore::class);
    }

    /**
     * Get total member count for this alliance.
     */
    public function getMemberCountAttribute(): int
    {
        return $this->members()->count();
    }

    /**
     * Check if the alliance is accepting applications.
     */
    public function isOpen(): bool
    {
        return $this->is_open;
    }
}
