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
     * @var UnitCollection The units of attacker player at the start of the battle.
     */
    public UnitCollection $attackerUnitsStart;

    /**
     * @var UnitCollection The units that survived the battle from the attacker player.
     */
    public UnitCollection $attackerUnitsResult;

    /**
     * @var UnitCollection The units of defender player at the start of the battle.
     */
    public UnitCollection $defenderUnitsStart;

    /**
     * @var UnitCollection The units survived the battle from the defender player.
     */
    public UnitCollection $defenderUnitsResult;

}