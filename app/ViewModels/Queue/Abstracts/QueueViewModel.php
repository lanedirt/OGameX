<?php

namespace OGame\ViewModels\Queue\Abstracts;

use OGame\GameObjects\Models\Abstracts\GameObject;

class QueueViewModel
{
    /**
     * Constructor.
     *
     * @param int $id
     * @param GameObject $object
     * @param int $time_countdown
     * @param int $time_total
     */
    public function __construct(public int $id, public GameObject $object, public int $time_countdown, public int $time_total)
    {
    }
}
