<?php

namespace OGame;

use Illuminate\Database\Eloquent\Model;

class UserTech extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users_tech';

    /**
     * Get the user that owns this tech record.
     */
    public function user()
    {
        return $this->belongsTo('OGame\User');
    }
}
