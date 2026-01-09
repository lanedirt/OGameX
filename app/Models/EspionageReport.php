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
 * @property int|null $counter_espionage_chance
 * @property array<string, string> $player_info
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $planetUserId
 * @method static Builder|EspionageReport newModelQuery()
 * @method static Builder|EspionageReport newQuery()
 * @method static Builder|EspionageReport query()
 * @method static Builder|EspionageReport whereBuildings($value)
 * @method static Builder|EspionageReport whereCreatedAt($value)
 * @method static Builder|EspionageReport whereDefense($value)
 * @method static Builder|EspionageReport whereId($value)
 * @method static Builder|EspionageReport wherePlanetGalaxy($value)
 * @method static Builder|EspionageReport wherePlanetPosition($value)
 * @method static Builder|EspionageReport wherePlanetSystem($value)
 * @method static Builder|EspionageReport wherePlanetUserId($value)
 * @method static Builder|EspionageReport wherePlayerInfo($value)
 * @method static Builder|EspionageReport whereResearch($value)
 * @method static Builder|EspionageReport whereResources($value)
 * @method static Builder|EspionageReport whereShips($value)
 * @method static Builder|EspionageReport whereUpdatedAt($value)
 * @method static Builder|EspionageReport whereDebris($value)
 * @property int $planet_type
 * @method static Builder<static>|EspionageReport wherePlanetType($value)
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
