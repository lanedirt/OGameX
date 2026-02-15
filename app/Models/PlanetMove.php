<?php

namespace OGame\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $planet_id
 * @property int $target_galaxy
 * @property int $target_system
 * @property int $target_position
 * @property int $time_start
 * @property int $time_arrive
 * @property bool $canceled
 * @property bool $processed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|PlanetMove newModelQuery()
 * @method static Builder|PlanetMove newQuery()
 * @method static Builder|PlanetMove query()
 * @mixin \Eloquent
 */
class PlanetMove extends Model
{
    protected $table = 'planet_moves';

    protected $fillable = [
        'planet_id',
        'target_galaxy',
        'target_system',
        'target_position',
        'time_start',
        'time_arrive',
        'canceled',
        'processed',
    ];
}
