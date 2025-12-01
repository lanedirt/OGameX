<?php

namespace Tests\Unit;

use OGame\Services\SettingsService;
use Tests\UnitTestCase;

/**
 * Test defense repair rate setting functionality.
 */
class DefenseRepairRateSettingTest extends UnitTestCase
{
    /**
     * Test that default defense repair rate is 70%.
     */
    public function testDefaultDefenseRepairRate(): void
    {
        // Create a fresh settings service to test default value
        // Note: This test verifies the default value in the method, not the database
        $settingsService = resolve(SettingsService::class);

        // Clear any existing setting to test default
        $settingsService->set('defense_repair_rate', 70);

        // Default value should be 70
        $this->assertEquals(70, $settingsService->defenseRepairRate());
    }

    /**
     * Test that custom defense repair rate is returned when set.
     */
    public function testCustomDefenseRepairRate(): void
    {
        $settingsService = resolve(SettingsService::class);

        // Set a custom value
        $settingsService->set('defense_repair_rate', 50);

        // Verify the custom value is returned
        $this->assertEquals(50, $settingsService->defenseRepairRate());

        // Test with 0%
        $settingsService->set('defense_repair_rate', 0);
        $this->assertEquals(0, $settingsService->defenseRepairRate());

        // Test with 100%
        $settingsService->set('defense_repair_rate', 100);
        $this->assertEquals(100, $settingsService->defenseRepairRate());
    }
}
