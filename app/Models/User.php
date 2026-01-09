<?php

namespace OGame\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use OGame\Enums\CharacterClass;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

/**
 *
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $two_factor_confirmed_at
 * @property string $lang
 * @property string|null $last_ip
 * @property string|null $time
 * @property string|null $register_ip
 * @property string|null $register_time
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $planet_current
 * @property int $dark_matter
 * @property Carbon|null $dark_matter_last_regen
 * @property bool $vacation_mode
 * @property Carbon|null $vacation_mode_activated_at
 * @property Carbon|null $vacation_mode_until
 * @property int|null $character_class
 * @property bool $character_class_free_used
 * @property Carbon|null $character_class_changed_at
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read UserTech|null $tech
 * @method static UserFactory factory($count = null, $state = [])
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereLang($value)
 * @method static Builder|User whereLastIp($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User wherePlanetCurrent($value)
 * @method static Builder|User whereRegisterIp($value)
 * @method static Builder|User whereRegisterTime($value)
 * @method static Builder|User whereTime($value)
 * @method static Builder|User whereTwoFactorConfirmedAt($value)
 * @method static Builder|User whereTwoFactorRecoveryCodes($value)
 * @method static Builder|User whereTwoFactorSecret($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User whereUsername($value)
 * @property-read Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read Collection<int, Role> $roles
 * @property-read int|null $roles_count
 * @method static Builder|User permission($permissions, $without = false)
 * @method static Builder|User role($roles, $guard = null, $without = false)
 * @method static Builder|User withoutPermission($permissions)
 * @method static Builder|User withoutRole($roles, $guard = null)
 * @property string|null $username_updated_at
 * @method static Builder|User whereUsernameUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use Notifiable;
    use HasFactory;
    use HasRoles;

    /**
     * Disable use of default "remember_token" laravel behavior.
     * @var bool
     */
    public $remember_token = false;

    /**
     * Boot method to attach model events.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Automatically assign admin role to the first non-Legor user
        static::created(function (User $user) {
            // Skip Legor
            if ($user->username === 'Legor') {
                return;
            }

            // Check if this is the first non-Legor user
            $nonLegorUserCount = User::where('username', '!=', 'Legor')->where('id', '!=', $user->id)->count();

            if ($nonLegorUserCount === 0) {
                // This is the first real user - assign admin role and rename to Admin
                $user->assignRole('admin');
                $user->username = 'Admin';
                $user->save();
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username', 'email', 'password', 'lang', 'espionage_probes_amount',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'vacation_mode' => 'boolean',
        'vacation_mode_activated_at' => 'datetime',
        'vacation_mode_until' => 'datetime',
        'dark_matter_last_regen' => 'datetime',
        'character_class_free_used' => 'boolean',
        'character_class_changed_at' => 'datetime',
        'alliance_left_at' => 'datetime',
    ];

    /**
     * Get the user tech record associated with the user.
     *
     * @return HasOne
     */
    public function tech(): HasOne
    {
        return $this->hasOne(UserTech::class);
    }

    /**
     * Get the dark matter transactions for the user.
     *
     * @return HasMany
     */
    public function darkMatterTransactions()
    {
        return $this->hasMany(DarkMatterTransaction::class);
    }

    /**
     * Get the highscore record associated with the user.
     *
     * @return HasOne
     */
    public function highscore(): HasOne
    {
        return $this->hasOne(Highscore::class, 'player_id');
    }

    /**
     * Get the alliance that the user belongs to.
     *
     * @return BelongsTo
     */
    public function alliance(): BelongsTo
    {
        return $this->belongsTo(Alliance::class, 'alliance_id');
    }

    /**
     * Check if the user is currently online.
     * A user is considered online if they were active within the last 15 minutes.
     *
     * @return bool
     */
    public function isOnline(): bool
    {
        if (!$this->time) {
            return false;
        }

        // User is online if last activity was within 15 minutes (900 seconds)
        $lastActivity = (int)$this->time;
        $currentTime = time();
        $timeDifference = $currentTime - $lastActivity;

        return $timeDifference <= 900; // 15 minutes
    }

    /**
     * Get the user's character class as an enum.
     *
     * @return CharacterClass|null
     */
    public function getCharacterClassEnum(): CharacterClass|null
    {
        if ($this->character_class === null) {
            return null;
        }

        return CharacterClass::tryFrom($this->character_class);
    }

    /**
     * Check if user is a Collector.
     *
     * @return bool
     */
    public function isCollector(): bool
    {
        return $this->character_class === CharacterClass::COLLECTOR->value;
    }

    /**
     * Check if user is a General.
     *
     * @return bool
     */
    public function isGeneral(): bool
    {
        return $this->character_class === CharacterClass::GENERAL->value;
    }

    /**
     * Check if user is a Discoverer.
     *
     * @return bool
     */
    public function isDiscoverer(): bool
    {
        return $this->character_class === CharacterClass::DISCOVERER->value;
    }

    /**
     * Check if user has a character class selected.
     *
     * @return bool
     */
    public function hasCharacterClass(): bool
    {
        return $this->character_class !== null;
    }
}
