<?php

namespace OGame\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 *
 *
 * @property int $id
 * @property int $player_id
 * @property int $general
 * @property int $economy
 * @property int $research
 * @property int $military
 * @property int $general_rank
 * @property int $economy_rank
 * @property int $research_rank
 * @property int $military_rank
 * @property-read User $player
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder<static>|Highscore newModelQuery()
 * @method static Builder<static>|Highscore newQuery()
 * @method static Builder<static>|Highscore query()
 * @method static Builder<static>|Highscore validRanks()
 * @method static Builder<static>|Highscore whereCreatedAt($value)
 * @method static Builder<static>|Highscore whereEconomy($value)
 * @method static Builder<static>|Highscore whereEconomyRank($value)
 * @method static Builder<static>|Highscore whereGeneral($value)
 * @method static Builder<static>|Highscore whereGeneralRank($value)
 * @method static Builder<static>|Highscore whereId($value)
 * @method static Builder<static>|Highscore whereMilitary($value)
 * @method static Builder<static>|Highscore whereMilitaryRank($value)
 * @method static Builder<static>|Highscore wherePlayerId($value)
 * @method static Builder<static>|Highscore whereResearch($value)
 * @method static Builder<static>|Highscore whereResearchRank($value)
 * @method static Builder<static>|Highscore whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Highscore extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
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
     * Get the user that owns the note record.
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
