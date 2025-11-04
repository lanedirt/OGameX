<?php

namespace OGame\GameMissions\BattleEngine\Models;

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
     * @var Resources The debris generated as a result from the destroyed ships and/or defense after battle.
     */
    public Resources $debris;

    /**
     * @var bool Whether a moon already existed at defender's planet location before battle commenced.
     */
    public bool $moonExisted;

    /**
     * @var int The percentage chance of a moon appearing out of the debris field as a result of the battle.
     */
    public int $moonChance;

    /**
     * @var bool Whether a moon was created as a result of the battle. This is based on a dice roll using the moon chance.
     */
    public bool $moonCreated;

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
     * @var UnitCollection The units that were lost by the attacker player during the battle.
     */
    public UnitCollection $attackerUnitsLost;

    /**
     * @var Resources The resources in terms of ships that the attacker player lost during the battle.
     */
    public Resources $attackerResourceLoss;

    /**
     * @var UnitCollection The units of defender player at the start of the battle.
     */
    public UnitCollection $defenderUnitsStart;

    /**
     * @var UnitCollection The units survived the battle from the defender player.
     */
    public UnitCollection $defenderUnitsResult;

    /**
     * @var UnitCollection The units that were lost by the defender player during the battle.
     */
    public UnitCollection $defenderUnitsLost;

    /**
     * @var Resources The resources in terms of ships/defense that the defender player lost during the battle.
     */
    public Resources $defenderResourceLoss;

    /**
     * @var int The attacker player's weapon technology level.
     */
    public int $attackerWeaponLevel;

    /**
     * @var int The attacker player's shield technology level.
     */
    public int $attackerShieldLevel;

    /**
     * @var int The attacker player's armor technology level.
     */
    public int $attackerArmorLevel;

    /**
     * @var int The defender player's weapon technology level.
     */
    public int $defenderWeaponLevel;

    /**
     * @var int The defender player's shield technology level.
     */
    public int $defenderShieldLevel;

    /**
     * @var int The defender player's armor technology level.
     */
    public int $defenderArmorLevel;

    /**
     * @var array<BattleResultRound> The rounds of the battle.
     */
    public array $rounds;
}
