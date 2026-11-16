<?php

namespace OGame\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * NPC empire state. One row per AI-controlled empire (user), holding its
 * randomized personality traits and AI decision memory.
 *
 * @property int $id
 * @property int $user_id
 * @property float $development_speed
 * @property float $aggression
 * @property float $expansion_rate
 * @property array<string, mixed>|null $ai_state
 * @property Carbon|null $last_ai_decision_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @method static Builder|NpcEmpire newModelQuery()
 * @method static Builder|NpcEmpire newQuery()
 * @method static Builder|NpcEmpire query()
 * @method static Builder|NpcEmpire whereUserId($value)
 * @mixin \Eloquent
 */
#[Fillable([
    'user_id', 'development_speed', 'aggression', 'expansion_rate', 'ai_state', 'last_ai_decision_at',
])]
class NpcEmpire extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'development_speed'   => 'float',
            'aggression'          => 'float',
            'expansion_rate'      => 'float',
            'ai_state'            => 'array',
            'last_ai_decision_at' => 'datetime',
        ];
    }

    /**
     * Get the user account that this empire controls.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
