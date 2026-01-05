<?php

namespace OGame\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
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
 * @property Carbon $created_at
 * @property-read User $user
 * @method static Builder|DarkMatterTransaction newModelQuery()
 * @method static Builder|DarkMatterTransaction newQuery()
 * @method static Builder|DarkMatterTransaction query()
 * @method static Builder|DarkMatterTransaction whereId($value)
 * @method static Builder|DarkMatterTransaction whereUserId($value)
 * @method static Builder|DarkMatterTransaction whereAmount($value)
 * @method static Builder|DarkMatterTransaction whereType($value)
 * @method static Builder|DarkMatterTransaction whereDescription($value)
 * @method static Builder|DarkMatterTransaction whereBalanceAfter($value)
 * @method static Builder|DarkMatterTransaction whereCreatedAt($value)
 * @method static Builder|DarkMatterTransaction ofType(string $type)
 * @method static Builder|DarkMatterTransaction forUser(int $userId)
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
     * @param Builder $query
     * @param string $type
     * @return Builder
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include transactions for a given user.
     *
     * @param Builder $query
     * @param int $userId
     * @return Builder
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
