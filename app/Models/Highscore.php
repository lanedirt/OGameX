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
 * @property int $player_id
 * @property int $general
 * @property int $economy
 * @property int $research
 * @property int $military_built
 * @property int $military_destroyed
 * @property int $military_lost
 * @property int $general_rank
 * @property int $economy_rank
 * @property int $research_rank
 * @property int $military_built_rank
 * @property int $military_destroyed_rank
 * @property int $military_lost_rank
 * @property-read User $player
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
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
 * @method static Builder<static>|Highscore whereMilitaryBuilt($value)
 * @method static Builder<static>|Highscore whereMilitaryDestroyed($value)
 * @method static Builder<static>|Highscore whereMilitaryLost($value)
 * @method static Builder<static>|Highscore whereMilitaryBuiltRank($value)
 * @method static Builder<static>|Highscore whereMilitaryDestroyedRank($value)
 * @method static Builder<static>|Highscore whereMilitaryLostRank($value)
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
        'military_built',
        'military_destroyed',
        'military_lost',
        'general_rank',
        'economy_rank',
        'research_rank',
        'military_built_rank',
        'military_destroyed_rank',
        'military_lost_rank',
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
     * Filters out null ranks and rank 0 (used for excluded players like Legor and admins when hidden).
     */
    public function scopeValidRanks(Builder $query): void
    {
        $query->where(function ($query) {
            $query->where('general_rank', '>', 0)
                ->where('economy_rank', '>', 0)
                ->where('military_built_rank', '>', 0)
                ->where('military_destroyed_rank', '>', 0)
                ->where('military_lost_rank', '>', 0)
                ->where('research_rank', '>', 0);
        });
    }
}
