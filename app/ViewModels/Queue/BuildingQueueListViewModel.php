<?php

namespace OGame\ViewModels\Queue;

class BuildingQueueListViewModel extends QueueListViewModel
{
    /**
     * List of queue items.
     *
     * @var array UnitQueueViewModel[]
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
     * @return BuildingQueueViewModel|null
     */
    public function getCurrentlyBuildingFromQueue() : ?BuildingQueueViewModel
    {
        foreach ($this->queue as $record) {
            if ($record->building == 1) {
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
    public function getQueuedFromQueue() : array
    {
        return parent::getQueuedFromQueue();
    }
}