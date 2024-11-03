<?php

namespace OGame\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $armor_technology
 * @property-read \OGame\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserTech newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTech newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTech query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTech whereArmorTechnology($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTech whereAstrophysics($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTech whereCombustionDrive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTech whereComputerTechnology($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTech whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTech whereEnergyTechnology($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTech whereEspionageTechnology($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTech whereGravitonTechnology($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTech whereHyperspaceDrive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTech whereHyperspaceTechnology($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTech whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTech whereImpulseDrive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTech whereIntergalacticResearchNetwork($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTech whereIonTechnology($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTech whereLaserTechnology($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTech wherePlasmaTechnology($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTech whereShieldingTechnology($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTech whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTech whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTech whereWeaponTechnology($value)
 * @method static \Database\Factories\UserTechFactory factory($count = null, $state = [])
 * @mixin \Eloquent
 */
#[ObservedBy([UserTechObserver::class])]
class UserTech extends Model
{
    use HasFactory;

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
