<?php

namespace OGame\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 *
 * @property int $id
 * @property int $planet_id
 * @property int $object_id
 * @property int $object_level_target
 * @property int $time_duration
 * @property int $time_start
 * @property int $time_end
 * @property float $metal
 * @property float $crystal
 * @property float $deuterium
 * @property int $building
 * @property int $processed
 * @property int $canceled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Planet $planet
 * @method static Builder|ResearchQueue newModelQuery()
 * @method static Builder|ResearchQueue newQuery()
 * @method static Builder|ResearchQueue query()
 * @method static Builder|ResearchQueue whereBuilding($value)
 * @method static Builder|ResearchQueue whereCanceled($value)
 * @method static Builder|ResearchQueue whereCreatedAt($value)
 * @method static Builder|ResearchQueue whereCrystal($value)
 * @method static Builder|ResearchQueue whereDeuterium($value)
 * @method static Builder|ResearchQueue whereId($value)
 * @method static Builder|ResearchQueue whereMetal($value)
 * @method static Builder|ResearchQueue whereObjectId($value)
 * @method static Builder|ResearchQueue whereObjectLevelTarget($value)
 * @method static Builder|ResearchQueue wherePlanetId($value)
 * @method static Builder|ResearchQueue whereProcessed($value)
 * @method static Builder|ResearchQueue whereTimeDuration($value)
 * @method static Builder|ResearchQueue whereTimeEnd($value)
 * @method static Builder|ResearchQueue whereTimeStart($value)
 * @method static Builder|ResearchQueue whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ResearchQueue extends Model
{
    /**
     * Get the planet that owns the research queue record.
     */
    public function planet(): BelongsTo
    {
        return $this->belongsTo(Planet::class);
    }
}
