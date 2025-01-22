<?php

namespace OGame\Models;

use Illuminate\Database\Eloquent\Model;

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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingQueue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingQueue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingQueue query()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingQueue whereBuilding($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingQueue whereCanceled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingQueue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingQueue whereCrystal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingQueue whereDeuterium($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingQueue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingQueue whereMetal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingQueue whereObjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingQueue whereObjectLevelTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingQueue wherePlanetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingQueue whereProcessed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingQueue whereTimeDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingQueue whereTimeEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingQueue whereTimeStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingQueue whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingQueue extends Model
{
    //
}
