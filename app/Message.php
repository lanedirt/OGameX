<?php

namespace OGame;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    /**
     * Get the user that owns the research queue record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo('OGame\User');
    }
}
