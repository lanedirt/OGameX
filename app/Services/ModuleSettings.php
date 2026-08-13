<?php

namespace OGame\Services;

/**
 * Scoped reader/writer for a module's server settings.
 *
 * Obtain an instance from SettingsService::module('alias'). Keys passed to this
 * class are always the short setting key; the module alias is prepended to form
 * the persisted key, e.g. "lifeforms.population.tick_seconds".
 */
class ModuleSettings
{
    public function __construct(
        private SettingsService $settings,
        private string $alias,
    ) {
    }

    public function get(string $key, string|int|float|bool $default = ''): string
    {
        return $this->settings->get($this->namespaced($key), (string) $default);
    }

    public function set(string $key, string|int|float|bool $value): void
    {
        $this->settings->set($this->namespaced($key), $value);
    }

    public function integer(string $key, int $default = 0): int
    {
        return (int) $this->get($key, $default);
    }

    public function string(string $key, string $default = ''): string
    {
        return $this->get($key, $default);
    }

    public function boolean(string $key, bool $default = false): bool
    {
        return (bool) $this->get($key, $default ? '1' : '0');
    }

    private function namespaced(string $key): string
    {
        return $this->alias . '.' . $key;
    }
}
