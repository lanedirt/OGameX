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

    /**
     * Returns the amount of planets that should be created for a new player
     * upon registration. Defaults to 1.
     *
     * @return int
     */
    public function registrationPlanetAmount(): int
    {
        return (int)$this->get('registration_planet_amount', 1);
    }

    /**
     * Returns the amount of planet fields bonus given upon planet creation.
     *
     * @return int
     */
    public function planetFieldsBonus(): int
    {
        return (int)$this->get('planet_fields_bonus', 0);
    }

    /**
     * Returns the amount of dark matter given for a new player.
     *
     * @return int
     */
    public function darkMatterBonus(): int
    {
        return (int)$this->get('dark_matter_bonus', 8000);
    }

    /**
     * Returns the status of the Alliance Combat System.
     *
     * @return int
     */
    public function allianceCombatSystemOn(): int
    {
        return (int)$this->get('alliance_combat_system_on', 1);
    }

    /**
     * Returns the percentage of debris field generated from destroyed ships.
     *
     * @return int
     */
    public function debrisFieldFromShips(): int
    {
        return (int)$this->get('debris_field_from_ships', 30);
    }

    /**
     * Returns the percentage of debris field generated from destroyed defensive structures.
     *
     * @return int
     */
    public function debrisFieldFromDefense(): int
    {
        return (int)$this->get('debris_field_from_defense', 0);
    }

    /**
     * Returns the status of Deuterium in debris fields.
     *
     * @return int
     */
    public function debrisFieldDeuteriumOn(): int
    {
        return (int)$this->get('debris_field_deuterium_on', 0);
    }

    /**
     * Returns the maximum percentage chance of a moon forming after battle.
     *
     * @return int
     */
    public function maximumMoonChance(): int
    {
        return (int)$this->get('maximum_moon_chance', 20);
    }

    /**
     * Returns the status of ignoring empty systems.
     *
     * @return int
     */
    public function ignoreEmptySystemsOn(): int
    {
        return (int)$this->get('ignore_empty_systems_on', 0);
    }

    /**
     * Returns the status of ignoring inactive systems.
     *
     * @return int
     */
    public function ignoreInactiveSystemsOn(): int
    {
        return (int)$this->get('ignore_inactive_systems_on', 0);
    }

    /**
     * Returns the number of galaxies in the universe.
     *
     * @return int
     */
    public function numberOfGalaxies(): int
    {
        return (int)$this->get('number_of_galaxies', 9);
    }

    /**
     * Returns the name of the universe.
     *
     * @return string
     */
    public function universeName(): string
    {
        return $this->get('universe_name', "Universe");
    }

    /**
     * Returns the battle engine setting.
     *
     * @return string
     */
    public function battleEngine(): string
    {
        return $this->get('battle_engine', 'rust');
    }

    /**
     * Returns if expedition failed outcome is enabled.
     *
     * @return bool
     */
    public function expeditionFailedEnabled(): bool
    {
        return (bool)$this->get('expedition_failed', 1);
    }

    /**
     * Returns if expedition failed and delay outcome is enabled.
     *
     * @return bool
     */
    public function expeditionFailedAndDelayEnabled(): bool
    {
        return (bool)$this->get('expedition_failed_and_delay', 1);
    }

    /**
     * Returns if expedition failed and speedup outcome is enabled.
     *
     * @return bool
     */
    public function expeditionFailedAndSpeedupEnabled(): bool
    {
        return (bool)$this->get('expedition_failed_and_speedup', 1);
    }

    /**
     * Returns if expedition gain ships outcome is enabled.
     *
     * @return bool
     */
    public function expeditionGainShipsEnabled(): bool
    {
        return (bool)$this->get('expedition_gain_ships', 1);
    }

    /**
     * Returns if expedition gain dark matter outcome is enabled.
     *
     * @return bool
     */
    public function expeditionGainDarkMatterEnabled(): bool
    {
        return (bool)$this->get('expedition_gain_dark_matter', 1);
    }

    /**
     * Returns if expedition gain resources outcome is enabled.
     *
     * @return bool
     */
    public function expeditionGainResourcesEnabled(): bool
    {
        return (bool)$this->get('expedition_gain_resources', 1);
    }

    /**
     * Returns if expedition gain merchant trade outcome is enabled.
     *
     * @return bool
     */
    public function expeditionGainMerchantTradeEnabled(): bool
    {
        return (bool)$this->get('expedition_gain_merchant_trade', 1);
    }

    /**
     * Returns if expedition gain item outcome is enabled.
     *
     * @return bool
     */
    public function expeditionGainItemEnabled(): bool
    {
        return (bool)$this->get('expedition_gain_item', 1);
    }

    /**
     * Returns if expedition loss of fleet outcome is enabled.
     *
     * @return bool
     */
    public function expeditionLossOfFleetEnabled(): bool
    {
        return (bool)$this->get('expedition_loss_of_fleet', 1);
    }

    /**
     * Returns if expedition battle outcome is enabled.
     *
     * @return bool
     */
    public function expeditionBattleEnabled(): bool
    {
        return (bool)$this->get('expedition_battle', 1);
    }
}
