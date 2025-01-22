<?php

namespace OGame\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property int $galaxy
 * @property int $system
 * @property int $planet
 * @property int $planet_type
 * @property int $destroyed
 * @property int $diameter
 * @property int $field_current
 * @property int $field_max
 * @property int $temp_min
 * @property int $temp_max
 * @property float $metal
 * @property int $metal_production
 * @property float $metal_max
 * @property float $crystal
 * @property int $crystal_production
 * @property float $crystal_max
 * @property float $deuterium
 * @property int $deuterium_production
 * @property float $deuterium_max
 * @property int $energy_used
 * @property int $energy_max
 * @property int $time_last_update
 * @property int $metal_mine
 * @property int $metal_mine_percent
 * @property int $crystal_mine
 * @property int $crystal_mine_percent
 * @property int $deuterium_synthesizer
 * @property int $deuterium_synthesizer_percent
 * @property int $solar_plant
 * @property int $solar_plant_percent
 * @property int $fusion_plant
 * @property int $fusion_plant_percent
 * @property int $robot_factory
 * @property int $nano_factory
 * @property int $shipyard
 * @property int $metal_store
 * @property int $crystal_store
 * @property int $deuterium_store
 * @property int $research_lab
 * @property int $terraformer
 * @property int $alliance_depot
 * @property int $missile_silo
 * @property int $space_dock
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
 * @property int $solar_satellite
 * @property int $rocket_launcher
 * @property int $light_laser
 * @property int $heavy_laser
 * @property int $gauss_cannon
 * @property int $ion_cannon
 * @property int $plasma_turret
 * @property int $small_shield_dome
 * @property int $large_shield_dome
 * @property int $anti_ballistic_missile
 * @property int $interplanetary_missile
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Planet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Planet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Planet query()
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereAllianceDepot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereAntiBallisticMissile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereBattleShip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereBattlecruiser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereBomber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereColonyShip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereCruiser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereCrystal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereCrystalMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereCrystalMine($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereCrystalMinePercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereCrystalProduction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereCrystalStore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereDeathstar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereDestroyed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereDestroyer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereDeuterium($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereDeuteriumMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereDeuteriumProduction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereDeuteriumStore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereDeuteriumSynthesizer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereDeuteriumSynthesizerPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereDiameter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereEnergyMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereEnergyUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereEspionageProbe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereFieldCurrent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereFieldMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereFusionPlant($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereFusionPlantPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereGalaxy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereGaussCannon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereHeavyFighter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereHeavyLaser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereInterplanetaryMissile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereIonCannon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereLargeCargo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereLargeShieldDome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereLightFighter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereLightLaser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereMetal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereMetalMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereMetalMine($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereMetalMinePercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereMetalProduction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereMetalStore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereMissileSilo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereNanoFactory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet wherePlanet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet wherePlanetType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet wherePlasmaTurret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereRecycler($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereResearchLab($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereRobotFactory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereRocketLauncher($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereShipyard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereSmallCargo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereSmallShieldDome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereSolarPlant($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereSolarPlantPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereSolarSatellite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereSpaceDock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereSystem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereTempMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereTempMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereTerraformer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereTimeLastUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereUserId($value)
 * @method static \Database\Factories\PlanetFactory factory($count = null, $state = [])
 * @property int $solar_satellite_percent
 * @method static \Illuminate\Database\Eloquent\Builder|Planet whereSolarSatellitePercent($value)
 * @property int $lunar_base
 * @property int $sensor_phalanx
 * @property int $jump_gate
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Planet whereJumpGate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Planet whereLunarBase($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Planet whereSensorPhalanx($value)
 * @mixin \Eloquent
 */
class Planet extends Model
{
    use HasFactory;

    /**
     * Get the planet that owns the research queue record.
     */
    public function planet(): BelongsTo
    {
        return $this->belongsTo('OGame\Models\Planet');
    }
}
