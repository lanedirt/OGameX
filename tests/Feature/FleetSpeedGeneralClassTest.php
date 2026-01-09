<?php

namespace Tests\Feature;

use Tests\AccountTestCase;

class FleetSpeedGeneralClassTest extends AccountTestCase
{
    /**
     * Test that General class can use 5% fleet speed.
     */
    public function testGeneralClassCanUse5PercentFleetSpeed(): void
    {
        // Set user character class to General
        $user = $this->planetService->getPlayer()->getUser();
        $user->character_class = \OGame\Enums\CharacterClass::GENERAL->value;
        $user->save();

        // Add a ship to the planet
        $this->planetAddUnit('light_fighter', 1);

        // Test data for fleet dispatch with 5% speed
        $fleetData = [
            'galaxy' => 1,
            'system' => 2,
            'position' => 1,
            'type' => 1,
            'metal' => 0,
            'crystal' => 0,
            'deuterium' => 0,
            'mission' => 3, // Transport mission
            'speed' => 0.5, // 5% speed
            'holdingtime' => 0,
            'token' => csrf_token(),
            'am202' => ['light_fighter' => 1],
        ];

        // This should not throw a validation error for General class
        $response = $this->postJson('/ajax/fleet/dispatch/send-fleet', $fleetData);

        // Should not return validation error for speed
        $response->assertStatus(200);
        $response->assertJsonMissing(['errors' => ['Fleet speed must be between 10% and 100% in 5% increments.']]);
    }

    /**
     * Test that non-General class cannot use 5% fleet speed.
     */
    public function testNonGeneralClassCannotUse5PercentFleetSpeed(): void
    {
        // Ensure user is not General class (default is no class)
        $user = $this->planetService->getPlayer()->getUser();
        $user->character_class = null;
        $user->save();

        // Add a ship to the planet
        $this->planetAddUnit('light_fighter', 1);

        // Test data for fleet dispatch with 5% speed
        $fleetData = [
            'galaxy' => 1,
            'system' => 2,
            'position' => 1,
            'type' => 1,
            'metal' => 0,
            'crystal' => 0,
            'deuterium' => 0,
            'mission' => 3, // Transport mission
            'speed' => 0.5, // 5% speed
            'holdingtime' => 0,
            'token' => csrf_token(),
            'am202' => ['light_fighter' => 1],
        ];

        // This should throw a validation error for non-General class
        $response = $this->postJson('/ajax/fleet/dispatch/send-fleet', $fleetData);

        // Should return validation error for speed
        $response->assertStatus(200);
        $response->assertJsonFragment(['errors' => [['message' => 'Fleet speed must be between 10% and 100% in 5% increments.', 'error' => 140020]]]);
    }

    /**
     * Test that Collector class cannot use 5% fleet speed.
     */
    public function testCollectorClassCannotUse5PercentFleetSpeed(): void
    {
        // Set user character class to Collector
        $user = $this->planetService->getPlayer()->getUser();
        $user->character_class = \OGame\Enums\CharacterClass::COLLECTOR->value;
        $user->save();

        // Add a ship to the planet
        $this->planetAddUnit('light_fighter', 1);

        // Test data for fleet dispatch with 5% speed
        $fleetData = [
            'galaxy' => 1,
            'system' => 2,
            'position' => 1,
            'type' => 1,
            'metal' => 0,
            'crystal' => 0,
            'deuterium' => 0,
            'mission' => 3, // Transport mission
            'speed' => 0.5, // 5% speed
            'holdingtime' => 0,
            'token' => csrf_token(),
            'am202' => ['light_fighter' => 1],
        ];

        // This should throw a validation error for Collector class
        $response = $this->postJson('/ajax/fleet/dispatch/send-fleet', $fleetData);

        // Should return validation error for speed
        $response->assertStatus(200);
        $response->assertJsonFragment(['errors' => [['message' => 'Fleet speed must be between 10% and 100% in 5% increments.', 'error' => 140020]]]);
    }

    /**
     * Test that Discoverer class cannot use 5% fleet speed.
     */
    public function testDiscovererClassCannotUse5PercentFleetSpeed(): void
    {
        // Set user character class to Discoverer
        $user = $this->planetService->getPlayer()->getUser();
        $user->character_class = \OGame\Enums\CharacterClass::DISCOVERER->value;
        $user->save();

        // Add a ship to the planet
        $this->planetAddUnit('light_fighter', 1);

        // Test data for fleet dispatch with 5% speed
        $fleetData = [
            'galaxy' => 1,
            'system' => 2,
            'position' => 1,
            'type' => 1,
            'metal' => 0,
            'crystal' => 0,
            'deuterium' => 0,
            'mission' => 3, // Transport mission
            'speed' => 0.5, // 5% speed
            'holdingtime' => 0,
            'token' => csrf_token(),
            'am202' => ['light_fighter' => 1],
        ];

        // This should throw a validation error for Discoverer class
        $response = $this->postJson('/ajax/fleet/dispatch/send-fleet', $fleetData);

        // Should return validation error for speed
        $response->assertStatus(200);
        $response->assertJsonFragment(['errors' => [['message' => 'Fleet speed must be between 10% and 100% in 5% increments.', 'error' => 140020]]]);
    }

    /**
     * Test that General class can still use normal speed ranges.
     */
    public function testGeneralClassCanUseNormalSpeedRanges(): void
    {
        // Set user character class to General
        $user = $this->planetService->getPlayer()->getUser();
        $user->character_class = \OGame\Enums\CharacterClass::GENERAL->value;
        $user->save();

        // Add a ship to the planet
        $this->planetAddUnit('light_fighter', 1);

        // Test data for fleet dispatch with 10% speed (minimum normal speed)
        $fleetData = [
            'galaxy' => 1,
            'system' => 2,
            'position' => 1,
            'type' => 1,
            'metal' => 0,
            'crystal' => 0,
            'deuterium' => 0,
            'mission' => 3, // Transport mission
            'speed' => 1.0, // 10% speed
            'holdingtime' => 0,
            'token' => csrf_token(),
            'am202' => ['light_fighter' => 1],
        ];

        // This should work fine for General class
        $response = $this->postJson('/ajax/fleet/dispatch/send-fleet', $fleetData);

        // Should not return validation error for speed
        $response->assertStatus(200);
        $response->assertJsonMissing(['errors' => ['Fleet speed must be between 10% and 100% in 5% increments.']]);
    }
}
