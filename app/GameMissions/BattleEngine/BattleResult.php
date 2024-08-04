<?php

namespace OGame\GameMissions\BattleEngine;

use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Resources;

/**
 * Class BattleResult.
 *
 * Model class that represents the result of a battle.
 */
class BattleResult
{
    /**
     * @var Resources The resources loot that the attacker player steals from the defender player's planet.
     */
    public Resources $loot;

    /**
     * @var int The max. percentage of resources that the attacker player could steal from the defender player's planet.
     */
    public int $lootPercentage;

    /**
     * @var UnitCollection The units that survived the battle from the attacker player.
     */
    public UnitCollection $attackerUnits;

}