<?php

namespace OGame;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Planet extends Model
{
    use HasFactory;

    /**
     * Get the planet that owns the research queue record.
     */
    public function planet()
    {
        return $this->belongsTo('OGame\Planet');
    }
}
