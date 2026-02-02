<?php

namespace OGame\Services;

use Exception;
use OGame\Models\Resources;
use RuntimeException;

/**
 * Class MerchantService
 *
 * Handles all merchant-related operations including:
 * - Calculating trade rates
 * - Executing resource trades
 * - Managing dark matter costs
 *
 * @package OGame\Services
 */
class MerchantService
{
    /**
     * Dark matter cost to call a merchant.
     */
    public const DARK_MATTER_COST = 3500;

    /**
     * Storage buffer percentage to allow over storage capacity for transactions.
     * This prevents trades from failing due to resources produced between UI calculation
     * and server-side validation. 1% buffer = trades can fill storage up to 101% of capacity.
     *
     * Set to 1% to accommodate resource production during transaction processing while
     * preventing exploitation. On 18M storage this allows ~180k buffer, which is sufficient
     * even for high economy speed servers with max-level mines.
     */
    public const STORAGE_BUFFER_PERCENTAGE = 0.01;

    /**
     * Call a merchant.
     *
     * @param PlayerService $player
     * @param string $merchantType ('metal', 'crystal', or 'deuterium')
     * @return array{success: bool, message: string, tradeRates?: array{give: string, receive: array<string, array{rate: float, display: string}>}}
     * @throws Exception
     */
    public static function callMerchant(PlayerService $player, string $merchantType): array
    {
        // Validate merchant type
        if (!in_array($merchantType, ['metal', 'crystal', 'deuterium'])) {
            throw new Exception('Invalid merchant type.');
        }

        // Check if player has enough dark matter
        $user = $player->getUser();
        if ($user->dark_matter < self::DARK_MATTER_COST) {
            return [
                'success' => false,
                'message' => __('t_merchant.error.trade.insufficient_dark_matter', ['cost' => number_format(self::DARK_MATTER_COST)]),
            ];
        }

        // Deduct dark matter cost atomically using DarkMatterService to prevent race conditions
        try {
            $darkMatterService = resolve(DarkMatterService::class);
            $darkMatterService->debit($user, self::DARK_MATTER_COST, 'merchant_call', 'Called ' . $merchantType . ' merchant');
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => __('t_merchant.error.trade.insufficient_dark_matter', ['cost' => number_format(self::DARK_MATTER_COST)]),
            ];
        }

        // Generate trade rates for this merchant call
        $tradeRates = self::generateTradeRates($merchantType);

        return [
            'success' => true,
            'message' => __('t_merchant.success.merchant_called'),
            'tradeRates' => $tradeRates,
        ];
    }

    /**
     * Generate randomized trade rates for a merchant.
     * Based on OGame mechanics: The resource being sold always has its maximum value.
     * The two resources you can receive have variable rates with weighted probabilities.
     *
     * Rates follow a triangular distribution:
     * - Metal: 2.10 to 3.00 (sold resource always 3.00)
     * - Crystal: 1.40 to 2.00 (sold resource always 2.00)
     * - Deuterium: 0.70 to 1.00 (sold resource always 1.00)
     *
     * The median rate is 2.70:1.80:0.90, with 3.00:2.00:1.00 having highest probability (14.97%).
     *
     * @param string $merchantType
     * @return array{give: string, receive: array<string, array{rate: float, display: string}>}
     */
    public static function generateTradeRates(string $merchantType): array
    {
        $rates = [];
        $rates['give'] = $merchantType;
        $rates['receive'] = [];

        // The merchant takes the specified resource type and gives the other two
        $receiveTypes = array_diff(['metal', 'crystal', 'deuterium'], [$merchantType]);

        foreach ($receiveTypes as $receiveType) {
            // Generate a random rate using weighted triangular distribution
            $rate = self::generateWeightedRate($receiveType);

            $rates['receive'][$receiveType] = [
                'rate' => round($rate, 2),  // Display with 2 decimal places
                'display' => self::formatTradeRate($merchantType, $receiveType, $rate),
            ];
        }

        return $rates;
    }

    /**
     * Generate a weighted random rate for a resource type.
     * Uses triangular distribution where 3.00:2.00:1.00 has the highest probability (14.97%).
     *
     * Rate ranges:
     * - Metal: 2.10 to 3.00 (in 0.03 increments)
     * - Crystal: 1.40 to 2.00 (in 0.02 increments)
     * - Deuterium: 0.70 to 1.00 (in 0.01 increments)
     *
     * @param string $resourceType
     * @return float
     */
    private static function generateWeightedRate(string $resourceType): float
    {
        // Define rate ranges and increments based on resource type
        $ranges = [
            'metal' => ['min' => 2.10, 'max' => 3.00, 'increment' => 0.03],
            'crystal' => ['min' => 1.40, 'max' => 2.00, 'increment' => 0.02],
            'deuterium' => ['min' => 0.70, 'max' => 1.00, 'increment' => 0.01],
        ];

        $range = $ranges[$resourceType];
        $min = $range['min'];
        $max = $range['max'];
        $increment = $range['increment'];

        // Calculate number of steps
        $steps = round(($max - $min) / $increment);

        // Generate weights for triangular distribution
        // Weights increase from 1 to (steps+1), then decrease symmetrically
        // The maximum value has the highest weight
        $weights = [];
        $totalWeight = 0;

        for ($i = 0; $i <= $steps; $i++) {
            // Triangular distribution: weight increases to middle, then decreases
            // Maximum value (at $i = $steps) gets highest weight
            if ($i <= $steps / 2) {
                $weight = $i + 1;
            } else {
                $weight = $steps - $i + 1;
            }

            // Maximum value (3.00, 2.00, 1.00) gets extra weight for 14.97% probability
            if ($i == $steps) {
                $weight = round($weight * 3.14);  // Boost to achieve ~15% probability
            }

            $weights[$i] = $weight;
            $totalWeight += $weight;
        }

        // Select a random weighted index
        $randomValue = rand(1, (int)$totalWeight);
        $cumulativeWeight = 0;

        foreach ($weights as $index => $weight) {
            $cumulativeWeight += $weight;
            if ($randomValue <= $cumulativeWeight) {
                return round($min + ($index * $increment), 2);
            }
        }

        // Fallback (should never reach here)
        return $max;
    }

    /**
     * Format a trade rate for display (e.g., "1,000 deuterium = 2,400 metal").
     *
     * @param string $giveType
     * @param string $receiveType
     * @param float $rate
     * @return string
     */
    private static function formatTradeRate(string $giveType, string $receiveType, float $rate): string
    {
        $baseAmount = 1000;
        $receiveAmount = (int)round($baseAmount * $rate);

        return number_format($baseAmount) . ' ' . ucfirst($giveType) .
               ' = ' .
               number_format($receiveAmount) . ' ' . ucfirst($receiveType);
    }

    /**
     * Execute a resource trade with the merchant.
     *
     * @param PlanetService $planet
     * @param string $giveResource
     * @param string $receiveResource
     * @param int $giveAmount
     * @param float $exchangeRate
     * @return array{success: bool, message: string, given?: int, received?: int}
     */
    public static function executeTrade(
        PlanetService $planet,
        string $giveResource,
        string $receiveResource,
        int $giveAmount,
        float $exchangeRate
    ): array {
        // Validate resource types
        $validResources = ['metal', 'crystal', 'deuterium'];
        if (!in_array($giveResource, $validResources) || !in_array($receiveResource, $validResources)) {
            return [
                'success' => false,
                'message' => __('t_merchant.error.trade.invalid_resource_type'),
            ];
        }

        // Check if player has enough of the give resource
        $currentResources = $planet->getResources();
        $currentAmount = $currentResources->{$giveResource}->get();

        if ($currentAmount < $giveAmount) {
            return [
                'success' => false,
                'message' => __('t_merchant.error.trade.not_enough_resource', [
                    'resource' => $giveResource,
                    'have' => number_format($currentAmount),
                    'need' => number_format($giveAmount)
                ]),
            ];
        }

        // Calculate how much the player receives
        $receiveAmount = (int)floor($giveAmount * $exchangeRate);

        // Check storage capacity for the receive resource
        $storageMethod = $receiveResource . 'Storage';
        $storageCapacity = $planet->{$storageMethod}()->get();
        $currentReceiveAmount = $currentResources->{$receiveResource}->get();

        // Apply storage buffer to account for resource production during transaction
        // Allow trades that would go slightly over base storage (up to +1%) to prevent
        // failures due to resources produced between UI calculation and server validation
        $maxAllowedCapacity = (int)floor($storageCapacity * (1 + self::STORAGE_BUFFER_PERCENTAGE));

        if ($currentReceiveAmount + $receiveAmount > $maxAllowedCapacity) {
            return [
                'success' => false,
                'message' => __('t_merchant.error.trade.not_enough_storage', [
                    'resource' => $receiveResource,
                    'need' => number_format($currentReceiveAmount + $receiveAmount),
                    'have' => number_format($storageCapacity)
                ]),
            ];
        }

        // Trade is allowed - execute at full requested amounts
        // Resources can go up to 105% of base storage capacity

        // Execute the trade using atomic deduction to prevent race conditions
        try {
            // Deduct the resource being given atomically
            $deductResources = new Resources(
                $giveResource === 'metal' ? $giveAmount : 0,
                $giveResource === 'crystal' ? $giveAmount : 0,
                $giveResource === 'deuterium' ? $giveAmount : 0
            );

            // Use atomic deduction (save_planet = true) to prevent race conditions
            $planet->deductResources($deductResources, true);

            // Add the resource being received (can exceed base storage up to 101%)
            $addResources = new Resources(
                $receiveResource === 'metal' ? $receiveAmount : 0,
                $receiveResource === 'crystal' ? $receiveAmount : 0,
                $receiveResource === 'deuterium' ? $receiveAmount : 0
            );

            $planet->addResources($addResources, true);

            return [
                'success' => true,
                'message' => __('t_merchant.success.trade_completed'),
                'given' => $giveAmount,
                'received' => $receiveAmount,
            ];
        } catch (RuntimeException $e) {
            // Atomic deduction failed - not enough resources
            return [
                'success' => false,
                'message' => __('t_merchant.error.trade.not_enough_resource', [
                    'resource' => $giveResource,
                    'have' => '0',
                    'need' => number_format($giveAmount)
                ]),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => __('t_merchant.error.trade.execution_failed', ['error' => $e->getMessage()]),
            ];
        }
    }

    /**
     * Add expedition merchant bonus to player.
     * Expeditions only call RESOURCE TRADERS (metal/crystal/deuterium), never scrap merchants.
     *
     * Behavior:
     * - If no active resource trader: Call a random resource trader for free
     * - If active resource trader exists: Keep same type, potentially improve rates (never worsen)
     *
     * @param PlayerService $player
     * @return array{improved: bool, merchant_type: string, called_new: bool}
     */
    public static function addExpeditionBonus(PlayerService $player): array
    {
        // Check if there's an active resource trader in cache (persists across sessions)
        $cacheKey = 'active_merchant_' . $player->getId();
        $activeMerchant = cache()->get($cacheKey);

        if ($activeMerchant) {
            // Merchant already active - keep same type but potentially improve rates
            $merchantType = $activeMerchant['type'];
            $currentRates = $activeMerchant['trade_rates'];

            // Generate new rates for the same merchant type
            $newRates = self::generateTradeRates($merchantType);

            // Check if new rates are better for ALL receive resources
            $improved = false;
            foreach ($newRates['receive'] as $receiveResource => $rateData) {
                $currentRate = $currentRates['receive'][$receiveResource]['rate'] ?? 0;
                $newRate = $rateData['rate'];

                // Better rate = higher exchange rate (you get more)
                if ($newRate > $currentRate) {
                    $improved = true;
                    // Update to the better rate
                    $currentRates['receive'][$receiveResource] = $rateData;
                }
                // If any rate is worse, keep the old rate (don't update)
            }

            if ($improved) {
                // Update cache with improved rates (persists until used/replaced)
                cache()->forever($cacheKey, [
                    'type' => $merchantType,
                    'trade_rates' => $currentRates,
                    'called_at' => $activeMerchant['called_at'],
                ]);
            }

            return [
                'improved' => $improved,
                'merchant_type' => $merchantType,
                'called_new' => false,
            ];
        } else {
            // No active merchant - call a random RESOURCE TRADER for free
            // Expeditions ONLY call resource traders, never scrap merchants
            $resourceTypes = ['metal', 'crystal', 'deuterium'];
            $merchantType = $resourceTypes[array_rand($resourceTypes)];

            // Generate trade rates
            $tradeRates = self::generateTradeRates($merchantType);

            // Store in cache (no dark matter cost for expedition merchants, persists until used/replaced)
            cache()->forever($cacheKey, [
                'type' => $merchantType,
                'trade_rates' => $tradeRates,
                'called_at' => time(),
            ]);

            return [
                'improved' => false,
                'merchant_type' => $merchantType,
                'called_new' => true,
            ];
        }
    }

    /**
     * Use one expedition bonus merchant call.
     *
     * NOTE: This function is currently unused as expeditions now immediately call
     * a resource trader via addExpeditionBonus() rather than granting bonus credits.
     * Kept for potential future use or backwards compatibility.
     *
     * @param PlayerService $player
     * @return bool True if bonus was used, false if no bonuses available
     * @deprecated Expeditions now immediately call merchants instead of granting bonuses
     */
    public static function useExpeditionBonus(PlayerService $player): bool
    {
        $user = $player->getUser();

        if ($user->merchant_expedition_bonuses > 0) {
            $user->merchant_expedition_bonuses--;
            $user->save();
            return true;
        }

        return false;
    }
}
