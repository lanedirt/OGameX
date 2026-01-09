<?php

namespace OGame\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 *
 *
 * @property int $id
 * @property int $galaxy
 * @property int $system
 * @property int $planet
 * @property float $metal
 * @property float $crystal
 * @property float $deuterium
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|DebrisField newModelQuery()
 * @method static Builder|DebrisField newQuery()
 * @method static Builder|DebrisField query()
 * @method static Builder|DebrisField whereCreatedAt($value)
 * @method static Builder|DebrisField whereCrystal($value)
 * @method static Builder|DebrisField whereDeuterium($value)
 * @method static Builder|DebrisField whereGalaxy($value)
 * @method static Builder|DebrisField whereId($value)
 * @method static Builder|DebrisField whereMetal($value)
 * @method static Builder|DebrisField wherePlanet($value)
 * @method static Builder|DebrisField whereSystem($value)
 * @method static Builder|DebrisField whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DebrisField extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'galaxy',
        'system',
        'planet',
        'metal',
        'crystal',
        'deuterium',
    ];
}
