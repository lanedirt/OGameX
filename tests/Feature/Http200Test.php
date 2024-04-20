<?php

namespace Tests\Feature;

use OGame\Services\ObjectService;
use Tests\AccountTestCase;

/**
 * Test AJAX calls to make sure they work as expected.
 */
class Http200Test extends AccountTestCase
{
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
     * Verify that all AJAX resources pages return HTTP 200.
     */
    public function testAjaxResources(): void
    {
        // Get all resource objects
        $objectService = new ObjectService();

        foreach ($objectService->getBuildingObjects() as $object) {
            $response = $this->get('ajax/resources?technology=' . $object->id);

            try {
                $response->assertStatus(200);
            } catch (\PHPUnit\Framework\AssertionFailedError $e) {
                $customMessage = 'AJAX resource page for "' . $object->title . '" does not return HTTP 200.';
                // Optionally, include original message:
                $customMessage .= "\nOriginal assertion failure: " . $e->getMessage();
                $this->fail($customMessage);
            }
        }
    }

    /**
     * Verify that all AJAX facilities pages return HTTP 200.
     */
    public function testAjaxFacilities(): void
    {
        // Get all resource objects
        $objectService = new ObjectService();

        foreach ($objectService->getStationObjects() as $object) {
            $response = $this->get('ajax/facilities?technology=' . $object->id);

            try {
                $response->assertStatus(200);
            } catch (\PHPUnit\Framework\AssertionFailedError $e) {
                $customMessage = 'AJAX facilities page for "' . $object->title . '" does not return HTTP 200.';
                // Optionally, include original message: $customMessage .= "\nOriginal assertion failure: " . $e->getMessage();
                $this->fail($customMessage);
            }
        }
    }

    /**
     * Verify that all AJAX research pages return HTTP 200.
     */
    public function testAjaxResearch(): void
    {
        // Get all resource objects
        $objectService = new ObjectService();

        foreach ($objectService->getResearchObjects() as $object) {
            $response = $this->get('ajax/research?technology=' . $object->id);

            try {
                $response->assertStatus(200);
            } catch (\PHPUnit\Framework\AssertionFailedError $e) {
                $customMessage = 'AJAX research page for "' . $object->title . '" does not return HTTP 200.';
                // Optionally, include original message: $customMessage .= "\nOriginal assertion failure: " . $e->getMessage();
                $this->fail($customMessage);
            }
        }
    }

    /**
     * Verify that all AJAX shipyard pages return HTTP 200.
     */
    public function testAjaxShipyard(): void
    {
        // Get all resource objects
        $objectService = new ObjectService();

        foreach ($objectService->getShipObjects() as $object) {
            $response = $this->get('ajax/shipyard?technology=' . $object->id);

            try {
                $response->assertStatus(200);
            } catch (\PHPUnit\Framework\AssertionFailedError $e) {
                $customMessage = 'AJAX shipyard page for "' . $object->title . '" does not return HTTP 200.';
                // Optionally, include original message: $customMessage .= "\nOriginal assertion failure: " . $e->getMessage();
                $this->fail($customMessage);
            }
        }
    }

    /**
     * Verify that all AJAX defense pages return HTTP 200.
     */
    public function testAjaxDefense(): void
    {
        // Get all resource objects
        $objectService = new ObjectService();

        foreach ($objectService->getDefenseObjects() as $object) {
            $response = $this->get('ajax/defense?technology=' . $object->id);

            try {
                $response->assertStatus(200);
            } catch (\PHPUnit\Framework\AssertionFailedError $e) {
                $customMessage = 'AJAX defense page for "' . $object->title . '" does not return HTTP 200.';
                // Optionally, include original message: $customMessage .= "\nOriginal assertion failure: " . $e->getMessage();
                $this->fail($customMessage);
            }
        }
    }

    /**
     * Verify that techtree pages for all objects return HTTP 200.
     */
    public function testTechtreePopups(): void
    {
        // Get all objects
        $objectService = new ObjectService();

        foreach ($objectService->getObjects() as $object) {
            $response = $this->get('ajax/techtree?tab=2&object_id=' . $object->id);

            try {
                $response->assertStatus(200);
            } catch (\PHPUnit\Framework\AssertionFailedError $e) {
                $customMessage = 'AJAX techtree page for "' . $object->title . '" does not return HTTP 200.';
                // Optionally, include original message: $customMessage .= "\nOriginal assertion failure: " . $e->getMessage();
                $this->fail($customMessage);
            }
        }
    }
}
