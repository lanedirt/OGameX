<?php

namespace OGame\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
 * @property int $time_departure
 * @property int $time_arrival
 * @property int $metal
 * @property int $crystal
 * @property int $deuterium
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
 * @property int $processed
 * @property int $canceled
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \OGame\Models\Planet|null $planetFrom
 * @property-read \OGame\Models\Planet|null $planetTo
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission query()
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission whereBattleShip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission whereBattlecruiser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission whereBomber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission whereCanceled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission whereColonyShip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission whereCruiser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission whereCrystal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission whereDeathstar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission whereDestroyer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission whereDeuterium($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission whereEspionageProbe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission whereGalaxyTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission whereHeavyFighter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission whereLargeCargo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission whereLightFighter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission whereMetal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission whereMissionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission wherePlanetIdFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission wherePlanetIdTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission wherePositionTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission whereProcessed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission whereRecycler($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission whereSmallCargo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission whereSystemTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission whereTimeArrival($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission whereTimeDeparture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission whereUpdatedAt($value)
 * @property int|null $parent_id
 * @property int $user_id
 * @property int|null $galaxy_from
 * @property int|null $system_from
 * @property int|null $position_from
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission whereGalaxyFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission wherePositionFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission whereSystemFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FleetMission whereUserId($value)
 * @mixin \Eloquent
 */
class FleetMission extends Model
{
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
}
