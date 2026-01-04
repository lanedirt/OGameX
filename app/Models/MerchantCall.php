<?php

namespace OGame\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class MerchantCall
 *
 * @property int $id
 * @property int $user_id
 * @property string $merchant_type
 * @property int $planet_id
 * @property string $called_at
 * @property string $week_identifier
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @property-read Planet $planet
 * @mixin \Eloquent
 */
class MerchantCall extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'merchant_type',
        'planet_id',
        'called_at',
        'week_identifier',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'called_at' => 'datetime',
    ];

    /**
     * Get the user that made the merchant call.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the planet where the merchant was called.
     */
    public function planet(): BelongsTo
    {
        return $this->belongsTo(Planet::class);
    }
}
