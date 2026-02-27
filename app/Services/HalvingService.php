<?php

namespace OGame\Services;

use Exception;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use OGame\Enums\DarkMatterTransactionType;
use OGame\Models\BuildingQueue;
use OGame\Models\ResearchQueue;
use OGame\Models\UnitQueue;
use OGame\Models\User;

/**
 * Class HalvingService.
 *
 * Primary service for all Dark Matter halving operations.
 * Allows players to spend Dark Matter to reduce construction time by 50%.
 *
 * @package OGame\Services
 */
class HalvingService
{
    private const MIN_COST = 750;
    private const MAX_COST_BUILDING = 72000;
    private const MAX_COST_RESEARCH = 108000;
    private const MAX_COST_UNIT = 72000;
    private const COST_PER_30_MINUTES = 750;

    /**
     * HalvingService constructor.
     */
    public function __construct(
        private DarkMatterTransactionService $transactionService
    ) {
    }

    /**
     * Calculate halving cost based on REMAINING time.
     *
     * Formula: ceiling((remaining_time_in_minutes / 30) * 750) DM
     * With min/max caps applied based on queue type.
     *
     * @param int $remainingTimeSeconds Remaining construction time in seconds
     * @param string $queueType 'building', 'research', or 'unit'
     * @return int Cost in Dark Matter
     */
    public function calculateHalvingCost(int $remainingTimeSeconds, string $queueType): int
    {
        // Convert seconds to minutes
        $remainingTimeMinutes = $remainingTimeSeconds / 60;

        // Apply formula: ceiling((remaining_time_in_minutes / 30) * 750)
        $cost = (int)ceil(($remainingTimeMinutes / 30) * self::COST_PER_30_MINUTES);

        // Apply minimum cost floor
        $cost = max(self::MIN_COST, $cost);

        // Apply maximum cost cap based on queue type
        $maxCost = match ($queueType) {
            'building' => self::MAX_COST_BUILDING,
            'research' => self::MAX_COST_RESEARCH,
            'unit' => self::MAX_COST_UNIT,
            default => self::MAX_COST_BUILDING,
        };

        return min($cost, $maxCost);
    }

    /**
     * Get current timestamp as integer.
     */
    private function getCurrentTimestamp(): int
    {
        return (int)Date::now()->timestamp;
    }

    /**
     * Get maximum time reduction for a queue type.
     * @param string $queueType 'building', 'research', or 'unit'
     * @return int Maximum reduction in seconds
     */
    private function getMaxReduction(string $queueType): int
    {
        // Max reduction = (max_cost / 750) * 30 minutes * 60 seconds
        return match ($queueType) {
            'building' => 172800, // 48 hours
            'research' => 259200, // 72 hours
            'unit' => 172800,     // 48 hours
            default => 172800,
        };
    }

    /**
     * Calculate new time values after halving.
     *
     * Halving reduces the ORIGINAL construction time by 50%, but capped at max reduction.
     * Cost is calculated based on REMAINING time (with caps).
     *
     * @param int $timeEnd Current time_end value
     * @param int $timeDuration Original construction time in seconds
     * @param string $queueType 'building', 'research', or 'unit'
     * @return array{remaining_time: int, new_time_end: int, cost: int}
     * @throws Exception If queue item already completed
     */
    private function calculateNewTimeValues(int $timeEnd, int $timeDuration, string $queueType): array
    {
        $currentTime = $this->getCurrentTimestamp();
        $remainingTime = $timeEnd - $currentTime;

        if ($remainingTime <= 0) {
            throw new Exception('Queue item already completed');
        }

        // Calculate cost based on REMAINING time
        $cost = $this->calculateHalvingCost($remainingTime, $queueType);

        // Calculate time reduction: 50% of ORIGINAL construction time, capped at max reduction
        // Use intdiv for exact 50% (no rounding)
        $halfOriginal = intdiv($timeDuration, 2);
        $maxReduction = $this->getMaxReduction($queueType);
        $reduction = min($halfOriginal, $maxReduction, $remainingTime);

        // Calculate new remaining time
        $newRemainingTime = $remainingTime - $reduction;

        // Handle edge case: if new remaining time is <= 0, set to 0 (instant completion)
        if ($newRemainingTime < 0) {
            $newRemainingTime = 0;
        }

        $newTimeEnd = $currentTime + $newRemainingTime;

        return [
            'remaining_time' => $newRemainingTime,
            'new_time_end' => $newTimeEnd,
            'cost' => $cost,
        ];
    }

    /**
     * Halve a building queue item.
     *
     * @param User $user The user performing the halving
     * @param int $queueItemId The building queue item ID
     * @param PlanetService $planet The planet service
     * @return array{success: bool, new_time_end: int, cost: int, new_balance: int, remaining_time: int}
     * @throws Exception If insufficient Dark Matter or invalid queue item
     */
    public function halveBuilding(User $user, int $queueItemId, PlanetService $planet): array
    {
        /** @var array{success: bool, new_time_end: int, cost: int, new_balance: int, remaining_time: int} $result */
        $result = DB::transaction(function () use ($user, $queueItemId, $planet) {
            // Lock user row for Dark Matter balance
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
            if (!$lockedUser) {
                throw new Exception('User not found');
            }

            // Lock and retrieve queue item
            $queueItem = BuildingQueue::where('id', $queueItemId)
                ->where('planet_id', $planet->getPlanetId())
                ->where('processed', 0)
                ->where('canceled', 0)
                ->where('building', 1)
                ->lockForUpdate()
                ->first();

            if (!$queueItem) {
                throw new Exception('Queue item not found or already completed');
            }

            // Calculate new time values
            // Cost is based on remaining time, reduction is 50% of original time
            $timeValues = $this->calculateNewTimeValues(
                (int)$queueItem->time_end,
                (int)$queueItem->time_duration,
                'building'
            );
            $cost = $timeValues['cost'];

            // Check balance
            if ($lockedUser->dark_matter < $cost) {
                throw new Exception("Insufficient Dark Matter. Required: {$cost}, Available: {$lockedUser->dark_matter}");
            }

            // Debit Dark Matter
            $lockedUser->dark_matter -= $cost;
            $lockedUser->save();

            // Record transaction
            $object = ObjectService::getObjectById($queueItem->object_id);
            $description = "Halving building: {$object->title} on planet {$planet->getPlanetName()} (ID: {$planet->getPlanetId()})";
            $this->transactionService->recordTransaction(
                $lockedUser,
                -$cost,
                DarkMatterTransactionType::HALVING->value,
                $description,
                $lockedUser->dark_matter
            );

            // Update queue item time_end
            $queueItem->time_end = $timeValues['new_time_end'];
            $queueItem->save();

            return [
                'success' => true,
                'new_time_end' => $timeValues['new_time_end'],
                'cost' => $cost,
                'new_balance' => $lockedUser->dark_matter,
                'remaining_time' => $timeValues['remaining_time'],
            ];
        });

        return $result;
    }

    /**
     * Halve a research queue item.
     *
     * @param User $user The user performing the halving
     * @param int $queueItemId The research queue item ID
     * @param PlayerService $player The player service
     * @return array{success: bool, new_time_end: int, cost: int, new_balance: int, remaining_time: int}
     * @throws Exception If insufficient Dark Matter or invalid queue item
     */
    public function halveResearch(User $user, int $queueItemId, PlayerService $player): array
    {
        /** @var array{success: bool, new_time_end: int, cost: int, new_balance: int, remaining_time: int} $result */
        $result = DB::transaction(function () use ($user, $queueItemId, $player) {
            // Lock user row for Dark Matter balance
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
            if (!$lockedUser) {
                throw new Exception('User not found');
            }

            // Lock and retrieve queue item
            $queueItem = ResearchQueue::query()
                ->join('planets', 'research_queues.planet_id', '=', 'planets.id')
                ->where('research_queues.id', $queueItemId)
                ->where('planets.user_id', $lockedUser->id)
                ->where('research_queues.processed', 0)
                ->where('research_queues.canceled', 0)
                ->where('research_queues.building', 1)
                ->select('research_queues.*')
                ->lockForUpdate()
                ->first();

            if (!$queueItem) {
                throw new Exception('Queue item not found or already completed');
            }

            // Calculate new time values
            // Cost is based on remaining time, reduction is 50% of original time
            $timeValues = $this->calculateNewTimeValues(
                (int)$queueItem->time_end,
                (int)$queueItem->time_duration,
                'research'
            );
            $cost = $timeValues['cost'];

            // Check balance
            if ($lockedUser->dark_matter < $cost) {
                throw new Exception("Insufficient Dark Matter. Required: {$cost}, Available: {$lockedUser->dark_matter}");
            }

            // Debit Dark Matter
            $lockedUser->dark_matter -= $cost;
            $lockedUser->save();

            // Record transaction
            $object = ObjectService::getResearchObjectById($queueItem->object_id);
            $planet = $player->planets->getById((int)$queueItem->planet_id);
            $description = "Halving research: {$object->title} on planet {$planet->getPlanetName()} (ID: {$queueItem->planet_id})";
            $this->transactionService->recordTransaction(
                $lockedUser,
                -$cost,
                DarkMatterTransactionType::HALVING->value,
                $description,
                $lockedUser->dark_matter
            );

            // Update queue item time_end
            $queueItemModel = ResearchQueue::find($queueItemId);
            if ($queueItemModel) {
                $queueItemModel->time_end = $timeValues['new_time_end'];
                $queueItemModel->save();
            }

            return [
                'success' => true,
                'new_time_end' => $timeValues['new_time_end'],
                'cost' => $cost,
                'new_balance' => $lockedUser->dark_matter,
                'remaining_time' => $timeValues['remaining_time'],
            ];
        });

        return $result;
    }

    /**
     * Halve a unit queue item.
     *
     * Halving removes 50% of the original construction time from the remaining time
     * (capped at remaining time) and instantly awards the units that correspond to
     * that removed time. Can only be used once per queue item — after halving the
     * "Finish" button is shown instead.
     *
     * Example: 100 units × 3s = 300s total. If 200s remain when halving:
     * - timeReduction = min(150, 200) = 150s removed
     * - unitsToAward  = floor(150 / 3) = 50 awarded instantly
     * - 50s remaining at the same 3s/unit rate
     *
     * Both time_start and time_end are shifted back by timeReduction so that
     * time_per_unit = (time_end - time_start) / object_amount stays constant and
     * normal queue processing continues without double-counting.
     *
     * @param User $user The user performing the halving
     * @param int $queueItemId The unit queue item ID
     * @param PlanetService $planet The planet service
     * @return array{success: bool, new_time_end: int, cost: int, new_balance: int, remaining_time: int}
     * @throws Exception If insufficient Dark Matter, already halved, or invalid queue item
     */
    public function halveUnit(User $user, int $queueItemId, PlanetService $planet): array
    {
        /** @var array{success: bool, new_time_end: int, cost: int, new_balance: int, remaining_time: int} $result */
        $result = DB::transaction(function () use ($user, $queueItemId, $planet) {
            // Lock user row for Dark Matter balance
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
            if (!$lockedUser) {
                throw new Exception('User not found');
            }

            // Lock and retrieve queue item
            $queueItem = UnitQueue::where('id', $queueItemId)
                ->where('planet_id', $planet->getPlanetId())
                ->where('processed', 0)
                ->lockForUpdate()
                ->first();

            if (!$queueItem) {
                throw new Exception('Queue item not found or already completed');
            }

            if ($queueItem->dm_halved) {
                throw new Exception('Queue item has already been halved. Use Complete to finish instantly.');
            }

            $currentTime = $this->getCurrentTimestamp();
            $remainingTime = (int)$queueItem->time_end - $currentTime;

            if ($remainingTime <= 0) {
                throw new Exception('Queue item already completed');
            }

            // Calculate cost based on remaining time before halving
            $cost = $this->calculateHalvingCost($remainingTime, 'unit');

            // Check balance
            if ($lockedUser->dark_matter < $cost) {
                throw new Exception("Insufficient Dark Matter. Required: {$cost}, Available: {$lockedUser->dark_matter}");
            }

            // Debit Dark Matter
            $lockedUser->dark_matter -= $cost;
            $lockedUser->save();

            // Record transaction
            $object = ObjectService::getUnitObjectById($queueItem->object_id);
            $description = "Halving unit: {$object->title} on planet {$planet->getPlanetName()} (ID: {$planet->getPlanetId()})";
            $this->transactionService->recordTransaction(
                $lockedUser,
                -$cost,
                DarkMatterTransactionType::HALVING->value,
                $description,
                $lockedUser->dark_matter
            );

            // Calculate time_per_unit from original order values
            $timePerUnit = (int)$queueItem->time_duration / (int)$queueItem->object_amount;

            // Time reduction: 50% of original duration, capped at remaining time
            $halfDuration = intdiv((int)$queueItem->time_duration, 2);
            $timeReduction = min($halfDuration, $remainingTime);

            // Units to award instantly: units that correspond to the removed time
            $unitsToAward = (int)floor($timeReduction / $timePerUnit);

            // Award units instantly to the planet
            if ($unitsToAward > 0) {
                $planet->addUnit($object->machine_name, $unitsToAward);
            }

            // Capture original time_end to know which subsequent items are chained to this one
            // and need to be updated as well.
            $originalTimeEnd = (int)$queueItem->time_end;

            // Shift time_start and time_end back equally to preserve time_per_unit rate.
            // Reset time_progress to now so the processing loop does not double-count.
            $queueItem->time_start = (int)$queueItem->time_start - $timeReduction;
            $queueItem->time_end = $originalTimeEnd - $timeReduction;
            $queueItem->object_amount_progress = (int)$queueItem->object_amount_progress + $unitsToAward;
            $queueItem->time_progress = $currentTime;
            $queueItem->dm_halved = 1;

            $newTimeEnd = (int)$queueItem->time_end;
            $newRemainingTime = $newTimeEnd - $currentTime;

            // If fully awarded, mark as processed
            if ($queueItem->object_amount_progress >= (int)$queueItem->object_amount) {
                $queueItem->processed = 1;
                $queueItem->time_end = $currentTime;
                $newTimeEnd = $currentTime;
                $newRemainingTime = 0;
            }

            $queueItem->save();

            // Update any other queued items for this planet to advance their time_start and time_end
            // equally as well to prevent gaps in the queue.
            $this->updateFutureQueueItems($planet->getPlanetId(), $originalTimeEnd, $newTimeEnd);

            return [
                'success' => true,
                'new_time_end' => $newTimeEnd,
                'cost' => $cost,
                'new_balance' => $lockedUser->dark_matter,
                'remaining_time' => $newRemainingTime,
            ];
        });

        return $result;
    }

    /**
     * Complete a unit queue item instantly using Dark Matter.
     *
     * This is used when the remaining time is short enough that a halve would
     * reduce it to zero. Instead of halving, the queue is completed immediately.
     *
     * @param User $user The user performing the completion
     * @param int $queueItemId The unit queue item ID
     * @param PlanetService $planet The planet service
     * @return array{success: bool, cost: int, new_balance: int}
     * @throws Exception If insufficient Dark Matter or invalid queue item
     */
    public function completeUnit(User $user, int $queueItemId, PlanetService $planet): array
    {
        /** @var array{success: bool, cost: int, new_balance: int} $result */
        $result = DB::transaction(function () use ($user, $queueItemId, $planet) {
            // Lock user row for Dark Matter balance
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
            if (!$lockedUser) {
                throw new Exception('User not found');
            }

            // Lock and retrieve queue item
            $queueItem = UnitQueue::where('id', $queueItemId)
                ->where('planet_id', $planet->getPlanetId())
                ->where('processed', 0)
                ->lockForUpdate()
                ->first();

            if (!$queueItem) {
                throw new Exception('Queue item not found or already completed');
            }

            // Calculate cost based on remaining time
            $currentTime = $this->getCurrentTimestamp();
            $remainingTime = (int)$queueItem->time_end - $currentTime;

            if ($remainingTime <= 0) {
                throw new Exception('Queue item already completed');
            }

            $cost = $this->calculateHalvingCost($remainingTime, 'unit');

            // Check balance
            if ($lockedUser->dark_matter < $cost) {
                throw new Exception("Insufficient Dark Matter. Required: {$cost}, Available: {$lockedUser->dark_matter}");
            }

            // Debit Dark Matter
            $lockedUser->dark_matter -= $cost;
            $lockedUser->save();

            // Record transaction
            $object = ObjectService::getUnitObjectById($queueItem->object_id);
            $description = "Completing unit: {$object->title} on planet {$planet->getPlanetName()} (ID: {$planet->getPlanetId()})";
            $this->transactionService->recordTransaction(
                $lockedUser,
                -$cost,
                DarkMatterTransactionType::HALVING->value,
                $description,
                $lockedUser->dark_matter
            );

            // Capture original time_end before overwriting so the update knows which
            // subsequent items were chained to this one.
            $originalTimeEnd = (int)$queueItem->time_end;

            // Set time_end to now so updateUnitQueue will award all remaining units.
            // Also clamp time_start to now: if the item was halved while still waiting
            // in the queue, halveUnit may have shifted time_start into the future; leaving
            // time_start > time_end would create a broken state where the queue processor
            // cannot retrieve the item (it filters by time_start <= now).
            $queueItem->time_end = $currentTime;
            if ((int)$queueItem->time_start > $currentTime) {
                $queueItem->time_start = $currentTime;
            }
            $queueItem->dm_completed = 1;
            $queueItem->save();

            // Update any other queued items for this planet to advance their time_start and time_end
            // equally as well to prevent gaps in the queue.
            $this->updateFutureQueueItems($planet->getPlanetId(), $originalTimeEnd, $currentTime);

            return [
                'success' => true,
                'cost' => $cost,
                'new_balance' => $lockedUser->dark_matter,
            ];
        });

        return $result;
    }

    /**
     * Get halving cost for a building or unit queue item.
     *
     * @param int $queueItemId The queue item ID
     * @param string $queueType 'building' or 'unit'
     * @param PlanetService $context Planet service for context
     * @return int Cost in Dark Matter
     * @throws Exception If queue item not found
     */
    public function getHalvingCostForBuildingOrUnit(int $queueItemId, string $queueType, PlanetService $context): int
    {
        $queueItem = match ($queueType) {
            'building' => BuildingQueue::where('id', $queueItemId)
                ->where('planet_id', $context->getPlanetId())
                ->where('processed', 0)
                ->where('canceled', 0)
                ->where('building', 1)
                ->first(),
            'unit' => UnitQueue::where('id', $queueItemId)
                ->where('planet_id', $context->getPlanetId())
                ->where('processed', 0)
                ->first(),
            default => throw new Exception('Invalid queue type. Must be building or unit'),
        };

        if (!$queueItem) {
            throw new Exception('Queue item not found or already completed');
        }

        // Calculate remaining time
        $currentTime = (int)Date::now()->timestamp;
        $remainingTime = (int)$queueItem->time_end - $currentTime;

        if ($remainingTime <= 0) {
            throw new Exception('Queue item already completed');
        }

        return $this->calculateHalvingCost($remainingTime, $queueType);
    }

    /**
     * Get halving cost for a research queue item.
     *
     * @param int $queueItemId The queue item ID
     * @param PlayerService $player Player service for context
     * @return int Cost in Dark Matter
     * @throws Exception If queue item not found
     */
    public function getHalvingCostForResearch(int $queueItemId, PlayerService $player): int
    {
        $queueItem = ResearchQueue::query()
            ->join('planets', 'research_queues.planet_id', '=', 'planets.id')
            ->where('research_queues.id', $queueItemId)
            ->where('planets.user_id', $player->getId())
            ->where('research_queues.processed', 0)
            ->where('research_queues.canceled', 0)
            ->where('research_queues.building', 1)
            ->select('research_queues.*')
            ->first();

        if (!$queueItem) {
            throw new Exception('Queue item not found or already completed');
        }

        // Calculate remaining time
        $currentTime = (int)Date::now()->timestamp;
        $remainingTime = (int)$queueItem->time_end - $currentTime;

        if ($remainingTime <= 0) {
            throw new Exception('Queue item already completed');
        }

        return $this->calculateHalvingCost($remainingTime, 'research');
    }

    /**
     * Update future queued items for this planet to advance their time_start and time_end
     * equally based on a issued halving/completing time reduction.
     *
     * @param int $planetId    Planet whose unit queue should be updated
     * @param int $oldTimeEnd  Original time_end of the modified item (before the change)
     * @param int $newTimeEnd  New time_end of the modified item (after the change)
     * @return void
     */
    private function updateFutureQueueItems(int $planetId, int $oldTimeEnd, int $newTimeEnd): void
    {
        if ($newTimeEnd >= $oldTimeEnd) {
            // Sanity check: time did not decrease, no update necessary.
            return;
        }

        $timeReduction = $oldTimeEnd - $newTimeEnd;

        UnitQueue::where('planet_id', $planetId)
            ->where('processed', 0)
            ->where('time_start', '>=', $oldTimeEnd)
            ->update([
                'time_start' => DB::raw("time_start - {$timeReduction}"),
                'time_end'   => DB::raw("time_end - {$timeReduction}"),
            ]);
    }
}
