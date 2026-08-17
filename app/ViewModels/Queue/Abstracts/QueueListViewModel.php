<?php

namespace OGame\ViewModels\Queue\Abstracts;

class QueueListViewModel
{
    /**
     * Default max items in the queue: 1 currently building + 4 in queue = 5.
     * TODO: refactor into global/constant setting configurable by admin.
     */
    public const DEFAULT_MAX_ITEMS_IN_QUEUE = 5;

    /**
     * Constructor.
     *
     * @param array<QueueViewModel> $queue
     * @param int $maxItemsInQueue Max amount of items (including the currently building one) allowed in this queue.
     */
    public function __construct(
        /**
         * List of queue items.
         */
        public array $queue,
        /**
         * Max amount of items allowed in this queue, including the currently building one.
         */
        public int $maxItemsInQueue = self::DEFAULT_MAX_ITEMS_IN_QUEUE
    ) {
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
     * @return bool
     */
    public function isQueueFull(): bool
    {
        return count($this->queue) >= $this->maxItemsInQueue;
    }
}
