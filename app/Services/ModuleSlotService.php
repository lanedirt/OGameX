<?php

namespace OGame\Services;

/**
 * Service for module view slot injection.
 *
 * Core Blade views contain @moduleSlot('slot.name', $data) directives at
 * agreed extension points. Modules register renderer callables here during
 * bootModule(). Each callable receives a data array and returns an HTML string.
 *
 * Usage in a module's bootModule():
 *
 *   ModuleSlotService::register('layout.resources_bar', function (array $data): string {
 *       return view('mymodule::layout.resource-tile', $data)->render();
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
}
