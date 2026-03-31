<?php

namespace OGame\Contracts\Modules;

use OGame\Services\PlayerService;

/**
 * Contract for modules that inject player-level data or state,
 * such as per-player artifact counts or lifeform progress.
 *
 * Register implementations via app()->tag() in bootModule():
 *   app()->tag(MyPlayerExtension::class, 'module.player_extensions');
 */
interface ExtendsPlayerService
{
    /**
     * Called when the PlayerService is booted for a player.
     * Attach module-specific data to the player as needed.
     */
    public function extendPlayer(PlayerService $player): void;
}
