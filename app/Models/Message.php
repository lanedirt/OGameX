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
 * @property int $type
 * @property string $subject
 * @property int|null $action_planet_id
 * @property int|null $sender_user_id
 * @property string $body
 * @property int $viewed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @method static Builder|Message newModelQuery()
 * @method static Builder|Message newQuery()
 * @method static Builder|Message query()
 * @method static Builder|Message whereActionPlanetId($value)
 * @method static Builder|Message whereBody($value)
 * @method static Builder|Message whereCreatedAt($value)
 * @method static Builder|Message whereId($value)
 * @method static Builder|Message whereSenderUserId($value)
 * @method static Builder|Message whereSubject($value)
 * @method static Builder|Message whereType($value)
 * @method static Builder|Message whereUpdatedAt($value)
 * @method static Builder|Message whereUserId($value)
 * @method static Builder|Message whereViewed($value)
 * @property array<string, string> $params
 * @method static Builder|Message whereParams($value)
 * @property string $key
 * @method static Builder|Message whereKey($value)
 * @property int|null $espionage_report_id
 * @method static Builder|Message whereEspionageReportId($value)
 * @property int|null $battle_report_id
 * @method static Builder|Message whereBattleReportId($value)
 * @mixin \Eloquent
 */
class Message extends Model
{
    use HasFactory;

    /**
     * Treat the params column as an array so its contents get stored/retrieved as JSON.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'params' => 'array',
    ];

    /**
     * Get the user that owns the research queue record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
