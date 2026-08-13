<?php

namespace OGame\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Small, namespaced state records for module-owned settings and checkpoints.
 * Rich relational data should still live in a module's own migration/table.
 */
class ModuleState extends Model
{
    protected $table = 'module_states';

    protected $guarded = [];
}
