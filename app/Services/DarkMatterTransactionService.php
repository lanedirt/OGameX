<?php

namespace OGame\Services;

use Illuminate\Support\Collection;
use OGame\Models\DarkMatterTransaction;
use OGame\Models\User;

/**
 * Class DarkMatterTransactionService.
 *
 * Handles Dark Matter transaction history and auditing.
 *
 * @package OGame\Services
 */
class DarkMatterTransactionService
{
    /**
     * Record a Dark Matter transaction.
     *
     * @param User $user
     * @param int $amount Positive for credit, negative for debit
     * @param string $type Transaction type from DarkMatterTransactionType enum
     * @param string $description Human-readable description
     * @param int $balanceAfter User's balance after this transaction
     * @return DarkMatterTransaction
     */
    public function recordTransaction(
        User $user,
        int $amount,
        string $type,
        string $description,
        int $balanceAfter
    ): DarkMatterTransaction {
        return DarkMatterTransaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'type' => $type,
            'description' => $description,
            'balance_after' => $balanceAfter,
            'created_at' => now(),
        ]);
    }

    /**
     * Get transaction history for a user.
     *
     * @param User $user
     * @param string|null $type Optional filter by transaction type
     * @param int $limit Maximum number of transactions to return
     * @return Collection
     */
    public function getHistory(User $user, string|null $type = null, int $limit = 50): Collection
    {
        $query = DarkMatterTransaction::forUser($user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit);

        if ($type !== null) {
            $query->ofType($type);
        }

        return $query->get();
    }

    /**
     * Get transaction statistics for a user.
     *
     * @param User $user
     * @return array{total_earned: int, total_spent: int, transaction_count: int}
     */
    public function getStatistics(User $user): array
    {
        // Use database aggregation for better performance
        $totalEarned = (int)DarkMatterTransaction::forUser($user->id)
            ->where('amount', '>', 0)
            ->sum('amount');

        $totalSpentSum = DarkMatterTransaction::forUser($user->id)
            ->where('amount', '<', 0)
            ->sum('amount');
        $totalSpent = (int)abs((float)$totalSpentSum);

        $transactionCount = DarkMatterTransaction::forUser($user->id)->count();

        return [
            'total_earned' => $totalEarned,
            'total_spent' => $totalSpent,
            'transaction_count' => $transactionCount,
        ];
    }
}
