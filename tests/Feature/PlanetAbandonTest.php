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
        $player = $this->planetService->getPlayer();
        if ($player === null) {
            $this->fail('Player is null.');
        }
        $startPlanetCount = $player->planets->planetCount();
        $this->assertGreaterThanOrEqual(2, $startPlanetCount);

        // Attempt to abandon the second planet.
        $response = $this->post('/ajax/planet-abandon/abandon', [
            '_token' => csrf_token(),
            'password' => 'password',
        ]);
        $response->assertStatus(200);
        $this->assertStringContainsString('Planet has been abandoned successfully!', (string)$response->getContent());

        // Reload player to get updated planet count.
        $player->load($player->getId());
        // Check that the user now has one less planet.
        $this->assertEquals($startPlanetCount - 1, $player->planets->planetCount());
    }

    /**
     * Check that abandoning the only remaining planet fails.
     */
    public function testFirstPlanetAbandonFail(): void
    {
        // Check that the user has at least two planets.
        $player = $this->planetService->getPlayer();
        if ($player === null) {
            $this->fail('Player is null.');
        }
        $startPlanetCount = $player->planets->planetCount();
        if ($startPlanetCount >= 2) {
            // Abandon all planets except the first one.
            foreach ($player->planets->all() as $planet) {
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
        $player->load($player->getId());
        // Check that the user now has only one planet.
        $this->assertEquals(1, $player->planets->planetCount());

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
