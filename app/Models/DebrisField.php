<?php

namespace OGame\Models;

use Illuminate\Database\Eloquent\Model;

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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|DebrisField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DebrisField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DebrisField query()
 * @method static \Illuminate\Database\Eloquent\Builder|DebrisField whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DebrisField whereCrystal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DebrisField whereDeuterium($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DebrisField whereGalaxy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DebrisField whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DebrisField whereMetal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DebrisField wherePlanet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DebrisField whereSystem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DebrisField whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DebrisField extends Model
{
}
