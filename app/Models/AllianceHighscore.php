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
 * @property int $general
 * @property int $economy
 * @property int $research
 * @property int $military
 * @property int|null $general_rank
 * @property int|null $economy_rank
 * @property int|null $research_rank
 * @property int|null $military_rank
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Alliance $alliance
 * @method static Builder|AllianceHighscore newModelQuery()
 * @method static Builder|AllianceHighscore newQuery()
 * @method static Builder|AllianceHighscore query()
 * @method static Builder|AllianceHighscore validRanks()
 * @method static Builder|AllianceHighscore whereId($value)
 * @method static Builder|AllianceHighscore whereAllianceId($value)
 * @method static Builder|AllianceHighscore whereGeneral($value)
 * @method static Builder|AllianceHighscore whereEconomy($value)
 * @method static Builder|AllianceHighscore whereResearch($value)
 * @method static Builder|AllianceHighscore whereMilitary($value)
 * @method static Builder|AllianceHighscore whereGeneralRank($value)
 * @method static Builder|AllianceHighscore whereEconomyRank($value)
 * @method static Builder|AllianceHighscore whereResearchRank($value)
 * @method static Builder|AllianceHighscore whereMilitaryRank($value)
 * @method static Builder|AllianceHighscore whereCreatedAt($value)
 * @method static Builder|AllianceHighscore whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AllianceHighscore extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'alliance_id',
        'general',
        'economy',
        'research',
        'military',
        'general_rank',
        'economy_rank',
        'research_rank',
        'military_rank',
    ];

    /**
     * Get the alliance that owns this highscore record.
     */
    public function alliance(): BelongsTo
    {
        return $this->belongsTo(Alliance::class);
    }

    /**
     * Scope to ensure only valid highscore ranks.
     */
    public function scopeValidRanks(Builder $query): void
    {
        $query->where(function ($query) {
            $query->where('general_rank', '!=', null)
                ->where('economy_rank', '!=', null)
                ->where('military_rank', '!=', null)
                ->where('research_rank', '!=', null);
        });
    }
}
