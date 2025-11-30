<?php

namespace OGame\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $amount
 * @property string $type
 * @property string $description
 * @property int $balance_after
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \OGame\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|DarkMatterTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DarkMatterTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DarkMatterTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|DarkMatterTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DarkMatterTransaction whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DarkMatterTransaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DarkMatterTransaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DarkMatterTransaction whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DarkMatterTransaction whereBalanceAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DarkMatterTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DarkMatterTransaction ofType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder|DarkMatterTransaction forUser(int $userId)
 * @mixin \Eloquent
 */
class DarkMatterTransaction extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'description',
        'balance_after',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Get the user that owns the transaction.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include transactions of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include transactions for a given user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
