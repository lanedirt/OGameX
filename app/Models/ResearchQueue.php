<?php

namespace OGame\Models;

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
 * @property int $metal
 * @property int $crystal
 * @property int $deuterium
 * @property int $building
 * @property int $processed
 * @property int $canceled
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \OGame\Models\Planet $planet
 * @method static \Illuminate\Database\Eloquent\Builder|ResearchQueue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ResearchQueue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ResearchQueue query()
 * @method static \Illuminate\Database\Eloquent\Builder|ResearchQueue whereBuilding($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResearchQueue whereCanceled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResearchQueue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResearchQueue whereCrystal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResearchQueue whereDeuterium($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResearchQueue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResearchQueue whereMetal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResearchQueue whereObjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResearchQueue whereObjectLevelTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResearchQueue wherePlanetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResearchQueue whereProcessed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResearchQueue whereTimeDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResearchQueue whereTimeEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResearchQueue whereTimeStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResearchQueue whereUpdatedAt($value)
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
