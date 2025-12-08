<?php

namespace Tests\Feature;

use OGame\Services\PlayerService;
use Tests\AccountTestCase;

/**
 * Test options page functionality.
 */
class OptionsTest extends AccountTestCase
{
    /**
     * Test that options page loads successfully.
     */
    public function testOptionsPageLoads(): void
    {
        $response = $this->get('/options');
        $response->assertStatus(200);
        $response->assertSee('Options');
    }

    /**
     * Test that saving espionage probes amount persists the value.
     */
    public function testSaveEspionageProbesAmount(): void
    {
        $playerService = resolve(PlayerService::class, ['player_id' => $this->currentUserId]);

        // Initially should be null
        $this->assertNull($playerService->getEspionageProbesAmount());

        // Save a value
        $response = $this->post('/options', [
            'espionage_probes_amount' => '10',
            '_token' => csrf_token(),
        ]);

        $response->assertRedirect('/options');
        $response->assertSessionHas('success');

        // Reload application to ensure fresh data
        $this->reloadApplication();

        // Reload player service to get fresh data
        $playerService = resolve(PlayerService::class, ['player_id' => $this->currentUserId]);

        // Verify the value was saved
        $this->assertEquals(10, $playerService->getEspionageProbesAmount());
    }

    /**
     * Test that saving empty espionage probes amount clears the value.
     */
    public function testSaveEspionageProbesAmountEmpty(): void
    {
        $playerService = resolve(PlayerService::class, ['player_id' => $this->currentUserId]);

        // Set an initial value
        $playerService->setEspionageProbesAmount(10);
        $playerService->save();

        // Verify it's set
        $this->assertEquals(10, $playerService->getEspionageProbesAmount());

        // Save empty value
        $response = $this->post('/options', [
            'espionage_probes_amount' => '',
            '_token' => csrf_token(),
        ]);

        $response->assertRedirect('/options');

        // Reload application to ensure fresh data
        $this->reloadApplication();

        // Reload player service to get fresh data
        $playerService = resolve(PlayerService::class, ['player_id' => $this->currentUserId]);

        // Verify the value was cleared
        $this->assertNull($playerService->getEspionageProbesAmount());
    }

    /**
     * Test that saved espionage probes amount is displayed in the options view.
     */
    public function testEspionageProbesAmountDisplayedInView(): void
    {
        $playerService = resolve(PlayerService::class, ['player_id' => $this->currentUserId]);

        // Set a value
        $playerService->setEspionageProbesAmount(15);
        $playerService->save();

        // Load the options page
        $response = $this->get('/options');
        $response->assertStatus(200);

        // Check that the value is in the input field
        $response->assertSee('value="15"', false);
    }

    /**
     * Test that invalid espionage probes amount (less than 1) shows error.
     */
    public function testSaveEspionageProbesAmountInvalid(): void
    {
        $playerService = resolve(PlayerService::class, ['player_id' => $this->currentUserId]);

        // Try to save invalid value (0)
        $response = $this->post('/options', [
            'espionage_probes_amount' => '0',
            '_token' => csrf_token(),
        ]);

        $response->assertRedirect('/options');
        $response->assertSessionHas('error');

        // Reload application to ensure fresh data
        $this->reloadApplication();

        // Verify the value was not saved
        $playerService = resolve(PlayerService::class, ['player_id' => $this->currentUserId]);
        $this->assertNull($playerService->getEspionageProbesAmount());
    }
}
