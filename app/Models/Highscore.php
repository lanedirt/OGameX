<?php

namespace OGame\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 *
 * @property int $id
 * @property int $player_id
 * @property int $general
 * @property int $economy
 * @property int $research
 * @property int $military
 * @property-read User $player
 * @mixin \Eloquent
 */
class Highscore extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'general',
        'economy',
        'research',
        'military',
    ];

    /**
     * Get the user that owns the note record.
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
