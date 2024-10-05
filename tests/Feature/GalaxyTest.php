<?php

namespace Tests\Feature;

use Tests\AccountTestCase;
use OGame\Services\DebrisFieldService;
use OGame\Models\Resources;

/**
 * Test that the galaxy page works as expected.
 */
class GalaxyTest extends AccountTestCase
{
    /**
     * Verify that debris fields are shown on the galaxy page.
     */
    public function testGalaxyDebrisFieldShown(): void
    {
        $coordinates = $this->planetService->getPlanetCoordinates();

        // Add debris field to the current planet.
        $debrisField = resolve(DebrisFieldService::class);
        $debrisField->loadOrCreateForCoordinates($coordinates);
        $debrisField->appendResources(new Resources(5555, 6666, 7777, 0));
        $debrisField->save();

        // Request overview page first to make sure the csrf_token is set.
        $response = $this->get('/');

        // Request the galaxy page AJAX call for current coordinates.
        // POST to http://localhost/ajax/galaxy with data:
        // galaxy = current galaxy
        // system = current system
        $response = $this->post('ajax/galaxy', [
            '_token' => csrf_token(),
            'galaxy' => $coordinates->galaxy,
            'system' => $coordinates->system,
        ]);

        $responseContent = $response->getContent();
        if ($response->status() !== 200 || ($responseContent !== false && stristr($responseContent, 'debris_1') === false)) {
            // Include HTML error message (first 2k chars)
            $customMessage = 'Galaxy page does not contain debris field.';
            if (!empty($responseContent)) {
                $customMessage .= "\nGalaxy ajax response: " . substr($responseContent, 0, 2000);
            }
            $this->fail($customMessage);
        }

        // Check that the response contains the debris field by checking for the name and resource amounts.
        $response->assertSeeText('debris_1');
        $response->assertSeeText('5555');
        $response->assertSeeText('6666');
        $response->assertSeeText('7777');
    }
}
