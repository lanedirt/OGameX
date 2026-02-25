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
 * @property int $sender_id
 * @property int|null $recipient_id
 * @property int|null $alliance_id
 * @property string $message
 * @property int|null $reply_to_id
 * @property Carbon|null $read_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $sender
 * @property-read User|null $recipient
 * @property-read Alliance|null $alliance
 * @property-read ChatMessage|null $replyTo
 * @method static Builder|ChatMessage newModelQuery()
 * @method static Builder|ChatMessage newQuery()
 * @method static Builder|ChatMessage query()
 * @mixin \Eloquent
 */
class ChatMessage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'sender_id',
        'recipient_id',
        'alliance_id',
        'message',
        'reply_to_id',
        'read_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    /**
     * Get the sender of the message.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the recipient of the message (for direct messages).
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * Get the alliance (for alliance chat messages).
     */
    public function alliance(): BelongsTo
    {
        return $this->belongsTo(Alliance::class, 'alliance_id');
    }

    /**
     * Get the message this is a reply to.
     */
    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(ChatMessage::class, 'reply_to_id');
    }
}
