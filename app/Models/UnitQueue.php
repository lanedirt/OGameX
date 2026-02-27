<?php

namespace OGame\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 *
 *
 * @property int $id
 * @property int $planet_id
 * @property int $object_id
 * @property int $object_amount
 * @property int $time_duration
 * @property int $time_start
 * @property int $time_end
 * @property int $time_progress
 * @property int $object_amount_progress
 * @property float $metal
 * @property float $crystal
 * @property float $deuterium
 * @property int $processed
 * @property int $dm_halved
 * @property int $dm_completed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Planet $planet
 * @method static Builder|UnitQueue newModelQuery()
 * @method static Builder|UnitQueue newQuery()
 * @method static Builder|UnitQueue query()
 * @method static Builder|UnitQueue whereCreatedAt($value)
 * @method static Builder|UnitQueue whereCrystal($value)
 * @method static Builder|UnitQueue whereDeuterium($value)
 * @method static Builder|UnitQueue whereId($value)
 * @method static Builder|UnitQueue whereMetal($value)
 * @method static Builder|UnitQueue whereObjectAmount($value)
 * @method static Builder|UnitQueue whereObjectAmountProgress($value)
 * @method static Builder|UnitQueue whereObjectId($value)
 * @method static Builder|UnitQueue wherePlanetId($value)
 * @method static Builder|UnitQueue whereProcessed($value)
 * @method static Builder|UnitQueue whereTimeDuration($value)
 * @method static Builder|UnitQueue whereTimeEnd($value)
 * @method static Builder|UnitQueue whereTimeProgress($value)
 * @method static Builder|UnitQueue whereTimeStart($value)
 * @method static Builder|UnitQueue whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class UnitQueue extends Model
{
    /**
     * Get the planet that owns the research queue record.
     */
    public function planet(): BelongsTo
    {
        return $this->belongsTo(Planet::class);
    }
}
