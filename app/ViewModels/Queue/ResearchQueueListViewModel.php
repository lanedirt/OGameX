<?php

namespace OGame\ViewModels\Queue;

use OGame\ViewModels\Queue\Abstracts\QueueListViewModel;

class ResearchQueueListViewModel extends QueueListViewModel
{
    /**
     * List of queue items.
     *
     * @var array<ResearchQueueViewModel>
     */
    public array $queue;

    /**
     * Returns the item in the queue that is currently building.
     *
     * @return ResearchQueueViewModel|null
     */
    public function getCurrentlyBuildingFromQueue(): ResearchQueueViewModel|null
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
     * @return array<ResearchQueueViewModel>
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
