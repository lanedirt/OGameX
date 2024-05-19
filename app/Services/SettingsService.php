<?php

namespace OGame\Services;

use OGame\Models\Setting;

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
     * @var array<string, Setting>
     */
    private array $settings = [];

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
    private function loadFromDatabase(): void
    {
        $settings = Setting::all();
        foreach ($settings as $setting) {
            $this->settings[$setting->key] = $setting;
        }
    }

    /**
     * Get a setting value by key.
     *
     * @param string $key
     * @param string|int $default
     * @return string
     */
    public function get(string $key, string|int $default = ''): string
    {
        // When a setting is accessed, load everything from database.
        // We do it here instead of in constructor so call to database
        // is only made when something on the page actually accesses
        // a settings value.
        if (empty($this->settings)) {
            $this->loadFromDatabase();
        }

        // If it doesn't exist, return default.
        if (empty($this->settings[$key])) {
            return (string)$default;
        }

        return $this->settings[$key]->value;
    }

    /**
     * Set a setting value by key.
     *
     * @param string $key
     * @param string|int $value
     * @return void
     */
    public function set(string $key, string|int $value): void
    {
        // When a setting is accessed, load everything from database.
        // We do it here instead of in constructor so call to database
        // is only made when something on the page actually accesses
        // a settings value.
        if (empty($this->settings)) {
            $this->loadFromDatabase();
        }

        // Check if to be saved value is actually different from current one.
        $currentValue = $this->get($key, '');
        if (!empty($currentValue) && $currentValue === $value) {
            // To be saved value is same as current value, skip update to prevent unnecessary db call.
            return;
        }

        $updated_setting = Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        $this->settings[$key] = $updated_setting;
    }

    /**
     * Returns the fleet speed setting.
     *
     * @return int
     */
    public function fleetSpeed(): int
    {
        return (int)$this->get('fleet_speed', 1);
    }

    /**
     * Returns the fleet speed setting.
     *
     * @return int
     */
    public function economySpeed(): int
    {
        return (int)$this->get('economy_speed', 1);
    }

    /**
     * Returns the fleet speed setting.
     *
     * @return int
     */
    public function researchSpeed(): int
    {
        return (int)$this->get('research_speed', 1);
    }

    /**
     * Returns the basic income metal setting.
     *
     * @return int
     */
    public function basicIncomeMetal(): int
    {
        return (int)$this->get('basic_income_metal', 30);
    }

    /**
     * Returns the basic income crystal setting.
     *
     * @return int
     */
    public function basicIncomeCrystal(): int
    {
        return (int)$this->get('basic_income_crystal', 15);
    }

    /**
     * Returns the basic income deuterium setting.
     *
     * @return int
     */
    public function basicIncomeDeuterium(): int
    {
        return (int)$this->get('basic_income_deuterium', 0);
    }

    /**
     * Returns the basic income energy setting.
     *
     * @return int
     */
    public function basicIncomeEnergy(): int
    {
        return (int)$this->get('basic_income_energy', 0);
    }
}
