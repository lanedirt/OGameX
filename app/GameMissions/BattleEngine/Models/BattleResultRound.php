<?php

namespace OGame\GameMissions\BattleEngine\Models;

use OGame\GameObjects\Models\Units\UnitCollection;

/**
 * Class BattleResultRound.
 *
 * Model class that represents result of a battle round.
 */
class BattleResultRound
{
    /**
     * @var UnitCollection Unit losses of the attacker player until now which includes previous rounds.
     * TODO: now this only works for a single attacker. Support for multiple attackers should be added later.
     */
    public UnitCollection $attackerLosses;

    /**
     * @var UnitCollection Unit losses of the player in this round.
     *  TODO: now this only works for a single attacker. Support for multiple attackers should be added later.
     */
    public UnitCollection $attackerLossesInRound;

    /**
     * @var UnitCollection Unit losses of the defender until now which includes previous rounds.
     */
    public UnitCollection $defenderLosses;

    /**
     * @var UnitCollection Unit losses of the defender player in this round.
     */
    public UnitCollection $defenderLossesInRound;

    /**
     * @var int Total amount of hits the attacker made this round.
     */
    public int $hitsAttacker = 0;

    /**
     * @var int Total amount of hits the defender made this round.
     */
    public int $hitsDefender = 0;

    /**
     * @var int Total amount of damage absorbed by the attacker this round.
     */
    public int $absorbedDamageAttacker = 0;

    /**
     * @var int Total amount of damage absorbed by the defender this round.
     */
    public int $absorbedDamageDefender = 0;

    /**
     * @var int Total amount of full strength of the attacker at the start of the round.
     */
    public int $fullStrengthAttacker = 0;

    /**
     * @var int Total amount of full strength of the defender at the start of the round.
     */
    public int $fullStrengthDefender = 0;

    /**
     * @var UnitCollection The units of the attacker remaining at the end of the round.
     */
    public UnitCollection $attackerShips;

    /**
     * @var UnitCollection The units of the defender remaining at the end of the round.
     */
    public UnitCollection $defenderShips;
}
