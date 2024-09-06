<?php

namespace Tests\Feature;

use OGame\Services\SettingsService;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    /**
     * @var SettingsService
     */
    protected SettingsService $settingsService;

    /**
     * Set up common test components.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Initialize empty settings service object
        $settingsService = resolve(SettingsService::class);
        $this->settingsService = $settingsService;
    }

    /**
     * Check that settings records are correctly retrieved from database.
     */
    public function testSettingsGet(): void
    {
        // Set a value.
        $game_name = $this->settingsService->get('game_name', '');

        // Check that the game_name settings key is not empty.
        $this->assertNotEmpty($game_name);
    }

    /**
     * Check that setting a key and then immediately getting it works.
     */
    public function testSettingsSetGet(): void
    {
        // We do not use the generic $this->settingService in this
        // test method because we want to recreate it mid-test.
        // Initialize a new settingsService object
        $settingsService = resolve(SettingsService::class);

        // Set a value.
        $random_value = 'random_string_' . rand(0, 99999);
        $settingsService->set('test_setting_key', $random_value);

        // Initialize a new settingsService again to get a clean state
        // with a new database connection attempt to load settings again.
        $settingsService = resolve(SettingsService::class);

        // Get the value we just set again.
        $get_value = $settingsService->get('test_setting_key', '');

        // Assert that set value equals the now new get value.
        $this->assertEquals($random_value, $get_value);
    }
}
