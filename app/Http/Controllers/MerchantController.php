<?php

namespace OGame\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Services\MerchantService;
use OGame\Services\PlayerService;

class MerchantController extends OGameController
{
    /**
     * Shows the merchant index page
     *
     * @param PlayerService $player
     * @return View
     */
    public function index(PlayerService $player): View
    {
        $this->setBodyId('traderOverview');

        $darkMatter = $player->getUser()->dark_matter;
        $merchantCost = MerchantService::DARK_MATTER_COST;

        return view('ingame.merchant.index', [
            'darkMatter' => $darkMatter,
            'merchantCost' => $merchantCost,
        ]);
    }

    /**
     * Show the resource market selection page
     *
     * @param Request $request
     * @param PlayerService $player
     * @return View
     */
    public function resourceMarket(Request $request, PlayerService $player): View
    {
        $this->setBodyId('traderOverview');

        $darkMatter = $player->getUser()->dark_matter;
        $merchantCost = MerchantService::DARK_MATTER_COST;

        // Check if a merchant is already active for this user
        $activeMerchant = $request->session()->get('active_merchant_' . $player->getId());

        return view('ingame.merchant.resource-market', [
            'darkMatter' => $darkMatter,
            'merchantCost' => $merchantCost,
            'activeMerchant' => $activeMerchant,
        ]);
    }

    /**
     * Show the resource market page for a specific merchant type
     *
     * @param Request $request
     * @param PlayerService $player
     * @param string $type
     * @return View
     */
    public function showMarket(Request $request, PlayerService $player, string $type): View
    {
        // Validate merchant type
        if (!in_array($type, ['metal', 'crystal', 'deuterium'])) {
            abort(404);
        }

        $planet = $player->planets->current();

        // Check if a merchant is already active for this user (planet-agnostic)
        $activeMerchant = $request->session()->get('active_merchant_' . $player->getId());

        // Check if this is an overlay request
        $isOverlay = $request->has('overlay') || $request->ajax();

        if ($isOverlay) {
            // Return the overlay view with no layout
            return view('ingame.merchant.overlay', [
                'merchantType' => $type,
                'planet' => $planet,
                'activeMerchant' => $activeMerchant,
            ]);
        }

        // Regular page view
        $this->setBodyId('traderOverview');
        return view('ingame.merchant.market', [
            'merchantType' => $type,
            'planet' => $planet,
            'activeMerchant' => $activeMerchant,
        ]);
    }

    /**
     * Call a merchant (planet-agnostic, user-level)
     *
     * @param Request $request
     * @param PlayerService $player
     * @return JsonResponse
     */
    public function callMerchant(Request $request, PlayerService $player): JsonResponse
    {
        $merchantType = $request->input('type');

        try {
            $result = MerchantService::callMerchant($player, $merchantType);

            if ($result['success']) {
                // Store active merchant in session (user-level, not planet-specific)
                $request->session()->put('active_merchant_' . $player->getId(), [
                    'type' => $merchantType,
                    'trade_rates' => $result['tradeRates'],
                    'called_at' => time(),
                ]);
            }

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Execute a trade with the merchant
     *
     * @param Request $request
     * @param PlayerService $player
     * @return JsonResponse
     */
    public function executeTrade(Request $request, PlayerService $player): JsonResponse
    {
        $giveResource = $request->input('give_resource');
        $receiveResource = $request->input('receive_resource');
        $giveAmount = (int)$request->input('give_amount');
        $exchangeRate = (float)$request->input('exchange_rate');

        try {
            // Get current planet (resources will be deducted from current planet)
            $planet = $player->planets->current();

            // Verify there's an active merchant for this user (planet-agnostic)
            $activeMerchant = $request->session()->get('active_merchant_' . $player->getId());
            if (!$activeMerchant) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active merchant. Please call a merchant first.',
                ], 400);
            }

            // Verify the merchant type matches
            if ($activeMerchant['type'] !== $giveResource) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid trade: merchant type mismatch.',
                ], 400);
            }

            // Verify the exchange rate matches the active merchant's rates
            if (!isset($activeMerchant['trade_rates']['receive'][$receiveResource]) ||
                abs($activeMerchant['trade_rates']['receive'][$receiveResource]['rate'] - $exchangeRate) > 0.0001) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid exchange rate.',
                ], 400);
            }

            // Execute the trade using current planet's resources
            $result = MerchantService::executeTrade(
                $planet,
                $giveResource,
                $receiveResource,
                $giveAmount,
                $exchangeRate
            );

            if ($result['success']) {
                // Remove the active merchant (one-time trade only)
                $request->session()->forget('active_merchant_' . $player->getId());
            }

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Dismiss the current merchant without trading
     *
     * @param Request $request
     * @param PlayerService $player
     * @return JsonResponse
     */
    public function dismissMerchant(Request $request, PlayerService $player): JsonResponse
    {
        $request->session()->forget('active_merchant_' . $player->getId());

        return response()->json([
            'success' => true,
            'message' => 'Merchant dismissed.',
        ]);
    }

    /**
     * Show the scrap merchant page
     *
     * @param Request $request
     * @param PlayerService $player
     * @return View
     */
    public function scrap(Request $request, PlayerService $player): View
    {
        $this->setBodyId('traderOverview');

        $planet = $player->planets->current();
        $user = $player->getUser();

        // Get scrap merchant session data (planet-specific)
        $scrapSession = $request->session()->get('scrap_merchant_' . $planet->getPlanetId(), [
            'offer_percentage' => 35, // Base 35%
            'bargain_count' => 0,
        ]);

        // Calculate bargain cost (increases by 2000 each time: 4000, 6000, 8000, etc.)
        $bargainCost = 4000 + ($scrapSession['bargain_count'] * 2000);

        // Get all ships and defense from planet with their costs
        $objectService = resolve(\OGame\Services\ObjectService::class);

        // Build ships array
        $ships = [];
        $shipObjects = $objectService::getShipObjects();
        foreach ($shipObjects as $shipObject) {
            $amount = $planet->getObjectAmount($shipObject->machine_name);
            $ships[$shipObject->id] = [
                'name' => $shipObject->title,
                'amount' => $amount,
                'cost' => [
                    'metal' => $shipObject->price->resources->metal->get(),
                    'crystal' => $shipObject->price->resources->crystal->get(),
                    'deuterium' => $shipObject->price->resources->deuterium->get(),
                ],
            ];
        }

        // Build defense array
        $defense = [];
        $defenseObjects = $objectService::getDefenseObjects();
        foreach ($defenseObjects as $defenseObject) {
            $amount = $planet->getObjectAmount($defenseObject->machine_name);
            $defense[$defenseObject->id] = [
                'name' => $defenseObject->title,
                'amount' => $amount,
                'cost' => [
                    'metal' => $defenseObject->price->resources->metal->get(),
                    'crystal' => $defenseObject->price->resources->crystal->get(),
                    'deuterium' => $defenseObject->price->resources->deuterium->get(),
                ],
            ];
        }

        // TODO: Future-proofing for premium ships (Reaper: 218, Pathfinder: 219)
        // These might require special handling or restrictions in the scrap merchant
        // Uncomment and implement when premium ship scrap rules are defined:
        /*
        // Remove or mark Reaper and Pathfinder if they shouldn't be scrappable
        if (isset($ships[218])) {
            // Option 1: Remove from scrap list entirely
            // unset($ships[218]);

            // Option 2: Mark as non-scrappable but show them
            // $ships[218]['scrappable'] = false;
        }

        if (isset($ships[219])) {
            // Option 1: Remove from scrap list entirely
            // unset($ships[219]);

            // Option 2: Mark as non-scrappable but show them
            // $ships[219]['scrappable'] = false;
        }
        */

        // Get storage capacity
        $storageCapacity = [
            'metal' => (int)floor($planet->metalStorage()->get()),
            'crystal' => (int)floor($planet->crystalStorage()->get()),
            'deuterium' => (int)floor($planet->deuteriumStorage()->get()),
        ];

        $currentResources = [
            'metal' => (int)floor($planet->metal()->get()),
            'crystal' => (int)floor($planet->crystal()->get()),
            'deuterium' => (int)floor($planet->deuterium()->get()),
        ];

        return view('ingame.merchant.scrap', [
            'offerPercentage' => $scrapSession['offer_percentage'],
            'bargainCount' => $scrapSession['bargain_count'],
            'bargainCost' => $bargainCost,
            'darkMatter' => $user->dark_matter,
            'ships' => $ships,
            'defense' => $defense,
            'storageCapacity' => $storageCapacity,
            'currentResources' => $currentResources,
        ]);
    }

    /**
     * Bargain with scrap merchant to increase offer percentage
     *
     * @param Request $request
     * @param PlayerService $player
     * @return JsonResponse
     */
    public function scrapBargain(Request $request, PlayerService $player): JsonResponse
    {
        $planet = $player->planets->current();
        $user = $player->getUser();

        // Get current scrap session
        $scrapSession = $request->session()->get('scrap_merchant_' . $planet->getPlanetId(), [
            'offer_percentage' => 35,
            'bargain_count' => 0,
        ]);

        // Check if already at max
        if ($scrapSession['offer_percentage'] >= 75) {
            return response()->json([
                'success' => false,
                'message' => 'Offer is already at maximum (75%).',
            ], 400);
        }

        // Calculate cost
        $bargainCost = 4000 + ($scrapSession['bargain_count'] * 2000);

        // Check dark matter
        if ($user->dark_matter < $bargainCost) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient dark matter.',
            ], 400);
        }

        // Deduct dark matter
        $user->dark_matter -= $bargainCost;
        $user->save();

        // Increase offer by 5-14% (appears to be 9% increments based on wiki)
        $increase = rand(5, 14);
        $newPercentage = min(75, $scrapSession['offer_percentage'] + $increase);

        // Update session
        $scrapSession['offer_percentage'] = $newPercentage;
        $scrapSession['bargain_count']++;
        $request->session()->put('scrap_merchant_' . $planet->getPlanetId(), $scrapSession);

        // Calculate new cost for next bargain
        $newCost = 4000 + ($scrapSession['bargain_count'] * 2000);

        return response()->json([
            'success' => true,
            'newPercentage' => $newPercentage,
            'bargainCount' => $scrapSession['bargain_count'],
            'darkMatter' => $user->dark_matter,
            'newCost' => $newCost,
        ]);
    }

    /**
     * Execute scrap trade
     *
     * @param Request $request
     * @param PlayerService $player
     * @return JsonResponse
     */
    public function scrapExecute(Request $request, PlayerService $player): JsonResponse
    {
        $items = $request->input('items', []);

        if (empty($items)) {
            return response()->json([
                'success' => false,
                'message' => 'No items selected.',
            ], 400);
        }

        $planet = $player->planets->current();

        // Get scrap session
        $scrapSession = $request->session()->get('scrap_merchant_' . $planet->getPlanetId(), [
            'offer_percentage' => 35,
            'bargain_count' => 0,
        ]);

        $offerPercentage = $scrapSession['offer_percentage'];

        // Calculate total resources to return
        $totalMetal = 0;
        $totalCrystal = 0;
        $totalDeuterium = 0;

        // Validate and calculate
        foreach ($items as $itemId => $amount) {
            $amount = (int)$amount;
            if ($amount <= 0) {
                continue;
            }

            // TODO: Future-proofing - check if premium ships (Reaper: 218, Pathfinder: 219) are scrappable
            /*
            if (in_array((int)$itemId, [218, 219])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Premium ships cannot be scrapped.',
                ], 400);
            }
            */

            // Get item cost from object service
            $objectService = resolve(\OGame\Services\ObjectService::class);
            $object = $objectService->getObjectById((int)$itemId);

            if (!$object) {
                continue;
            }

            // Check if player has enough
            $currentAmount = $planet->getObjectAmount($object->machine_name);
            if ($currentAmount < $amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough ' . $object->title . ' available.',
                ], 400);
            }

            // Calculate return resources
            $cost = $object->price;
            $totalMetal += $cost->resources->metal->get() * $amount;
            $totalCrystal += $cost->resources->crystal->get() * $amount;
            $totalDeuterium += $cost->resources->deuterium->get() * $amount;
        }

        // Apply offer percentage
        $returnMetal = (int)floor($totalMetal * ($offerPercentage / 100));
        $returnCrystal = (int)floor($totalCrystal * ($offerPercentage / 100));
        $returnDeuterium = (int)floor($totalDeuterium * ($offerPercentage / 100));

        // Check storage capacity
        $storageCapacity = [
            'metal' => $planet->metalStorage()->get(),
            'crystal' => $planet->crystalStorage()->get(),
            'deuterium' => $planet->deuteriumStorage()->get(),
        ];

        $currentResources = $planet->getResources();
        // Ensure free storage is never negative (can happen when production exceeds storage)
        $freeMetalStorage = max(0, $storageCapacity['metal'] - $currentResources->metal->get());
        $freeCrystalStorage = max(0, $storageCapacity['crystal'] - $currentResources->crystal->get());
        $freeDeuteriumStorage = max(0, $storageCapacity['deuterium'] - $currentResources->deuterium->get());

        $returnMetal = min($returnMetal, $freeMetalStorage);
        $returnCrystal = min($returnCrystal, $freeCrystalStorage);
        $returnDeuterium = min($returnDeuterium, $freeDeuteriumStorage);

        // Execute the trade: remove items and add resources
        $objectService = resolve(\OGame\Services\ObjectService::class);
        foreach ($items as $itemId => $amount) {
            $amount = (int)$amount;
            if ($amount <= 0) {
                continue;
            }

            $object = $objectService->getObjectById((int)$itemId);
            if ($object) {
                $planet->removeUnit($object->machine_name, $amount);
            }
        }

        // Add resources
        $resources = new \OGame\Models\Resources($returnMetal, $returnCrystal, $returnDeuterium, 0);
        $planet->addResources($resources);

        // Clear scrap session
        $request->session()->forget('scrap_merchant_' . $planet->getPlanetId());

        // Pick a random merchant response message
        $merchantMessages = [
            'Okay, thanks, bye, next!',
            'Doing business with you is going to ruin me!',
            'There\'d be a few percent more were it not for the bullet holes.',
        ];
        $randomMessage = $merchantMessages[array_rand($merchantMessages)];

        return response()->json([
            'success' => true,
            'message' => $randomMessage,
            'returned' => [
                'metal' => $returnMetal,
                'crystal' => $returnCrystal,
                'deuterium' => $returnDeuterium,
            ],
        ]);
    }
}
