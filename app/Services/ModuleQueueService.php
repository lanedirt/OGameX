<?php

namespace OGame\Services;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
use OGame\Models\ModuleQueueItem;
use OGame\Models\Planet;

/**
 * Storage for scheduled module work. Modules opt into execution by registering
 * a ProvidesQueueProcessor and consuming their due items from that processor.
 */
class ModuleQueueService
{
    public function enqueueForPlanet(
        string $alias,
        string $queue,
        Planet|int $planet,
        array $payload,
        CarbonInterface $availableAt,
    ): ModuleQueueItem {
        return ModuleQueueItem::create([
            'module_alias' => $alias,
            'scope' => 'planet',
            'owner_id' => $planet instanceof Planet ? $planet->id : $planet,
            'queue' => $queue,
            'payload' => $payload,
            'available_at' => $availableAt,
        ]);
    }

    /**
     * Retrieve due, incomplete work inside the caller's transaction. Locking is
     * intentional: a queue processor should mark each item complete before its
     * transaction commits.
     *
     * @return Collection<int, ModuleQueueItem>
     */
    public function dueForPlanet(string $alias, string $queue, Planet|int $planet): Collection
    {
        return ModuleQueueItem::query()
            ->where([
                'module_alias' => $alias,
                'scope' => 'planet',
                'owner_id' => $planet instanceof Planet ? $planet->id : $planet,
                'queue' => $queue,
            ])
            ->whereNull('completed_at')
            ->where('available_at', '<=', now())
            ->orderBy('id')
            ->lockForUpdate()
            ->get();
    }

    public function complete(ModuleQueueItem $item): void
    {
        $item->forceFill(['completed_at' => now()])->save();
    }
}
