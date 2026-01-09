<?php

namespace OGame\Models;

use Database\Factories\UserTechFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use OGame\Observers\UserTechObserver;

/**
 *
 *
 * @property int $id
 * @property int $user_id
 * @property int $energy_technology
 * @property int $laser_technology
 * @property int $ion_technology
 * @property int $hyperspace_technology
 * @property int $plasma_technology
 * @property int $combustion_drive
 * @property int $impulse_drive
 * @property int $hyperspace_drive
 * @property int $espionage_technology
 * @property int $computer_technology
 * @property int $astrophysics
 * @property int $intergalactic_research_network
 * @property int $graviton_technology
 * @property int $weapon_technology
 * @property int $shielding_technology
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $armor_technology
 * @property-read User $user
 * @method static Builder|UserTech newModelQuery()
 * @method static Builder|UserTech newQuery()
 * @method static Builder|UserTech query()
 * @method static Builder|UserTech whereArmorTechnology($value)
 * @method static Builder|UserTech whereAstrophysics($value)
 * @method static Builder|UserTech whereCombustionDrive($value)
 * @method static Builder|UserTech whereComputerTechnology($value)
 * @method static Builder|UserTech whereCreatedAt($value)
 * @method static Builder|UserTech whereEnergyTechnology($value)
 * @method static Builder|UserTech whereEspionageTechnology($value)
 * @method static Builder|UserTech whereGravitonTechnology($value)
 * @method static Builder|UserTech whereHyperspaceDrive($value)
 * @method static Builder|UserTech whereHyperspaceTechnology($value)
 * @method static Builder|UserTech whereId($value)
 * @method static Builder|UserTech whereImpulseDrive($value)
 * @method static Builder|UserTech whereIntergalacticResearchNetwork($value)
 * @method static Builder|UserTech whereIonTechnology($value)
 * @method static Builder|UserTech whereLaserTechnology($value)
 * @method static Builder|UserTech wherePlasmaTechnology($value)
 * @method static Builder|UserTech whereShieldingTechnology($value)
 * @method static Builder|UserTech whereUpdatedAt($value)
 * @method static Builder|UserTech whereUserId($value)
 * @method static Builder|UserTech whereWeaponTechnology($value)
 * @method static UserTechFactory factory($count = null, $state = [])
 * @mixin \Eloquent
 */
#[ObservedBy([UserTechObserver::class])]
class UserTech extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users_tech';

    /**
     * Get the user that owns this tech record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
