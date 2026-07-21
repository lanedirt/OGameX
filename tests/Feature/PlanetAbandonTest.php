<?php

namespace Tests\Feature;

use Tests\AccountTestCase;

class PlanetAbandonTest extends AccountTestCase
{
    /**
     * Check that abandoning a second planet works as expected.
     */
    public function testSecondPlanetAbandon(): void
    {
        // Check that the user has at least two planets.
        $startPlanetCount = $this->planetService->getPlayer()->planets->planetCount();
        $this->assertGreaterThanOrEqual(2, $startPlanetCount);
        $this->assertNotNull($this->secondPlanetService);
        $abandonedPlanetId = $this->secondPlanetService->getPlanetId();

        // Switch to second planet then abandon it.
        $this->get('/overview?cp=' . $abandonedPlanetId);
        $response = $this->post('/ajax/planet-abandon/abandon', [
            '_token' => csrf_token(),
            'password' => 'password',
        ]);
        $response->assertStatus(200);
        $this->assertStringContainsString('Planet has been abandoned successfully!', (string)$response->getContent());

        // Soft-delete: row remains with destroyed flag.
        $abandonedRow = \OGame\Models\Planet::find($abandonedPlanetId);
        $this->assertNotNull($abandonedRow);
        $this->assertGreaterThan(0, (int) $abandonedRow->destroyed);

        // Reload player to get updated planet count.
        $this->planetService->getPlayer()->load($this->planetService->getPlayer()->getId());
        // Check that the user now has one less planet.
        $this->assertEquals($startPlanetCount - 1, $this->planetService->getPlayer()->planets->planetCount());
    }

    /**
     * Check that abandoning the only remaining planet fails.
     */
    public function testFirstPlanetAbandonFail(): void
    {
        // Check that the user has at least two planets.
        $startPlanetCount = $this->planetService->getPlayer()->planets->planetCount();
        if ($startPlanetCount >= 2) {
            // Abandon all planets except the first one.
            foreach ($this->planetService->getPlayer()->planets->all() as $planet) {
                if ($planet->getPlanetId() !== $this->planetService->getPlanetId()) {
                    $response = $this->post('/ajax/planet-abandon/abandon', [
                        '_token' => csrf_token(),
                        'password' => 'password',
                    ]);
                    $response->assertStatus(200);
                    $this->assertStringContainsString('Planet has been abandoned successfully!', (string)$response->getContent());
                }
            }
        }

        // Reload player to get updated planet count.
        $this->planetService->getPlayer()->load($this->planetService->getPlayer()->getId());
        // Check that the user now has only one planet.
        $this->assertEquals(1, $this->planetService->getPlayer()->planets->planetCount());

        // Attempt to abandon the only remaining planet.
        $response = $this->post('/ajax/planet-abandon/abandon', [
            '_token' => csrf_token(),
            'password' => 'password',
        ]);

        // Assert that response is HTTP 200 but contains error message.
        $response->assertStatus(200);
        $this->assertStringContainsString('Cannot abandon only remaining planet', (string)$response->getContent());
    }
}
