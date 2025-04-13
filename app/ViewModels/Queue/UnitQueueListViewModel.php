<?php

namespace OGame\ViewModels\Queue;

use OGame\ViewModels\Queue\Abstracts\QueueListViewModel;

class UnitQueueListViewModel extends QueueListViewModel
{
    /**
     * List of queue items.
     *
     * @var array<UnitQueueViewModel>
     */
    public array $queue;

    /**
     * Returns the item in the queue that is currently building.
     *
     * @return UnitQueueViewModel|null
     */
    public function getCurrentlyBuildingFromQueue(): UnitQueueViewModel|null
    {
        // Return the first item in the queue if exists.
        if (count($this->queue) > 0) {
            return $this->queue[0];
        }

        return null;
    }

    /**
     * Returns the items in the queue that are queued.
     *
     * @return array<UnitQueueViewModel>
     */
    public function getQueuedFromQueue(): array
    {
        $queued = [];
        // Everything except the first
        for ($i = 1, $iMax = count($this->queue); $i < $iMax; $i++) {
            $queued[] = $this->queue[$i];
        }

        return $queued;
    }
}
