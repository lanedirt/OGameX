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
            '/highscore',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);

            try {
                $response->assertStatus(200);
            } catch (\PHPUnit\Framework\AssertionFailedError $e) {
                $customMessage = 'Main page "' . $route . '" does not return HTTP 200.';
                // Include HTML error message (first 2k chars)
                $htmlContent = $response->getContent();
                if (!empty($htmlContent)) {
                    $customMessage .= "\nResponse: " . substr($htmlContent, 0, 2000);
                }
                $this->fail($customMessage);
            }
        }
    }

    /**
     * Verify that all AJAX resources pages return HTTP 200.
     */
    public function testAjaxResources(): void
    {
        foreach (ObjectService::getBuildingObjects() as $object) {
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
        foreach (ObjectService::getStationObjects() as $object) {
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
        foreach (ObjectService::getResearchObjects() as $object) {
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
        foreach (ObjectService::getShipObjects() as $object) {
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
        foreach (ObjectService::getDefenseObjects() as $object) {
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
     * Verify that all AJAX techtree pages return HTTP 200.
     */
    public function testAjaxTechtree(): void
    {
        foreach (ObjectService::getObjects() as $object) {
            $response = $this->get('ajax/techtree?tab=1&object_id=' . $object->id);

            try {
                $response->assertStatus(200);
            } catch (\PHPUnit\Framework\AssertionFailedError $e) {
                $customMessage = 'AJAX techtree popup for "' . $object->title . '" does not return HTTP 200.';
                // Optionally, include original message: $customMessage .= "\nOriginal assertion failure: " . $e->getMessage();
                $this->fail($customMessage);
            }
        }
    }

    /**
     * Verify that all AJAX applications pages return HTTP 200.
     */
    public function testAjaxApplications(): void
    {
        foreach (ObjectService::getObjects() as $object) {
            $response = $this->get('ajax/techtree?tab=2&object_id=' . $object->id);

            try {
                $response->assertStatus(200);
            } catch (\PHPUnit\Framework\AssertionFailedError $e) {
                $customMessage = 'AJAX techtree popup applications tab for "' . $object->title . '" does not return HTTP 200.';
                // Optionally, include original message: $customMessage .= "\nOriginal assertion failure: " . $e->getMessage();
                $this->fail($customMessage);
            }
        }
    }

    /**
     * Verify that all AJAX techinfo pages return HTTP 200.
     */
    public function testAjaxTechinfo(): void
    {
        foreach (ObjectService::getObjects() as $object) {
            $response = $this->get('ajax/techtree?tab=3&object_id=' . $object->id);

            try {
                $response->assertStatus(200);
            } catch (\PHPUnit\Framework\AssertionFailedError $e) {
                $customMessage = 'AJAX techtree popup techinfo tab for "' . $object->title . '" does not return HTTP 200.';
                // Optionally, include original message: $customMessage .= "\nOriginal assertion failure: " . $e->getMessage();
                $this->fail($customMessage);
            }
        }
    }

    /**
     * Verify that all AJAX technology pages return HTTP 200.
     */
    public function testAjaxTechnology(): void
    {
        foreach (ObjectService::getObjects() as $object) {
            $response = $this->get('ajax/techtree?tab=4&object_id=' . $object->id);

            try {
                $response->assertStatus(200);
            } catch (\PHPUnit\Framework\AssertionFailedError $e) {
                $customMessage = 'AJAX techtree popup technology tab for "' . $object->title . '" does not return HTTP 200.';
                // Optionally, include original message: $customMessage .= "\nOriginal assertion failure: " . $e->getMessage();
                $this->fail($customMessage);
            }
        }
    }
}
