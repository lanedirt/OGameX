<?php

namespace OGame\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 *
 * @property int $id
 * @property int $planet_galaxy
 * @property int $planet_system
 * @property int $planet_position
 * @property int $planet_user_id
 * @property array<string, int> $resources
 * @property array<string, int> $debris
 * @property array<string, int> $buildings
 * @property array<string, int> $research
 * @property array<string, int> $ships
 * @property array<string, int> $defense
 * @property array<string, string> $player_info
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \OGame\Models\User|null $planetUserId
 * @method static \Illuminate\Database\Eloquent\Builder|EspionageReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EspionageReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EspionageReport query()
 * @method static \Illuminate\Database\Eloquent\Builder|EspionageReport whereBuildings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EspionageReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EspionageReport whereDefense($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EspionageReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EspionageReport wherePlanetGalaxy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EspionageReport wherePlanetPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EspionageReport wherePlanetSystem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EspionageReport wherePlanetUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EspionageReport wherePlayerInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EspionageReport whereResearch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EspionageReport whereResources($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EspionageReport whereShips($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EspionageReport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EspionageReport whereDebris($value)
 * @property int $planet_type
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EspionageReport wherePlanetType($value)
 * @mixin \Eloquent
 */
class EspionageReport extends Model
{
    protected $casts = [
        'player_info' => 'array',
        'resources' => 'array',
        'debris' => 'array',
        'buildings' => 'array',
        'research' => 'array',
        'ships' => 'array',
        'defense' => 'array',
    ];

    /**
     * Get the player that owns the planet that this espionage report is about.
     * Note: this is not the player that owns the espionage report!
     */
    public function planetUserId(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
