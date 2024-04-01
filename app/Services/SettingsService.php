<?php

namespace OGame\Services;

use OGame\Setting;

/**
 * Class SettingsService.
 *
 * SettingsService object.
 *
 * @package OGame\Services
 */
class SettingsService
{
    /**
     * Array of setting objects.
     *
     * @var array
     */
    protected array $settings = [];

    /**
     * SettingsService constructor.
     */
    public function __construct()
    {

    }

    /**
     * Load all settings from database and cache locally.
     *
     * @return void
     */
    private function loadFromDatabase() {
        $settings = Setting::all();
        foreach ($settings as $setting) {
            $this->settings[$setting->key] = $setting;
        }
    }

    /**
     * Get a setting value by key.
     *
     * @param $key
     * @param $default
     * @return string
     */
    public function get($key, $default = null): string
    {
        // When a setting is accessed, load everythin from database.
        // We do it here instead of in constructor so call to database
        // is only made when something on the page actually accesses
        // a settings value.
        if (empty($this->settings)) {
            $this->loadFromDatabase();
        }

        // If it doesn't exist, return default.
        if (empty($this->settings[$key])) {
            return $default;
        }

        return $this->settings[$key]->value;
    }

    /**
     * Set a setting value by key.
     *
     * @param $key
     * @param $value
     * @return void
     */
    public function set($key, $value): void
    {
        // When a setting is accessed, load everythin from database.
        // We do it here instead of in constructor so call to database
        // is only made when something on the page actually accesses
        // a settings value.
        if (empty($this->settings)) {
            $this->loadFromDatabase();
        }

        // Check if to be saved value is actually different from current one.
        $currentValue = $this->get($key, '');
        if (!empty($currentValue) && $currentValue == $value) {
            // To be saved value is same as current value, skip update to prevent unnecessary db call.
            return;
        }

        $updated_setting = Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        $this->settings[$key] = $updated_setting;
    }
}
