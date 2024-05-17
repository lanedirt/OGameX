<?php

namespace Feature;

use Tests\AccountTestCase;

class PlanetAbandonTest extends AccountTestCase
{
    /**
     * Check that abandoning a second planet works as expected.
     */
    public function testSecondPlanetAbandon(): void
    {
        // Check that the user has at least two planets.
        $startPlanetCount = $this->planetService->getPlayer()->planets->count();
        $this->assertGreaterThanOrEqual(2, $startPlanetCount);

        // Attempt to abandon the second planet.
        $response = $this->post('/ajax/planet-abandon/abandon', [
            '_token' => csrf_token(),
            'password' => 'password',
        ]);
        $response->assertStatus(200);
        $this->assertStringContainsString('Planet has been abandoned succesfully!', (string)$response->getContent());

        // Reload player to get updated planet count.
        $this->planetService->getPlayer()->load($this->planetService->getPlayer()->getId());
        // Check that the user now has one less planet.
        $this->assertEquals($startPlanetCount - 1, $this->planetService->getPlayer()->planets->count());
    }

    /**
     * Check that abandoning the only remaining planet fails.
     */
    public function testFirstPlanetAbandonFail(): void
    {
        // Check that the user has at least two planets.
        $startPlanetCount = $this->planetService->getPlayer()->planets->count();
        if ($startPlanetCount >= 2) {
            // Abandon all planets except the first one.
            foreach ($this->planetService->getPlayer()->planets->all() as $planet) {
                if ($planet->getPlanetId() !== $this->planetService->getPlanetId()) {
                    $response = $this->post('/ajax/planet-abandon/abandon', [
                        '_token' => csrf_token(),
                        'password' => 'password',
                    ]);
                    $response->assertStatus(200);
                    $this->assertStringContainsString('Planet has been abandoned succesfully!', (string)$response->getContent());
                }
            }
        }

        // Reload player to get updated planet count.
        $this->planetService->getPlayer()->load($this->planetService->getPlayer()->getId());
        // Check that the user now has only one planet.
        $this->assertEquals(1, $this->planetService->getPlayer()->planets->count());

        // Attempt to abandon the only remaining planet.
        $response = $this->post('/ajax/planet-abandon/abandon', [
            '_token' => csrf_token(),
            'password' => 'password',
        ]);
        $response->assertStatus(500);
    }
}
