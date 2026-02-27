<?php

namespace OGame\ViewModels\Queue;

use OGame\GameObjects\Models\Abstracts\GameObject;
use OGame\ViewModels\Queue\Abstracts\QueueViewModel;

class UnitQueueViewModel extends QueueViewModel
{
    /**
     * Constructor.
     *
     * @param int $id
     * @param GameObject $object
     * @param int $time_countdown
     * @param int $time_total
     * @param int $object_amount
     * @param int $object_amount_remaining
     * @param int $time_countdown_object_next
     * @param int $time_countdown_per_object
     */
    public function __construct(
        int $id,
        GameObject $object,
        int $time_countdown,
        int $time_total,
        public int $object_amount,
        public int $object_amount_remaining,
        public int $time_countdown_object_next,
        public int $time_countdown_per_object,
        public bool $dm_halved = false,
    ) {
        parent::__construct($id, $object, $time_countdown, $time_total);
    }
}
