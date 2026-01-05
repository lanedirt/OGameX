<?php

namespace OGame\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property int $planet_id
 * @property int $object_id
 * @property int $object_level_target
 * @property bool $is_downgrade
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
 * @method static Builder|BuildingQueue newModelQuery()
 * @method static Builder|BuildingQueue newQuery()
 * @method static Builder|BuildingQueue query()
 * @method static Builder|BuildingQueue whereBuilding($value)
 * @method static Builder|BuildingQueue whereCanceled($value)
 * @method static Builder|BuildingQueue whereCreatedAt($value)
 * @method static Builder|BuildingQueue whereCrystal($value)
 * @method static Builder|BuildingQueue whereDeuterium($value)
 * @method static Builder|BuildingQueue whereId($value)
 * @method static Builder|BuildingQueue whereMetal($value)
 * @method static Builder|BuildingQueue whereObjectId($value)
 * @method static Builder|BuildingQueue whereObjectLevelTarget($value)
 * @method static Builder|BuildingQueue whereIsDowngrade($value)
 * @method static Builder|BuildingQueue wherePlanetId($value)
 * @method static Builder|BuildingQueue whereProcessed($value)
 * @method static Builder|BuildingQueue whereTimeDuration($value)
 * @method static Builder|BuildingQueue whereTimeEnd($value)
 * @method static Builder|BuildingQueue whereTimeStart($value)
 * @method static Builder|BuildingQueue whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingQueue extends Model
{
    //
}
