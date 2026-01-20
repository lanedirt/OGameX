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
 * @property int|null $planet_id_from
 * @property int|null $planet_id_to
 * @property int|null $galaxy_to
 * @property int|null $system_to
 * @property int|null $position_to
 * @property int $mission_type
 * @property int|null $union_id
 * @property int|null $union_slot
 * @property int $time_departure
 * @property int $time_arrival
 * @property float $metal
 * @property float $crystal
 * @property float $deuterium
 * @property float $deuterium_consumption
 * @property int $light_fighter
 * @property int $heavy_fighter
 * @property int $cruiser
 * @property int $battle_ship
 * @property int $battlecruiser
 * @property int $bomber
 * @property int $destroyer
 * @property int $deathstar
 * @property int $small_cargo
 * @property int $large_cargo
 * @property int $colony_ship
 * @property int $recycler
 * @property int $espionage_probe
 * @property int $interplanetary_missile
 * @property int|null $target_priority
 * @property int $processed
 * @property int $processed_hold
 * @property int $canceled
 * @property array|null $wreck_field_data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Planet|null $planetFrom
 * @property-read Planet|null $planetTo
 * @method static Builder|FleetMission newModelQuery()
 * @method static Builder|FleetMission newQuery()
 * @method static Builder|FleetMission query()
 * @method static Builder|FleetMission whereBattleShip($value)
 * @method static Builder|FleetMission whereBattlecruiser($value)
 * @method static Builder|FleetMission whereBomber($value)
 * @method static Builder|FleetMission whereCanceled($value)
 * @method static Builder|FleetMission whereColonyShip($value)
 * @method static Builder|FleetMission whereCreatedAt($value)
 * @method static Builder|FleetMission whereCruiser($value)
 * @method static Builder|FleetMission whereCrystal($value)
 * @method static Builder|FleetMission whereDeathstar($value)
 * @method static Builder|FleetMission whereDestroyer($value)
 * @method static Builder|FleetMission whereDeuterium($value)
 * @method static Builder|FleetMission whereEspionageProbe($value)
 * @method static Builder|FleetMission whereInterplanetaryMissile($value)
 * @method static Builder|FleetMission whereTargetPriority($value)
 * @method static Builder|FleetMission whereGalaxyTo($value)
 * @method static Builder|FleetMission whereHeavyFighter($value)
 * @method static Builder|FleetMission whereId($value)
 * @method static Builder|FleetMission whereLargeCargo($value)
 * @method static Builder|FleetMission whereLightFighter($value)
 * @method static Builder|FleetMission whereMetal($value)
 * @method static Builder|FleetMission whereMissionType($value)
 * @method static Builder|FleetMission wherePlanetIdFrom($value)
 * @method static Builder|FleetMission wherePlanetIdTo($value)
 * @method static Builder|FleetMission wherePositionTo($value)
 * @method static Builder|FleetMission whereProcessed($value)
 * @method static Builder|FleetMission whereProcessedHold($value)
 * @method static Builder|FleetMission whereRecycler($value)
 * @method static Builder|FleetMission whereSmallCargo($value)
 * @method static Builder|FleetMission whereSystemTo($value)
 * @method static Builder|FleetMission whereTimeArrival($value)
 * @method static Builder|FleetMission whereTimeDeparture($value)
 * @method static Builder|FleetMission whereUpdatedAt($value)
 * @property int|null $parent_id
 * @property int $user_id
 * @property int|null $galaxy_from
 * @property int|null $system_from
 * @property int|null $position_from
 * @method static Builder|FleetMission whereGalaxyFrom($value)
 * @method static Builder|FleetMission whereParentId($value)
 * @method static Builder|FleetMission wherePositionFrom($value)
 * @method static Builder|FleetMission whereSystemFrom($value)
 * @method static Builder|FleetMission whereUserId($value)
 * @property int $type_from
 * @property int $type_to
 * @method static Builder|FleetMission whereTypeFrom($value)
 * @method static Builder|FleetMission whereTypeTo($value)
 * @method static Builder<static>|FleetMission whereDeuteriumConsumption($value)
 * @property int|null $time_holding
 * @method static Builder<static>|FleetMission whereTimeHolding($value)
 * @mixin \Eloquent
 */
class FleetMission extends Model
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'wreck_field_data' => 'array',
    ];

    /**
     * Get the planet that this fleet mission is going from.
     */
    public function planetFrom(): BelongsTo
    {
        return $this->belongsTo(Planet::class);
    }

    /**
     * Get the planet to (optional).
     */
    public function planetTo(): BelongsTo
    {
        return $this->belongsTo(Planet::class);
    }

    /**
     * Get the union this mission belongs to (for ACS Attack).
     */
    public function union(): BelongsTo
    {
        return $this->belongsTo(FleetUnion::class, 'union_id');
    }

    /**
     * Check if this mission is part of a union.
     */
    public function isInUnion(): bool
    {
        return $this->union_id !== null;
    }
}
