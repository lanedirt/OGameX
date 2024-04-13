<?php

namespace OGame\ViewModels\Queue\Abstracts;

class QueueListViewModel
{
    /**
     * List of queue items.
     *
     * @var array QueueViewModel[]
     */
    public array $queue;

    /**
     * Constructor.
     *
     * @param array<QueueViewModel> $queue
     */
    public function __construct(array $queue)
    {
        $this->queue = $queue;
    }

    /**
     * Returns the item in the queue that is currently building.
     *
     * @return QueueViewModel|null
     */
    public function getCurrentlyBuildingFromQueue() : ?QueueViewModel
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
     * @return array<QueueViewModel>
     */
    public function getQueuedFromQueue() : array
    {
        $queued = [];
        foreach ($this->queue as $record) {
            if ($record->building == 0) {
                $queued[] = $record;
            }
        }

        return $queued;
    }

    /**
     * Get amount of items in the queue.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->queue);
    }

    /**
     * Get amount of items in the queue.
     *
     * @return int
     */
    public function isQueueFull(): int
    {
        // Max items in queue is 4.
        // TODO: refactor into global/constant setting configurable by admin.
        $maxItemsInQueue = 4;
        return count($this->queue) >= $maxItemsInQueue;
    }
}