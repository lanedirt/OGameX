<?php

namespace OGame;

use Illuminate\Database\Eloquent\Model;

class ResearchQueue extends Model
{
    /**
     * Get the planet that owns the research queue record.
     */
    public function planet()
    {
        return $this->belongsTo('OGame\Planet');
    }
}
