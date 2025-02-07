<?php

namespace Tests\Feature;

use Tests\AccountTestCase;

/**
 * Class ShopPageTest
 *
 * This test ensures that the shop page loads correctly for an authenticated user.
 * Since this extends AccountTestCase, the user is already registered and logged in.
 */
class ShopPageTest extends AccountTestCase
{
    /**
     * Test that the shop page loads successfully for an authenticated user.
     *
     * @return void
     */
    public function testShopPageLoadsCorrectly(): void
    {
        // Since AccountTestCase sets up authentication, we can directly test the shop page.
        $response = $this->get('/shop');

        // Assert that the shop page returns an HTTP 200 OK response.
        $response->assertStatus(200);
    }
}