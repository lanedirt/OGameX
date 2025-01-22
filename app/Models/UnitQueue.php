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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \OGame\Models\Planet $planet
 * @method static \Illuminate\Database\Eloquent\Builder|UnitQueue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UnitQueue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UnitQueue query()
 * @method static \Illuminate\Database\Eloquent\Builder|UnitQueue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitQueue whereCrystal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitQueue whereDeuterium($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitQueue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitQueue whereMetal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitQueue whereObjectAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitQueue whereObjectAmountProgress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitQueue whereObjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitQueue wherePlanetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitQueue whereProcessed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitQueue whereTimeDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitQueue whereTimeEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitQueue whereTimeProgress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitQueue whereTimeStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitQueue whereUpdatedAt($value)
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
