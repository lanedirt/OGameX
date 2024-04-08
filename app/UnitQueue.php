<?php

namespace OGame;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitQueue extends Model
{
    /**
     * Get the planet that owns the research queue record.
     */
    public function planet(): BelongsTo
    {
        return $this->belongsTo('OGame\Planet');
    }
}
