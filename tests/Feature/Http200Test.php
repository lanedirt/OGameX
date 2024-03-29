<?php

namespace Tests\Feature;

use Illuminate\Support\Str;
use OGame\Services\ObjectService;
use Tests\TestCase;

/**
 * Test AJAX calls to make sure they work as expected.
 */
class Http200Test extends TestCase
{
    /**
     * Set up common test components.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create a new user and login so we can access ingame features
        $this->createAndLoginUser();
    }

    protected function createAndLoginUser() {
        $response = $this->get('/login');

        // Check for existence of register form
        $response->assertSee('subscribeForm');

        // Simulate form data
        // Generate random email
        $randomEmail = Str::random(10) . '@example.com';

        $formData = [
            '_token' => csrf_token(),
            'email' => $randomEmail,
            'password' => 'asdasdasd',
            'v' => '3',
            'step' => 'validate',
            'kid' => '',
            'errorCodeOn' => '1',
            'is_utf8' => '1',
            'agb' => 'on',
        ];

        // Submit the registration form
        $this->post('/register', $formData);

        // We should now automatically be logged in.
    }

    /**
     * Verify that all main pages return HTTP 200.
     */
    public function testMainPages(): void
    {
        // Get all GET routes defined in web.php
        $routes = [
            '/overview',
            '/resources',
            '/resources/settings',
            '/facilities',
            '/research',
            '/shipyard',
            '/defense',
            '/fleet',
            '/fleet/movement',
            '/galaxy',
            '/merchant',
            '/messages',
            '/alliance',
            '/premium',
            '/shop',
            '/buddies',
            '/rewards',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);

            try {
                $response->assertStatus(200);
            } catch (\PHPUnit\Framework\AssertionFailedError $e) {
                $customMessage = 'Main page "' . $route . '" does not return HTTP 200.';
                // Optionally, include original message: $customMessage .= "\nOriginal assertion failure: " . $e->getMessage();
                $this->fail($customMessage);
            }
        }
    }

    /**
     * Verify that all AJAX resource pages return HTTP 200.
     */
    public function testAjaxResources(): void
    {
        // Get all resource objects
        $objectService = new ObjectService();

        foreach ($objectService->getBuildingObjects() as $object) {
            $response = $this->get('ajax/resources?type=' . $object['id']);

            try {
                $response->assertStatus(200);
            } catch (\PHPUnit\Framework\AssertionFailedError $e) {
                $customMessage = 'AJAX resource page for "' . $object['title'] . '" does not return HTTP 200.';
                // Optionally, include original message: $customMessage .= "\nOriginal assertion failure: " . $e->getMessage();
                $this->fail($customMessage);
            }
        }
    }

    /**
     * Verify that all AJAX resource pages return HTTP 200.
     */
    public function testAjaxFacilities(): void
    {
        // Get all resource objects
        $objectService = new ObjectService();

        foreach ($objectService->getStationObjects() as $object) {
            $response = $this->get('ajax/facilities?type=' . $object['id']);

            try {
                $response->assertStatus(200);
            } catch (\PHPUnit\Framework\AssertionFailedError $e) {
                $customMessage = 'AJAX facilities page for "' . $object['title'] . '" does not return HTTP 200.';
                // Optionally, include original message: $customMessage .= "\nOriginal assertion failure: " . $e->getMessage();
                $this->fail($customMessage);
            }
        }
    }

    /**
     * Verify that all AJAX resource pages return HTTP 200.
     */
    public function testAjaxResearch(): void
    {
        // Get all resource objects
        $objectService = new ObjectService();

        foreach ($objectService->getResearchObjects() as $object) {
            $response = $this->get('ajax/research?type=' . $object['id']);

            try {
                $response->assertStatus(200);
            } catch (\PHPUnit\Framework\AssertionFailedError $e) {
                $customMessage = 'AJAX research page for "' . $object['title'] . '" does not return HTTP 200.';
                // Optionally, include original message: $customMessage .= "\nOriginal assertion failure: " . $e->getMessage();
                $this->fail($customMessage);
            }
        }
    }

    /**
     * Verify that all AJAX resource pages return HTTP 200.
     */
    public function testAjaxShipyard(): void
    {
        // Get all resource objects
        $objectService = new ObjectService();

        foreach ($objectService->getShipObjects() as $object) {
            $response = $this->get('ajax/shipyard?type=' . $object['id']);

            try {
                $response->assertStatus(200);
            } catch (\PHPUnit\Framework\AssertionFailedError $e) {
                $customMessage = 'AJAX shipyard page for "' . $object['title'] . '" does not return HTTP 200.';
                // Optionally, include original message: $customMessage .= "\nOriginal assertion failure: " . $e->getMessage();
                $this->fail($customMessage);
            }
        }
    }

    /**
     * Verify that all AJAX resource pages return HTTP 200.
     */
    public function testAjaxDefence(): void
    {
        // Get all resource objects
        $objectService = new ObjectService();

        foreach ($objectService->getDefenceObjects() as $object) {
            $response = $this->get('ajax/defense?type=' . $object['id']);

            try {
                $response->assertStatus(200);
            } catch (\PHPUnit\Framework\AssertionFailedError $e) {
                $customMessage = 'AJAX defence page for "' . $object['title'] . '" does not return HTTP 200.';
                // Optionally, include original message: $customMessage .= "\nOriginal assertion failure: " . $e->getMessage();
                $this->fail($customMessage);
            }
        }
    }
}
