<?php

namespace OGame\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A scheduled, module-owned item. Processing remains opt-in through the
 * module's registered ProvidesQueueProcessor implementation.
 */
class ModuleQueueItem extends Model
{
    protected $table = 'module_queue_items';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'available_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }
}
