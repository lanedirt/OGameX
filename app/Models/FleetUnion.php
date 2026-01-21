<?php

namespace OGame\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use OGame\Models\Enums\PlanetType;
use OGame\Models\Planet\Coordinate;

/**
 *
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $name
 * @property int $galaxy_to
 * @property int $system_to
 * @property int $position_to
 * @property int $planet_type_to
 * @property int $time_arrival
 * @property int $max_fleets
 * @property int $max_players
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $creator
 * @property-read Collection<int, FleetMission> $fleetMissions
 * @property-read int|null $fleet_missions_count
 * @method static Builder|FleetUnion newModelQuery()
 * @method static Builder|FleetUnion newQuery()
 * @method static Builder|FleetUnion query()
 * @method static Builder|FleetUnion whereId($value)
 * @method static Builder|FleetUnion whereUserId($value)
 * @method static Builder|FleetUnion whereName($value)
 * @method static Builder|FleetUnion whereGalaxyTo($value)
 * @method static Builder|FleetUnion whereSystemTo($value)
 * @method static Builder|FleetUnion wherePositionTo($value)
 * @method static Builder|FleetUnion wherePlanetTypeTo($value)
 * @method static Builder|FleetUnion whereTimeArrival($value)
 * @method static Builder|FleetUnion whereMaxFleets($value)
 * @method static Builder|FleetUnion whereMaxPlayers($value)
 * @method static Builder|FleetUnion whereCreatedAt($value)
 * @method static Builder|FleetUnion whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FleetUnion extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'galaxy_to',
        'system_to',
        'position_to',
        'planet_type_to',
        'time_arrival',
        'max_fleets',
        'max_players',
    ];

    /**
     * Get the creator of this union.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all fleet missions in this union.
     */
    public function fleetMissions(): HasMany
    {
        return $this->hasMany(FleetMission::class, 'union_id');
    }

    /**
     * Get active (non-canceled, non-processed) fleet missions.
     *
     * @return HasMany<FleetMission, $this>
     */
    public function activeFleetMissions(): HasMany
    {
        return $this->fleetMissions()->where('canceled', 0)->where('processed', 0);
    }

    /**
     * Get the target coordinate.
     */
    public function getTargetCoordinate(): Coordinate
    {
        return new Coordinate($this->galaxy_to, $this->system_to, $this->position_to);
    }

    /**
     * Get the target planet type.
     */
    public function getTargetPlanetType(): PlanetType
    {
        return PlanetType::from($this->planet_type_to);
    }

    /**
     * Get count of unique players in this union.
     */
    public function getUniquePlayerCount(): int
    {
        return $this->activeFleetMissions()
            ->distinct('user_id')
            ->count('user_id');
    }

    /**
     * Get the remaining time until arrival in seconds.
     */
    public function getRemainingTime(): int
    {
        return max(0, $this->time_arrival - time());
    }

    /**
     * Check if the union has reached max fleets.
     */
    public function hasReachedMaxFleets(): bool
    {
        return $this->activeFleetMissions()->count() >= $this->max_fleets;
    }

    /**
     * Check if the union has reached max players.
     */
    public function hasReachedMaxPlayers(): bool
    {
        return $this->getUniquePlayerCount() >= $this->max_players;
    }
}
