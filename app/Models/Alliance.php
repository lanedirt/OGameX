<?php

namespace OGame\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Alliance Model
 *
 * @property int $id
 * @property string $tag
 * @property string $name
 * @property string|null $description
 * @property string|null $logo
 * @property string|null $external_url
 * @property string|null $internal_text
 * @property string|null $application_text
 * @property int $founder_id
 * @property bool $open_for_applications
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $founder
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AllianceMember> $members
 * @property-read int|null $members_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AllianceRank> $ranks
 * @property-read int|null $ranks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AllianceApplication> $applications
 * @property-read int|null $applications_count
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
        'tag',
        'name',
        'description',
        'logo',
        'external_url',
        'internal_text',
        'application_text',
        'founder_id',
        'open_for_applications',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'open_for_applications' => 'boolean',
    ];

    /**
     * Get the founder of the alliance.
     *
     * @return BelongsTo
     */
    public function founder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'founder_id');
    }

    /**
     * Get all members of the alliance.
     *
     * @return HasMany
     */
    public function members(): HasMany
    {
        return $this->hasMany(AllianceMember::class);
    }

    /**
     * Get all ranks in the alliance.
     *
     * @return HasMany
     */
    public function ranks(): HasMany
    {
        return $this->hasMany(AllianceRank::class);
    }

    /**
     * Get all applications to the alliance.
     *
     * @return HasMany
     */
    public function applications(): HasMany
    {
        return $this->hasMany(AllianceApplication::class);
    }

    /**
     * Get pending applications.
     *
     * @return HasMany
     */
    public function pendingApplications(): HasMany
    {
        return $this->hasMany(AllianceApplication::class)->where('status', 'pending');
    }
}
