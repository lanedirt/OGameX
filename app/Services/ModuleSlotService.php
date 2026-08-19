<?php

namespace OGame\Services;

use InvalidArgumentException;

/**
 * Service for module view slot injection.
 *
 * A core Blade view renders a named slot with @moduleSlot('slot.name'). Module
 * providers register a renderer callable for that slot, which receives an empty
 * data array and returns an HTML string. Core views are only ever modified at
 * these explicit, documented boundaries.
 *
 * Slots are additive and renderer output is plain HTML appended at a fixed
 * position. This foundation exposes a single, clearly controlled slot; no slot
 * may inject JavaScript or replace arbitrary core markup.
 *
 * Available slot names:
 *   admin.nav — after the existing nav items in the admin sidebar
 */
class ModuleSlotService
{
    /**
     * Core-owned extension points. Modules may append content only at these
     * explicit boundaries; this keeps core views and module upgrades safe.
     */
    public const SLOTS = [
        'admin.nav',
    ];

    /** @var array<string, array<callable>> */
    private static array $slots = [];

    /**
     * Register a renderer callable for a named slot.
     *
     * @param string   $slot     The slot name, e.g. 'admin.nav'
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
