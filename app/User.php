<?php

namespace OGame;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * Disable use of default "remember_token" laravel behavior.
     * @var bool
     */
    public $remember_token = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'lang',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the user tech record associated with the user.
     */
    public function tech()
    {
        return $this->hasOne('OGame\UserTech');
    }
}
