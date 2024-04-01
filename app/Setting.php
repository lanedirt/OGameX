<?php

namespace OGame;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * Setting table has custom primary key (not "id")
     *
     * @var string
     */
    protected $primaryKey = 'key';

    /**
     * Setting table has custom primary key (that does not increment).
     *
     * @var bool
     */
    public $incrementing = false;

    protected $fillable = ['key', 'value'];
}
