<?php

namespace OGame\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Persisted level or amount for a game object contributed by a module.
 *
 * Core objects continue to use their established columns. Module rows are
 * deliberately namespaced so installing a module never changes planets or
 * users_tech.
 */
class ModuleObjectState extends Model
{
    protected $table = 'module_object_states';

    protected $guarded = [];
}
