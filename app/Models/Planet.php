<?php

namespace OGame\Models;

use Database\Factories\PlanetFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Planet newModelQuery()
 * @method static Builder|Planet newQuery()
 * @method static Builder|Planet query()
 * @method static Builder|Planet whereAllianceDepot($value)
 * @method static Builder|Planet whereAntiBallisticMissile($value)
 * @method static Builder|Planet whereBattleShip($value)
 * @method static Builder|Planet whereBattlecruiser($value)
 * @method static Builder|Planet whereBomber($value)
 * @method static Builder|Planet whereColonyShip($value)
 * @method static Builder|Planet whereCreatedAt($value)
 * @method static Builder|Planet whereCruiser($value)
 * @method static Builder|Planet whereCrystal($value)
 * @method static Builder|Planet whereCrystalMax($value)
 * @method static Builder|Planet whereCrystalMine($value)
 * @method static Builder|Planet whereCrystalMinePercent($value)
 * @method static Builder|Planet whereCrystalProduction($value)
 * @method static Builder|Planet whereCrystalStore($value)
 * @method static Builder|Planet whereDeathstar($value)
 * @method static Builder|Planet whereDestroyed($value)
 * @method static Builder|Planet whereDestroyer($value)
 * @method static Builder|Planet whereDeuterium($value)
 * @method static Builder|Planet whereDeuteriumMax($value)
 * @method static Builder|Planet whereDeuteriumProduction($value)
 * @method static Builder|Planet whereDeuteriumStore($value)
 * @method static Builder|Planet whereDeuteriumSynthesizer($value)
 * @method static Builder|Planet whereDeuteriumSynthesizerPercent($value)
 * @method static Builder|Planet whereDiameter($value)
 * @method static Builder|Planet whereEnergyMax($value)
 * @method static Builder|Planet whereEnergyUsed($value)
 * @method static Builder|Planet whereEspionageProbe($value)
 * @method static Builder|Planet whereFieldCurrent($value)
 * @method static Builder|Planet whereFieldMax($value)
 * @method static Builder|Planet whereFusionPlant($value)
 * @method static Builder|Planet whereFusionPlantPercent($value)
 * @method static Builder|Planet whereGalaxy($value)
 * @method static Builder|Planet whereGaussCannon($value)
 * @method static Builder|Planet whereHeavyFighter($value)
 * @method static Builder|Planet whereHeavyLaser($value)
 * @method static Builder|Planet whereId($value)
 * @method static Builder|Planet whereInterplanetaryMissile($value)
 * @method static Builder|Planet whereIonCannon($value)
 * @method static Builder|Planet whereLargeCargo($value)
 * @method static Builder|Planet whereLargeShieldDome($value)
 * @method static Builder|Planet whereLightFighter($value)
 * @method static Builder|Planet whereLightLaser($value)
 * @method static Builder|Planet whereMetal($value)
 * @method static Builder|Planet whereMetalMax($value)
 * @method static Builder|Planet whereMetalMine($value)
 * @method static Builder|Planet whereMetalMinePercent($value)
 * @method static Builder|Planet whereMetalProduction($value)
 * @method static Builder|Planet whereMetalStore($value)
 * @method static Builder|Planet whereMissileSilo($value)
 * @method static Builder|Planet whereName($value)
 * @method static Builder|Planet whereNanoFactory($value)
 * @method static Builder|Planet wherePlanet($value)
 * @method static Builder|Planet wherePlanetType($value)
 * @method static Builder|Planet wherePlasmaTurret($value)
 * @method static Builder|Planet whereRecycler($value)
 * @method static Builder|Planet whereResearchLab($value)
 * @method static Builder|Planet whereRobotFactory($value)
 * @method static Builder|Planet whereRocketLauncher($value)
 * @method static Builder|Planet whereShipyard($value)
 * @method static Builder|Planet whereSmallCargo($value)
 * @method static Builder|Planet whereSmallShieldDome($value)
 * @method static Builder|Planet whereSolarPlant($value)
 * @method static Builder|Planet whereSolarPlantPercent($value)
 * @method static Builder|Planet whereSolarSatellite($value)
 * @method static Builder|Planet whereSpaceDock($value)
 * @method static Builder|Planet whereSystem($value)
 * @method static Builder|Planet whereTempMax($value)
 * @method static Builder|Planet whereTempMin($value)
 * @method static Builder|Planet whereTerraformer($value)
 * @method static Builder|Planet whereTimeLastUpdate($value)
 * @method static Builder|Planet whereUpdatedAt($value)
 * @method static Builder|Planet whereUserId($value)
 * @method static PlanetFactory factory($count = null, $state = [])
 * @property int $solar_satellite_percent
 * @method static Builder|Planet whereSolarSatellitePercent($value)
 * @property int $lunar_base
 * @property int $sensor_phalanx
 * @property int $jump_gate
 * @property int|null $jump_gate_cooldown
 * @property int|null $default_jump_gate_target_id
 * @method static Builder<static>|Planet whereJumpGate($value)
 * @method static Builder<static>|Planet whereLunarBase($value)
 * @method static Builder<static>|Planet whereSensorPhalanx($value)
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
