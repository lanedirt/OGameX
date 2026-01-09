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
 * @property int|null $planet_user_id
 * @property array<string, mixed>|null $general
 * @property array<string, mixed>|null $attacker
 * @property array<string, mixed>|null $defender
 * @property array<mixed>|null $rounds
 * @property array<string, mixed>|null $loot
 * @property array<string, mixed>|null $debris
 * @property array<string, mixed>|null $repaired_defenses
 * @property array<string, mixed>|null $wreckage
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $planetUserId
 * @method static Builder|BattleReport newModelQuery()
 * @method static Builder|BattleReport newQuery()
 * @method static Builder|BattleReport query()
 * @method static Builder|BattleReport whereAttacker($value)
 * @method static Builder|BattleReport whereCreatedAt($value)
 * @method static Builder|BattleReport whereDebris($value)
 * @method static Builder|BattleReport whereDefender($value)
 * @method static Builder|BattleReport whereGeneral($value)
 * @method static Builder|BattleReport whereId($value)
 * @method static Builder|BattleReport whereLoot($value)
 * @method static Builder|BattleReport wherePlanetGalaxy($value)
 * @method static Builder|BattleReport wherePlanetPosition($value)
 * @method static Builder|BattleReport wherePlanetSystem($value)
 * @method static Builder|BattleReport wherePlanetUserId($value)
 * @method static Builder|BattleReport whereRepairedDefenses($value)
 * @method static Builder|BattleReport whereRounds($value)
 * @method static Builder|BattleReport whereUpdatedAt($value)
 * @method static Builder|BattleReport whereWreckage($value)
 * @property int $planet_type
 * @method static Builder<static>|BattleReport wherePlanetType($value)
 * @mixin \Eloquent
 */
class BattleReport extends Model
{
    protected $casts = [
        'general' => 'array',
        'attacker' => 'array',
        'defender' => 'array',
        'rounds' => 'array',
        'loot' => 'array',
        'debris' => 'array',
        'repaired_defenses' => 'array',
        'wreckage' => 'array',
    ];

    /**
     * Get the player that owns the planet that this battle report is about.
     */
    public function planetUserId(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
