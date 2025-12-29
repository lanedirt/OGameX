<?php

namespace OGame\Services;

/**
 * NPCPlayerService - A minimal PlayerService for NPC players used in expedition battles.
 *
 * This service extends PlayerService but overrides the minimal methods needed for battle simulations
 * without requiring a real database player record.
 */
class NPCPlayerService extends PlayerService
{
    private string $npcType;
    private int $weaponTech;
    private int $shieldTech;
    private int $armorTech;

    /**
     * Create an NPC player with specified tech levels.
     *
     * @param string $npcType 'pirate' or 'alien'
     * @param int $weaponTech
     * @param int $shieldTech
     * @param int $armorTech
     */
    public function __construct(string $npcType, int $weaponTech, int $shieldTech, int $armorTech)
    {
        // Initialize NPC properties FIRST before calling parent constructor
        // because parent constructor may call methods that need these properties
        $this->npcType = $npcType;
        $this->weaponTech = $weaponTech;
        $this->shieldTech = $shieldTech;
        $this->armorTech = $armorTech;

        // Call parent constructor with player_id = 0 (no real user record)
        // This creates a dummy user object which is fine for NPCs
        parent::__construct(0);
    }

    /**
     * Get NPC research level for a given technology.
     * Only weapon, shielding, and armor technologies are supported.
     *
     * @param string $machine_name
     * @return int
     */
    public function getResearchLevel(string $machine_name): int
    {
        return match ($machine_name) {
            'weapon_technology' => $this->weaponTech,
            'shielding_technology' => $this->shieldTech,
            'armor_technology' => $this->armorTech,
            default => 0,
        };
    }

    /**
     * Get NPC identifier (for battle reports).
     *
     * @return int
     */
    public function getId(): int
    {
        // Use negative IDs to distinguish NPCs from real players
        return $this->npcType === 'pirate' ? -1 : -2;
    }

    /**
     * Get NPC username (for battle reports).
     *
     * @param bool $formatted Whether to format the username (not used for NPCs)
     * @return string
     */
    public function getUsername(bool $formatted = true): string
    {
        return $this->npcType === 'pirate' ? 'Pirates' : 'Aliens';
    }

    /**
     * Get NPC type.
     *
     * @return string
     */
    public function getNpcType(): string
    {
        return $this->npcType;
    }
}
