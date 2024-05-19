<?php

namespace OGame\ViewModels\Queue;

use OGame\ViewModels\Queue\Abstracts\QueueListViewModel;

class BuildingQueueListViewModel extends QueueListViewModel
{
    /**
     * List of queue items.
     *
     * @var array<BuildingQueueViewModel>
     */
    public array $queue;

    /**
     * Constructor.
     *
     * @param array<BuildingQueueViewModel> $queue
     */
    public function __construct(array $queue)
    {
        $this->queue = $queue;
    }

    /**
     * Returns the item in the queue that is currently building.
     *
     * @return ?BuildingQueueViewModel
     */
    public function getCurrentlyBuildingFromQueue(): ?BuildingQueueViewModel
    {
        foreach ($this->queue as $record) {
            if ((int)$record->building === 1) {
                return $record;
            }
        }

        return null;
    }

    /**
     * Returns the items in the queue that are queued.
     *
     * @return array<BuildingQueueViewModel>
     */
    public function getQueuedFromQueue(): array
    {
        $queued = [];
        foreach ($this->queue as $record) {
            if ((int)$record->building === 0) {
                $queued[] = $record;
            }
        }

        return $queued;
    }
}
