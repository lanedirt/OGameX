<?php

namespace OGame\Services;

use Exception;
use OGame\Models\Resources;

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
     * Base trade rates (metal:crystal:deuterium = 3:2:1)
     * These values represent the relative worth of each resource.
     */
    private const BASE_TRADE_RATES = [
        'metal' => 3,
        'crystal' => 2,
        'deuterium' => 1,
    ];

    /**
     * Call a merchant.
     *
     * @param PlayerService $player
     * @param string $merchantType ('metal', 'crystal', or 'deuterium')
     * @return array{success: bool, message: string, tradeRates?: array}
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
                'message' => 'Insufficient dark matter. You need ' . number_format(self::DARK_MATTER_COST) . ' dark matter to call a merchant.',
            ];
        }

        // Deduct dark matter cost
        $user->dark_matter -= self::DARK_MATTER_COST;
        $user->save();

        // Generate trade rates for this merchant call
        $tradeRates = self::generateTradeRates($merchantType);

        return [
            'success' => true,
            'message' => 'Merchant called successfully.',
            'tradeRates' => $tradeRates,
        ];
    }

    /**
     * Generate randomized trade rates for a merchant.
     * Based on OGame wiki: rates range from 2:1 (best) to 3:1 (worst).
     * The base 3:2:1 ratio represents relative values, but the merchant
     * takes a significant fee making rates unfavorable to the player.
     *
     * @param string $merchantType
     * @return array{give: string, receive: array{metal?: array, crystal?: array, deuterium?: array}}
     */
    public static function generateTradeRates(string $merchantType): array
    {
        $rates = [];
        $rates['give'] = $merchantType;
        $rates['receive'] = [];

        // The merchant takes the specified resource type and gives the other two
        $receiveTypes = array_diff(['metal', 'crystal', 'deuterium'], [$merchantType]);

        foreach ($receiveTypes as $receiveType) {
            // Calculate base exchange rate (how much you give vs how much you get)
            $giveValue = self::BASE_TRADE_RATES[$merchantType];
            $receiveValue = self::BASE_TRADE_RATES[$receiveType];

            // Fair ratio (no merchant fee)
            $fairRate = $receiveValue / $giveValue;

            // Merchant takes 50-66.67% fee (rates from 2:1 to 3:1 per OGame wiki)
            // This means you get 33.33% to 50% of fair value
            // Random between 0.3333 and 0.5000
            $merchantMultiplier = (rand(3333, 5000) / 10000);

            // Calculate how much of the receive resource you get per unit of give resource
            // Example: Trading deuterium (value 1) for metal (value 3)
            // Fair rate: 3.0, but merchant gives you only 1.0-1.5 (33-50% of fair)
            $exchangeRate = $fairRate * $merchantMultiplier;

            $rates['receive'][$receiveType] = [
                'rate' => round($exchangeRate, 4),
                'display' => self::formatTradeRate($merchantType, $receiveType, $exchangeRate),
            ];
        }

        return $rates;
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
                'message' => 'Invalid resource type.',
            ];
        }

        // Check if player has enough of the give resource
        $currentResources = $planet->getResources();
        $currentAmount = $currentResources->{$giveResource}->get();

        \Log::info('Merchant trade resource check', [
            'give_resource' => $giveResource,
            'current_amount' => $currentAmount,
            'give_amount_requested' => $giveAmount,
            'has_enough' => $currentAmount >= $giveAmount,
        ]);

        if ($currentAmount < $giveAmount) {
            \Log::warning('Merchant trade failed: insufficient resources', [
                'give_resource' => $giveResource,
                'current_amount' => $currentAmount,
                'give_amount_requested' => $giveAmount,
                'shortage' => $giveAmount - $currentAmount,
            ]);

            return [
                'success' => false,
                'message' => 'Not enough ' . $giveResource . ' available. You have ' . number_format($currentAmount) . ' but need ' . number_format($giveAmount) . '.',
            ];
        }

        // Calculate how much the player receives
        $receiveAmount = (int)floor($giveAmount * $exchangeRate);

        // Check storage capacity for the receive resource
        $storageMethod = $receiveResource . 'Storage';
        $storageCapacity = $planet->{$storageMethod}()->get();
        $currentReceiveAmount = $currentResources->{$receiveResource}->get();

        \Log::info('Merchant trade storage check', [
            'receive_resource' => $receiveResource,
            'current_amount' => $currentReceiveAmount,
            'receive_amount' => $receiveAmount,
            'total_after_trade' => $currentReceiveAmount + $receiveAmount,
            'storage_capacity' => $storageCapacity,
            'has_space' => ($currentReceiveAmount + $receiveAmount) <= $storageCapacity,
        ]);

        if ($currentReceiveAmount + $receiveAmount > $storageCapacity) {
            \Log::warning('Merchant trade failed: insufficient storage', [
                'receive_resource' => $receiveResource,
                'current_amount' => $currentReceiveAmount,
                'receive_amount' => $receiveAmount,
                'storage_capacity' => $storageCapacity,
                'overflow' => ($currentReceiveAmount + $receiveAmount) - $storageCapacity,
            ]);

            return [
                'success' => false,
                'message' => 'Not enough storage capacity for ' . $receiveResource . '. You need ' . number_format($currentReceiveAmount + $receiveAmount) . ' capacity but only have ' . number_format($storageCapacity) . '.',
            ];
        }

        // Execute the trade
        try {
            // Log resources before trade
            \Log::info('Merchant trade starting', [
                'before_metal' => $currentResources->metal->get(),
                'before_crystal' => $currentResources->crystal->get(),
                'before_deuterium' => $currentResources->deuterium->get(),
                'give' => $giveResource,
                'give_amount' => $giveAmount,
                'receive' => $receiveResource,
                'receive_amount' => $receiveAmount,
            ]);

            // Deduct the resource being given
            $deductResources = new Resources(
                $giveResource === 'metal' ? $giveAmount : 0,
                $giveResource === 'crystal' ? $giveAmount : 0,
                $giveResource === 'deuterium' ? $giveAmount : 0
            );

            \Log::info('About to deduct resources', [
                'metal' => $deductResources->metal->get(),
                'crystal' => $deductResources->crystal->get(),
                'deuterium' => $deductResources->deuterium->get(),
            ]);

            $planet->deductResources($deductResources, false);

            // Add the resource being received
            $addResources = new Resources(
                $receiveResource === 'metal' ? $receiveAmount : 0,
                $receiveResource === 'crystal' ? $receiveAmount : 0,
                $receiveResource === 'deuterium' ? $receiveAmount : 0
            );

            \Log::info('About to add resources', [
                'metal' => $addResources->metal->get(),
                'crystal' => $addResources->crystal->get(),
                'deuterium' => $addResources->deuterium->get(),
            ]);

            $planet->addResources($addResources, false);

            // Log resources after operations but before save
            $afterResources = $planet->getResources();
            \Log::info('Resources after operations, before save', [
                'after_metal' => $afterResources->metal->get(),
                'after_crystal' => $afterResources->crystal->get(),
                'after_deuterium' => $afterResources->deuterium->get(),
            ]);

            // Save the planet with updated resources
            $planet->save();

            \Log::info('Planet saved successfully');

            return [
                'success' => true,
                'message' => 'Trade completed successfully.',
                'given' => $giveAmount,
                'received' => $receiveAmount,
            ];
        } catch (\Exception $e) {
            \Log::error('Merchant trade failed: ' . $e->getMessage(), [
                'give' => $giveResource,
                'receive' => $receiveResource,
                'amount' => $giveAmount,
                'rate' => $exchangeRate,
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Trade execution failed: ' . $e->getMessage(),
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
        // Check if there's an active resource trader in session
        $sessionKey = 'active_merchant_' . $player->getId();
        $activeMerchant = session()->get($sessionKey);

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
                // Update session with improved rates
                session()->put($sessionKey, [
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

            // Store in session (no dark matter cost for expedition merchants)
            session()->put($sessionKey, [
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
