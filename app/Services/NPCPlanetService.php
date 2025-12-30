<?php

namespace OGame\Services;

use OGame\Factories\PlayerServiceFactory;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Planet;

/**
 * NPCPlanetService - A specialized PlanetService for NPC expedition battles.
 *
 * This service wraps a real planet but overrides the getShipUnits(), getDefenseUnits(),
 * and getPlayer() methods to return NPC data instead of the real planet's data.
 */
class NPCPlanetService extends PlanetService
{
    private NPCPlayerService $npcPlayer;
    private UnitCollection $npcFleet;

    /**
     * Create an NPC planet service for expedition battles.
     *
     * @param PlayerServiceFactory $playerServiceFactory
     * @param SettingsService $settingsService
     * @param NPCPlayerService $npcPlayer The NPC player (pirate or alien)
     * @param UnitCollection $npcFleet The NPC fleet to fight
     * @param int $basePlanetId The real planet ID to use for coordinates/ID
     */
    public function __construct(
        PlayerServiceFactory $playerServiceFactory,
        SettingsService $settingsService,
        NPCPlayerService $npcPlayer,
        UnitCollection $npcFleet,
        int $basePlanetId
    ) {
        // Initialize NPC properties FIRST before calling parent constructor
        // because parent constructor may call methods that need these properties
        $this->npcPlayer = $npcPlayer;
        $this->npcFleet = $npcFleet;

        // Call parent constructor with the base planet ID
        // This will load the planet but we'll override the key methods to return NPC data
        parent::__construct($playerServiceFactory, $settingsService, null, null, $basePlanetId);
    }

    /**
     * Override to return NPC player instead of real planet owner.
     * This ensures battle engine uses NPC tech levels.
     *
     * @return PlayerService
     */
    public function getPlayer(): PlayerService
    {
        return $this->npcPlayer;
    }

    /**
     * Override to return NPC fleet instead of planet's ships.
     *
     * @return UnitCollection
     */
    public function getShipUnits(): UnitCollection
    {
        return $this->npcFleet;
    }

    /**
     * Override to return empty defenses (NPCs don't have defenses).
     *
     * @return UnitCollection
     */
    public function getDefenseUnits(): UnitCollection
    {
        return new UnitCollection();
    }
}
