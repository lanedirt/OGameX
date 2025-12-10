<?php

namespace OGame\ViewModels\Queue;

use OGame\GameObjects\Models\Abstracts\GameObject;
use OGame\ViewModels\Queue\Abstracts\QueueViewModel;

class BuildingQueueViewModel extends QueueViewModel
{
    public bool $building;
    public int $level_target;
    public bool $is_downgrade;

    /**
     * Constructor.
     *
     * @param int $id
     * @param GameObject $object
     * @param int $time_countdown
     * @param int $time_total
     * @param bool $building
     * @param int $level_target
     * @param bool $is_downgrade
     */
    public function __construct(
        int $id,
        GameObject $object,
        int $time_countdown,
        int $time_total,
        bool $building,
        int $level_target,
        bool $is_downgrade = false
    ) {
        $this->building = $building;
        $this->level_target = $level_target;
        $this->is_downgrade = $is_downgrade;

        parent::__construct($id, $object, $time_countdown, $time_total);
    }
}
