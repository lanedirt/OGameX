<?php

namespace OGame\ViewModels\Queue;

class ResearchQueueListViewModel extends QueueListViewModel
{
    /**
     * List of queue items.
     *
     * @var array ResearchQueueViewModel[]
     */
    public array $queue;

    /**
     * Constructor.
     *
     * @param array<ResearchQueueViewModel> $queue
     */
    public function __construct(array $queue)
    {
        $this->queue = $queue;
    }

    /**
     * Returns the item in the queue that is currently building.
     *
     * @return ResearchQueueViewModel|null
     */
    public function getCurrentlyBuildingFromQueue() : ?ResearchQueueViewModel
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
     * @return array<ResearchQueueViewModel>
     */
    public function getQueuedFromQueue() : array
    {
        return parent::getQueuedFromQueue();
    }
}