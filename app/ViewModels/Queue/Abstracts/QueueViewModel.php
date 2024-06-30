<?php

namespace OGame\ViewModels\Queue\Abstracts;

use OGame\GameObjects\Models\Abstracts\GameObject;

class QueueViewModel
{
    public int $id;
    public GameObject $object;
    public int $time_countdown;
    public int $time_total;

    /**
     * Constructor.
     *
     * @param int $id
     * @param GameObject $object
     * @param int $time_countdown
     * @param int $time_total
     */
    public function __construct(int $id, GameObject $object, int $time_countdown, int $time_total)
    {
        $this->id = $id;
        $this->object = $object;
        $this->time_countdown = $time_countdown;
        $this->time_total = $time_total;
    }
}
