<?php

namespace OGame\Services;

use InvalidArgumentException;

/**
 * Service for module view slot injection.
 *
 * Core Blade views contain @moduleSlot('slot.name', $data) directives at
 * agreed extension points. Modules register renderer callables through the
 * Extensions facade, which delegates to this service. Each callable receives a
 * data array and returns an HTML string.
 *
 * Usage in a module provider:
 *
 *   Extensions::module('mymodule', function (ModuleExtension $module): void {
 *       $module->slot('layout.resources_bar', function (array $data): string {
 *           return view('mymodule::layout.resource-tile', $data)->render();
 *       });
 *   });
 *
 * Available slot names:
 *   layout.resources_bar       — after darkmatter tile in main layout resource bar
 *   layout.resources_bar_js    — after resource JS vars in main layout
 *   resources.building_section — after building grid on resources page
 *   resources.production_box   — after production boxes on resources page
 *   overview.planet_info       — after planet stats on overview page
 *   admin.nav                  — after existing nav items in admin bar
 */
class ModuleSlotService
{
    /**
     * Core-owned extension points. Modules may append content only at these
     * explicit boundaries; this keeps core views and module upgrades safe.
     */
    public const SLOTS = [
        'layout.resources_bar',
        'layout.resources_bar_js',
        'resources.building_section',
        'resources.production_box',
        'overview.planet_info',
        'admin.nav',
    ];

    /** @var array<string, array<callable>> */
    private static array $slots = [];

    /**
     * Register a renderer callable for a named slot.
     *
     * @param string   $slot     The slot name, e.g. 'layout.resources_bar'
     * @param callable $renderer Receives array $data, returns HTML string
     */
    public static function register(string $slot, callable $renderer): void
    {
        if (!in_array($slot, self::SLOTS, true)) {
            throw new InvalidArgumentException(sprintf(
                'Unknown module view slot [%s]. Supported slots: %s.',
                $slot,
                implode(', ', self::SLOTS),
            ));
        }

        self::$slots[$slot][] = $renderer;
    }

    /**
     * Render all registered callables for a slot and return concatenated HTML.
     *
     * @param string               $slot
     * @param array<string, mixed> $data
     * @return string
     */
    public static function render(string $slot, array $data = []): string
    {
        $html = '';
        foreach (self::$slots[$slot] ?? [] as $renderer) {
            $html .= $renderer($data);
        }

        return $html;
    }

    /**
     * Returns true if at least one renderer is registered for the slot.
     */
    public static function hasSlot(string $slot): bool
    {
        return !empty(self::$slots[$slot]);
    }

    /**
     * Clear all registered slot renderers. Intended for use in tests only.
     */
    public static function resetSlots(): void
    {
        self::$slots = [];
    }
}
