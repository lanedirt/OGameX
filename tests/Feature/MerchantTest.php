<?php

namespace Tests\Feature;

use OGame\GameMessages\ExpeditionMerchantFound;
use OGame\Models\Resources;
use OGame\Services\MerchantService;
use OGame\Services\ObjectService;
use Tests\AccountTestCase;

/**
 * Test class for Merchant functionality.
 * Tests resource trader, scrap merchant, expedition bonuses, and storage capacity limits.
 */
class MerchantTest extends AccountTestCase
{
    /**
     * Test that merchant index page loads successfully.
     */
    public function testMerchantIndexPageLoads(): void
    {
        $response = $this->get('/merchant');
        $response->assertStatus(200);
        $response->assertSee('Resource Market');
        $response->assertSee('Scrap Merchant');
    }

    /**
     * Test that resource market page loads successfully.
     */
    public function testResourceMarketPageLoads(): void
    {
        $response = $this->get('/merchant/resource-market');
        $response->assertStatus(200);
        $response->assertSee('Exchange your resources');
        $response->assertSee('Call merchant');
    }

    /**
     * Test that scrap merchant page loads successfully.
     */
    public function testScrapMerchantPageLoads(): void
    {
        $response = $this->get('/merchant/scrap');
        $response->assertStatus(200);
        $response->assertSee('Scrap Merchant');
        $response->assertSee('Ships');
        $response->assertSee('Defensive structures');
    }

    // ==============================================
    // Resource Trader Tests
    // ==============================================

    /**
     * Test calling a merchant with sufficient dark matter.
     */
    public function testCallMerchantWithSufficientDarkMatter(): void
    {
        // Give player dark matter
        $player = $this->planetService->getPlayer();
        $player->getUser()->dark_matter = 10000;
        $player->save();

        $response = $this->post('/merchant/call', [
            'type' => 'metal',
            '_token' => csrf_token(),
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify dark matter was deducted
        $player->getUser()->refresh();
        $this->assertEquals(10000 - MerchantService::DARK_MATTER_COST, $player->getUser()->dark_matter);

        // Verify trade rates were generated
        $data = $response->json();
        $this->assertArrayHasKey('tradeRates', $data);
        $this->assertEquals('metal', $data['tradeRates']['give']);
        $this->assertArrayHasKey('crystal', $data['tradeRates']['receive']);
        $this->assertArrayHasKey('deuterium', $data['tradeRates']['receive']);
    }

    /**
     * Test calling a merchant without sufficient dark matter.
     */
    public function testCallMerchantWithoutSufficientDarkMatter(): void
    {
        // Set dark matter to less than cost
        $this->planetService->getPlayer()->getUser()->dark_matter = 1000;
        $this->planetService->getPlayer()->save();

        $response = $this->post('/merchant/call', [
            'type' => 'crystal',
            '_token' => csrf_token(),
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => false]);

        // Verify dark matter was NOT deducted
        $this->planetService->getPlayer()->getUser()->refresh();
        $this->assertEquals(1000, $this->planetService->getPlayer()->getUser()->dark_matter);
    }

    /**
     * Test calling merchant with invalid type.
     */
    public function testCallMerchantWithInvalidType(): void
    {
        $player = $this->planetService->getPlayer();
        $player->getUser()->dark_matter = 10000;
        $player->save();

        $response = $this->post('/merchant/call', [
            'type' => 'invalid_resource',
            '_token' => csrf_token(),
        ]);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
        $this->assertStringContainsString('Invalid merchant type', $response->json('message'));
    }

    /**
     * Test executing a valid trade with sufficient resources.
     */
    public function testExecuteTradeWithSufficientResources(): void
    {
        // Call merchant first
        $this->planetService->getPlayer()->getUser()->dark_matter = 10000;
        $this->planetService->getPlayer()->save();

        $callResponse = $this->post('/merchant/call', [
            'type' => 'metal',
            '_token' => csrf_token(),
        ]);

        $tradeRates = $callResponse->json()['tradeRates'];
        $exchangeRate = $tradeRates['receive']['crystal']['rate'];

        // Give planet resources
        $this->planetService->addResources(new Resources(100000, 0, 0, 0));
        $this->planetService->save();

        // Calculate a safe trade amount that won't exceed storage
        // With rates between 1.40-2.00, trading 5000 metal = 7000-10000 crystal (within 10k storage)
        $giveAmount = 5000;

        // Execute trade
        $response = $this->post('/merchant/trade', [
            'give_resource' => 'metal',
            'receive_resource' => 'crystal',
            'give_amount' => $giveAmount,
            'exchange_rate' => $exchangeRate,
            '_token' => csrf_token(),
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify resources were traded
        $this->planetService->reloadPlanet();
        $this->assertLessThan(100000, $this->planetService->metal()->get());
        $this->assertGreaterThan(0, $this->planetService->crystal()->get());
    }

    /**
     * Test executing trade without sufficient resources.
     */
    public function testExecuteTradeWithoutSufficientResources(): void
    {
        // Call merchant first
        $this->planetService->getPlayer()->getUser()->dark_matter = 10000;
        $this->planetService->getPlayer()->save();

        $callResponse = $this->post('/merchant/call', [
            'type' => 'deuterium',
            '_token' => csrf_token(),
        ]);

        $tradeRates = $callResponse->json()['tradeRates'];
        $exchangeRate = $tradeRates['receive']['metal']['rate'];

        // Give planet minimal resources
        $this->planetService->addResources(new Resources(0, 0, 100, 0));
        $this->planetService->save();

        // Try to trade more than available
        $response = $this->post('/merchant/trade', [
            'give_resource' => 'deuterium',
            'receive_resource' => 'metal',
            'give_amount' => 10000,
            'exchange_rate' => $exchangeRate,
            '_token' => csrf_token(),
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => false]);
    }

    /**
     * Test that trade respects storage capacity limits.
     * This is critical - players should NEVER exceed storage capacity.
     */
    public function testTradeRespectsStorageCapacity(): void
    {
        // Call merchant first
        $this->planetService->getPlayer()->getUser()->dark_matter = 10000;
        $this->planetService->getPlayer()->save();

        $callResponse = $this->post('/merchant/call', [
            'type' => 'metal',
            '_token' => csrf_token(),
        ]);

        $tradeRates = $callResponse->json()['tradeRates'];
        $exchangeRate = $tradeRates['receive']['crystal']['rate'];

        // Fill crystal storage almost to capacity
        $crystalStorageCapacity = $this->planetService->crystalStorage()->get();
        $this->planetService->addResources(new Resources(100000, $crystalStorageCapacity - 1000, 0, 0));
        $this->planetService->save();

        // Try to trade for more crystal than storage can hold
        $response = $this->post('/merchant/trade', [
            'give_resource' => 'metal',
            'receive_resource' => 'crystal',
            'give_amount' => 50000,
            'exchange_rate' => $exchangeRate,
            '_token' => csrf_token(),
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => false]);
        $this->assertStringContainsString('Not enough storage capacity', $response->json('message'));

        // Verify crystal didn't exceed capacity
        $this->planetService->reloadPlanet();
        $this->assertLessThanOrEqual($crystalStorageCapacity, $this->planetService->crystal()->get());
    }

    /**
     * Test dismissing a merchant.
     */
    public function testDismissMerchant(): void
    {
        // Call merchant first
        $this->planetService->getPlayer()->getUser()->dark_matter = 10000;
        $this->planetService->getPlayer()->save();

        $this->post('/merchant/call', [
            'type' => 'metal',
            '_token' => csrf_token(),
        ]);

        // Dismiss merchant
        $response = $this->post('/merchant/dismiss', [
            '_token' => csrf_token(),
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    // ==============================================
    // Scrap Merchant Tests
    // ==============================================

    /**
     * Test scrapping ships with base offer.
     */
    public function testScrapShipsWithBaseOffer(): void
    {
        // Add ships to planet
        $smallCargoObject = ObjectService::getObjectByMachineName('small_cargo');
        $this->planetService->addUnit('small_cargo', 10);
        $this->planetService->save();

        // Scrap 5 small cargo ships
        $response = $this->post('/merchant/scrap/execute', [
            'items' => [
                $smallCargoObject->id => 5,
            ],
            'confirmed' => true,
            '_token' => csrf_token(),
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify ships were removed
        $this->planetService->reloadPlanet();
        $this->assertEquals(5, $this->planetService->getObjectAmount('small_cargo'));

        // Verify resources were added (35% of cost)
        $returned = $response->json()['returned'];
        $expectedMetal = (int)floor($smallCargoObject->price->resources->metal->get() * 5 * 0.35);
        $this->assertEquals($expectedMetal, $returned['metal']);
    }

    /**
     * Test scrapping defense units.
     */
    public function testScrapDefenseUnits(): void
    {
        // Add defense to planet
        $rocketLauncherObject = ObjectService::getObjectByMachineName('rocket_launcher');
        $this->planetService->addUnit('rocket_launcher', 20);
        $this->planetService->save();

        // Scrap 10 rocket launchers
        $response = $this->post('/merchant/scrap/execute', [
            'items' => [
                $rocketLauncherObject->id => 10,
            ],
            'confirmed' => true,
            '_token' => csrf_token(),
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify defense was removed
        $this->planetService->reloadPlanet();
        $this->assertEquals(10, $this->planetService->getObjectAmount('rocket_launcher'));
    }

    /**
     * Test that scrap merchant respects storage capacity limits.
     * This is critical - players should NEVER exceed storage capacity.
     */
    public function testScrapRespectsStorageCapacity(): void
    {
        // Add many ships
        $this->planetService->addUnit('small_cargo', 1000);
        $this->planetService->save();

        // Fill metal storage to near capacity
        $metalStorageCapacity = $this->planetService->metalStorage()->get();
        $this->planetService->addResources(new Resources($metalStorageCapacity - 1000, 0, 0, 0));
        $this->planetService->save();

        $smallCargoObject = ObjectService::getObjectByMachineName('small_cargo');

        // Try to scrap ships (should return error with adjusted amounts)
        $response = $this->post('/merchant/scrap/execute', [
            'items' => [
                $smallCargoObject->id => 1000,
            ],
            'confirmed' => true,
            '_token' => csrf_token(),
        ]);

        // Should return error with needsConfirmation flag
        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'needsConfirmation' => true
        ]);

        // Should have warnings and adjusted amounts
        $data = $response->json();
        $this->assertArrayHasKey('warnings', $data);
        $this->assertArrayHasKey('adjustedItems', $data);

        // Adjusted amount should be less than requested
        $adjustedAmount = $data['adjustedItems'][$smallCargoObject->id] ?? 0;
        $this->assertLessThan(1000, $adjustedAmount);

        // Verify ships were NOT consumed
        $this->planetService->reloadPlanet();
        $this->assertEquals(1000, $this->planetService->getObjectAmount('small_cargo'));
    }

    /**
     * Test bargaining with scrap merchant.
     */
    public function testScrapMerchantBargain(): void
    {
        // Give player dark matter
        $this->planetService->getPlayer()->getUser()->dark_matter = 10000;
        $this->planetService->getPlayer()->save();

        // Bargain (first bargain costs 2000 DM)
        $response = $this->post('/merchant/scrap/bargain', [
            '_token' => csrf_token(),
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify dark matter was deducted (10000 - 2000 = 8000)
        $this->planetService->getPlayer()->getUser()->refresh();
        $this->assertEquals(8000, $this->planetService->getPlayer()->getUser()->dark_matter);

        // Verify offer increased
        $data = $response->json();
        $this->assertGreaterThan(35, $data['newPercentage']);
        $this->assertLessThanOrEqual(75, $data['newPercentage']);
    }

    /**
     * Test bargaining without sufficient dark matter.
     */
    public function testScrapMerchantBargainWithoutSufficientDarkMatter(): void
    {
        // Give player insufficient dark matter
        $this->planetService->getPlayer()->getUser()->dark_matter = 1000;
        $this->planetService->getPlayer()->save();

        $response = $this->post('/merchant/scrap/bargain', [
            '_token' => csrf_token(),
        ]);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
    }

    /**
     * Test bargaining cost increases correctly.
     */
    public function testScrapMerchantBargainCostIncrease(): void
    {
        // Give player plenty of dark matter
        $this->planetService->getPlayer()->getUser()->dark_matter = 50000;
        $this->planetService->getPlayer()->save();

        // First bargain - 2000 DM
        $response1 = $this->post('/merchant/scrap/bargain', ['_token' => csrf_token()]);
        $response1->assertJson(['success' => true]);
        $this->assertEquals(4000, $response1->json()['newCost']); // Next cost is 4000

        // Second bargain - 4000 DM
        $response2 = $this->post('/merchant/scrap/bargain', ['_token' => csrf_token()]);
        $response2->assertJson(['success' => true]);
        $this->assertEquals(6000, $response2->json()['newCost']); // Next cost is 6000

        // Verify total DM spent: 2000 + 4000 = 6000
        $this->planetService->getPlayer()->getUser()->refresh();
        $this->assertEquals(44000, $this->planetService->getPlayer()->getUser()->dark_matter);
    }

    /**
     * Test that bargaining caps at 75%.
     */
    public function testScrapMerchantBargainCapsAt75Percent(): void
    {
        // Give player lots of dark matter
        $this->planetService->getPlayer()->getUser()->dark_matter = 200000;
        $this->planetService->getPlayer()->save();

        // Bargain multiple times to reach cap
        $maxAttempts = 20;
        $finalPercentage = 35;

        for ($i = 0; $i < $maxAttempts; $i++) {
            $response = $this->post('/merchant/scrap/bargain', ['_token' => csrf_token()]);

            if ($response->status() === 400) {
                // Already at max
                break;
            }

            $finalPercentage = $response->json()['newPercentage'];

            if ($finalPercentage >= 75) {
                break;
            }
        }

        // Verify we're at or below cap
        $this->assertLessThanOrEqual(75, $finalPercentage);
    }

    /**
     * Test scrapping without selecting any items.
     */
    public function testScrapWithoutSelectingItems(): void
    {
        $response = $this->post('/merchant/scrap/execute', [
            'items' => [],
            'confirmed' => true,
            '_token' => csrf_token(),
        ]);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
    }

    /**
     * Test scrapping more units than player has.
     */
    public function testScrapMoreUnitsThanAvailable(): void
    {
        // Add only 5 ships
        $smallCargoObject = ObjectService::getObjectByMachineName('small_cargo');
        $this->planetService->addUnit('small_cargo', 5);
        $this->planetService->save();

        // Try to scrap 10
        $response = $this->post('/merchant/scrap/execute', [
            'items' => [
                $smallCargoObject->id => 10,
            ],
            'confirmed' => true,
            '_token' => csrf_token(),
        ]);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
    }

    /**
     * Test that scrap returns error with adjusted amounts when storage is insufficient.
     * Should NOT complete the scrap, but return adjusted amounts and warnings.
     */
    public function testScrapReturnsAdjustedAmountsForInsufficientStorage(): void
    {
        // Add many ships
        $smallCargoObject = ObjectService::getObjectByMachineName('small_cargo');
        $this->planetService->addUnit('small_cargo', 1000);
        $this->planetService->save();

        // Fill metal storage almost to capacity
        $metalStorageCapacity = $this->planetService->metalStorage()->get();
        $this->planetService->addResources(new Resources($metalStorageCapacity - 5000, 0, 0, 0));
        $this->planetService->save();

        // Try to scrap all 1000 ships (which would exceed storage)
        $response = $this->post('/merchant/scrap/execute', [
            'items' => [
                $smallCargoObject->id => 1000,
            ],
            'confirmed' => true,
            '_token' => csrf_token(),
        ]);

        // Should fail with 400 error and needsConfirmation flag
        $response->assertStatus(400);
        $response->assertJson(['success' => false, 'needsConfirmation' => true]);

        // Verify warnings were returned
        $data = $response->json();
        $this->assertArrayHasKey('warnings', $data);
        $this->assertNotEmpty($data['warnings'], 'Should have warnings about storage reduction');

        // Verify adjustedItems were returned
        $this->assertArrayHasKey('adjustedItems', $data);

        // Verify the warning contains item details
        $warning = $data['warnings'][0];
        $this->assertEquals($smallCargoObject->id, $warning['itemId']);
        $this->assertEquals($smallCargoObject->title, $warning['itemName']);
        $this->assertEquals(1000, $warning['requestedAmount']);
        $this->assertLessThan(1000, $warning['adjustedAmount']);

        // Verify ships were NOT consumed (operation was cancelled)
        $this->planetService->reloadPlanet();
        $this->assertEquals(1000, $this->planetService->getObjectAmount('small_cargo'));
    }

    /**
     * Test that scrap returns error when storage is completely full.
     */
    public function testScrapReturnsErrorWhenStorageCompletelyFull(): void
    {
        // Add ships
        $smallCargoObject = ObjectService::getObjectByMachineName('small_cargo');
        $this->planetService->addUnit('small_cargo', 100);
        $this->planetService->save();

        // Fill storage completely
        $metalStorageCapacity = $this->planetService->metalStorage()->get();
        $this->planetService->addResources(new Resources($metalStorageCapacity, 0, 0, 0));
        $this->planetService->save();

        // Try to scrap ships with full storage
        $response = $this->post('/merchant/scrap/execute', [
            'items' => [
                $smallCargoObject->id => 100,
            ],
            'confirmed' => true,
            '_token' => csrf_token(),
        ]);

        // Should fail with error and needsConfirmation flag
        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'needsConfirmation' => true
        ]);

        // Should have warnings and adjusted amounts
        $data = $response->json();
        $this->assertArrayHasKey('warnings', $data);
        $this->assertArrayHasKey('adjustedItems', $data);

        // Adjusted amount should be significantly reduced (less than requested)
        $adjustedAmount = $data['warnings'][0]['adjustedAmount'];
        $this->assertLessThan(100, $adjustedAmount);

        // Verify ships were NOT consumed
        $this->planetService->reloadPlanet();
        $this->assertEquals(100, $this->planetService->getObjectAmount('small_cargo'));
    }

    // ==============================================
    // Expedition Merchant Bonus Tests
    // ==============================================

    /**
     * Test adding expedition merchant calls a resource trader.
     * Expeditions ONLY call resource traders (metal/crystal/deuterium), never scrap merchants.
     */
    public function testAddExpeditionMerchantCallsResourceTrader(): void
    {
        $player = $this->planetService->getPlayer();

        // No active merchant initially
        $this->assertNull(cache()->get('active_merchant_' . $player->getId()));

        // Add expedition bonus - should call a random resource trader
        $result = MerchantService::addExpeditionBonus($player);

        // Verify a new merchant was called
        $this->assertTrue($result['called_new']);
        $this->assertContains($result['merchant_type'], ['metal', 'crystal', 'deuterium']);

        // Verify merchant is now active in cache
        $activeMerchant = cache()->get('active_merchant_' . $player->getId());
        // @phpstan-ignore-next-line - PHPStan doesn't understand cache()->get() can return non-null values
        $this->assertNotNull($activeMerchant, 'Merchant should be active in cache');
        $this->assertEquals($result['merchant_type'], $activeMerchant['type']);
        $this->assertArrayHasKey('trade_rates', $activeMerchant);
    }

    /**
     * Test expedition merchant only calls resource traders, never scrap merchant.
     */
    public function testExpeditionMerchantNeverCallsScrapMerchant(): void
    {
        $player = $this->planetService->getPlayer();

        // Call expedition merchant multiple times to verify it's always a resource trader
        for ($i = 0; $i < 10; $i++) {
            // Clear cache
            cache()->forget('active_merchant_' . $player->getId());

            $result = MerchantService::addExpeditionBonus($player);

            // Should always be a resource trader
            $this->assertContains(
                $result['merchant_type'],
                ['metal', 'crystal', 'deuterium'],
                'Expedition merchant should only call resource traders, never scrap merchant'
            );
        }
    }

    /**
     * Test that ExpeditionMerchantFound message exists.
     */
    public function testExpeditionMerchantFoundMessageExists(): void
    {
        // Verify the message class exists and has correct structure
        $this->assertTrue(class_exists(ExpeditionMerchantFound::class));

        // Verify translations exist
        $message1 = __('t_messages.expedition_merchant_found.body.1');
        $message2 = __('t_messages.expedition_merchant_found.body.2');
        $message3 = __('t_messages.expedition_merchant_found.body.3');

        $this->assertNotEquals('t_messages.expedition_merchant_found.body.1', $message1);
        $this->assertNotEquals('t_messages.expedition_merchant_found.body.2', $message2);
        $this->assertNotEquals('t_messages.expedition_merchant_found.body.3', $message3);
    }

    /**
     * Test expedition merchant improves rates when merchant already active.
     * When finding a merchant on expedition with one already active, the resource type
     * stays the same but exchange rates can improve (they never worsen).
     */
    public function testExpeditionMerchantImprovesExistingMerchantRates(): void
    {
        $player = $this->planetService->getPlayer();

        // Call a merchant first
        $player->getUser()->dark_matter = 10000;
        $player->save();

        $callResponse = $this->post('/merchant/call', [
            'type' => 'metal',
            '_token' => csrf_token(),
        ]);

        $originalRates = $callResponse->json()['tradeRates'];

        // Mock cache to ensure we have control over rates
        cache()->forever('active_merchant_' . $player->getId(), [
            'type' => 'metal',
            'trade_rates' => $originalRates,
            'called_at' => time(),
        ]);

        // Simulate finding a merchant on expedition
        // Note: This might improve rates or keep them the same, but never worsen
        $result = MerchantService::addExpeditionBonus($player);

        // Verify the merchant type stayed the same
        $this->assertEquals('metal', $result['merchant_type']);

        // Get updated cache
        $activeMerchant = cache()->get('active_merchant_' . $player->getId());
        $this->assertNotNull($activeMerchant);
        $this->assertEquals('metal', $activeMerchant['type']);

        // Verify rates didn't worsen (either improved or stayed same)
        foreach ($originalRates['receive'] as $resource => $originalRateData) {
            $newRate = $activeMerchant['trade_rates']['receive'][$resource]['rate'];
            $originalRate = $originalRateData['rate'];
            $this->assertGreaterThanOrEqual(
                $originalRate,
                $newRate,
                "Rate for $resource should not worsen (was $originalRate, now $newRate)"
            );
        }
    }

    /**
     * Test expedition merchant when no merchant is active calls a new one.
     */
    public function testExpeditionMerchantWithNoActiveMerchantCallsNew(): void
    {
        $player = $this->planetService->getPlayer();

        // No active merchant
        $this->assertNull(cache()->get('active_merchant_' . $player->getId()));

        // Add expedition bonus - should call a new merchant
        $result = MerchantService::addExpeditionBonus($player);

        // Should call a new merchant, not improve rates
        $this->assertFalse($result['improved']);
        $this->assertTrue($result['called_new']);
        $this->assertContains($result['merchant_type'], ['metal', 'crystal', 'deuterium']);

        // Verify merchant is now active
        $activeMerchant = cache()->get('active_merchant_' . $player->getId());
        // @phpstan-ignore-next-line - PHPStan doesn't understand cache()->get() can return non-null values
        $this->assertNotNull($activeMerchant, 'Merchant should be active in cache');
        $this->assertEquals($result['merchant_type'], $activeMerchant['type']);
    }

    /**
     * Test calling merchant with Dark Matter replaces existing merchant.
     * When calling a new merchant with Dark Matter while one is already active,
     * the old merchant is replaced by the new one.
     */
    public function testCallingMerchantWithDarkMatterReplacesExisting(): void
    {
        $player = $this->planetService->getPlayer();
        $player->getUser()->dark_matter = 20000;
        $player->save();

        // Call first merchant (metal)
        $response1 = $this->post('/merchant/call', [
            'type' => 'metal',
            '_token' => csrf_token(),
        ]);

        $this->assertEquals('metal', $response1->json()['tradeRates']['give']);

        // Call second merchant (crystal) - should replace first
        $response2 = $this->post('/merchant/call', [
            'type' => 'crystal',
            '_token' => csrf_token(),
        ]);

        $this->assertEquals('crystal', $response2->json()['tradeRates']['give']);

        // Verify only crystal merchant is active
        $activeMerchant = cache()->get('active_merchant_' . $player->getId());
        $this->assertEquals('crystal', $activeMerchant['type']);
    }
}
