<?php

namespace OGame\Models;

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
 * @property int $ignored_user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @property-read User $ignoredUser
 * @method static Builder|IgnoredPlayer newModelQuery()
 * @method static Builder|IgnoredPlayer newQuery()
 * @method static Builder|IgnoredPlayer query()
 * @method static Builder|IgnoredPlayer whereId($value)
 * @method static Builder|IgnoredPlayer whereUserId($value)
 * @method static Builder|IgnoredPlayer whereIgnoredUserId($value)
 * @method static Builder|IgnoredPlayer whereCreatedAt($value)
 * @method static Builder|IgnoredPlayer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class IgnoredPlayer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'ignored_user_id',
    ];

    /**
     * Get the user who is ignoring.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who is being ignored.
     */
    public function ignoredUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ignored_user_id');
    }
}
