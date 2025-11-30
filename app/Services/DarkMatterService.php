<?php

namespace OGame\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use OGame\Enums\DarkMatterTransactionType;
use OGame\Models\User;

/**
 * Class DarkMatterService.
 *
 * Primary service for all Dark Matter operations.
 *
 * @package OGame\Services
 */
class DarkMatterService
{
    /**
     * DarkMatterService constructor.
     *
     * @param DarkMatterTransactionService $transactionService
     * @param SettingsService $settingsService
     */
    public function __construct(
        private DarkMatterTransactionService $transactionService,
        private SettingsService $settingsService
    ) {
    }

    /**
     * Credit Dark Matter to a user.
     *
     * @param User $user
     * @param int $amount
     * @param string $type Transaction type from DarkMatterTransactionType enum
     * @param string $description
     * @return void
     * @throws Exception
     */
    public function credit(User $user, int $amount, string $type, string $description): void
    {
        if ($amount < 0) {
            throw new Exception('Cannot credit negative amount');
        }

        DB::transaction(function () use ($user, $amount, $type, $description) {
            // Lock the user row for update
            $user = User::where('id', $user->id)->lockForUpdate()->first();

            // Update balance
            $user->dark_matter += $amount;
            $user->save();

            // Record transaction
            $this->transactionService->recordTransaction(
                $user,
                $amount,
                $type,
                $description,
                $user->dark_matter
            );
        });
    }

    /**
     * Debit Dark Matter from a user.
     *
     * @param User $user
     * @param int $amount
     * @param string $type Transaction type from DarkMatterTransactionType enum
     * @param string $description
     * @return void
     * @throws Exception
     */
    public function debit(User $user, int $amount, string $type, string $description): void
    {
        if ($amount < 0) {
            throw new Exception('Cannot debit negative amount');
        }

        DB::transaction(function () use ($user, $amount, $type, $description) {
            // Lock the user row for update
            $user = User::where('id', $user->id)->lockForUpdate()->first();

            // Check balance
            if ($user->dark_matter < $amount) {
                throw new Exception("Insufficient Dark Matter. Required: {$amount}, Available: {$user->dark_matter}");
            }

            // Update balance
            $user->dark_matter -= $amount;
            $user->save();

            // Record transaction (negative amount for debit)
            $this->transactionService->recordTransaction(
                $user,
                -$amount,
                $type,
                $description,
                $user->dark_matter
            );
        });
    }

    /**
     * Get current Dark Matter balance for a user.
     *
     * @param User $user
     * @return int
     */
    public function getBalance(User $user): int
    {
        return $user->dark_matter;
    }

    /**
     * Check if user can afford an amount.
     *
     * @param User $user
     * @param int $amount
     * @return bool
     */
    public function canAfford(User $user, int $amount): bool
    {
        return $user->dark_matter >= $amount;
    }

    /**
     * Process periodic regeneration for a user.
     *
     * @param User $user
     * @return void
     */
    public function processRegeneration(User $user): void
    {
        $regenPeriod = (int)$this->settingsService->get('dark_matter_regen_period', 604800);
        $regenAmount = (int)$this->settingsService->get('dark_matter_regen_amount', 150000);

        DB::transaction(function () use ($user, $regenPeriod, $regenAmount) {
            // Lock the user row for update
            $user = User::where('id', $user->id)->lockForUpdate()->first();

            // Check if regeneration is due
            if ($user->dark_matter_last_regen === null) {
                // First time regeneration
                $user->dark_matter += $regenAmount;
                $user->dark_matter_last_regen = now();
                $user->save();

                // Record transaction
                $this->transactionService->recordTransaction(
                    $user,
                    $regenAmount,
                    DarkMatterTransactionType::REGENERATION->value,
                    'Periodic Dark Matter regeneration',
                    $user->dark_matter
                );
                return;
            }

            $timeSinceLastRegen = now()->diffInSeconds($user->dark_matter_last_regen);

            if ($timeSinceLastRegen >= $regenPeriod) {
                $user->dark_matter += $regenAmount;
                $user->dark_matter_last_regen = now();
                $user->save();

                // Record transaction
                $this->transactionService->recordTransaction(
                    $user,
                    $regenAmount,
                    DarkMatterTransactionType::REGENERATION->value,
                    'Periodic Dark Matter regeneration',
                    $user->dark_matter
                );
            }
        });
    }

    /**
     * Process regeneration for all eligible users.
     *
     * @return void
     */
    public function processAllRegeneration(): void
    {
        $regenPeriod = (int)$this->settingsService->get('dark_matter_regen_period', 604800);

        // Get users who need regeneration
        $users = User::where(function ($query) use ($regenPeriod) {
            $query->whereNull('dark_matter_last_regen')
                ->orWhere('dark_matter_last_regen', '<=', now()->subSeconds($regenPeriod));
        })->get();

        foreach ($users as $user) {
            try {
                $this->processRegeneration($user);
            } catch (Exception $e) {
                // Log error but continue processing other users
                logger()->error("Failed to process regeneration for user {$user->id}: {$e->getMessage()}");
            }
        }
    }

    /**
     * Calculate expedition reward amount.
     *
     * @param bool $hasPathfinder Whether the expedition fleet has Pathfinder ships
     * @return int
     */
    public function calculateExpeditionReward(bool $hasPathfinder): int
    {
        $multiplier = (float)$this->settingsService->get('expedition_dark_matter_multiplier', '1.0');

        if ($hasPathfinder) {
            $min = (int)$this->settingsService->get('expedition_dark_matter_min_pathfinder', 300);
            $max = (int)$this->settingsService->get('expedition_dark_matter_max_pathfinder', 400);
        } else {
            $min = (int)$this->settingsService->get('expedition_dark_matter_min_no_pathfinder', 150);
            $max = (int)$this->settingsService->get('expedition_dark_matter_max_no_pathfinder', 200);
        }

        $baseReward = rand($min, $max);
        return (int)($baseReward * $multiplier);
    }

    /**
     * Calculate speed-up cost based on remaining time.
     * Formula: ceil((remaining_time_in_hours / 2) * (1 / universe_speed))
     * Minimum cost: 1 DM
     *
     * @param int $remainingSeconds
     * @param float $universeSpeed
     * @return int
     */
    public function calculateSpeedupCost(int $remainingSeconds, float $universeSpeed): int
    {
        $remainingHours = $remainingSeconds / 3600;
        $cost = ceil(($remainingHours / 2) * (1 / $universeSpeed));

        return max(1, (int)$cost);
    }
}
