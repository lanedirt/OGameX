<?php

namespace OGame\GameMissions\BattleEngine;

use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Resources;

/**
 * Class BattleResultRound.
 *
 * Model class that represents result of a battle round.
 */
class BattleResultRound
{
    /*
   "attackerLosses": {"4492924": {"203": "1"}},
            "attackerLossesInThisRound": {"4492924": {"203": "1"}},
            "defenderLosses": [{"401": "15", "402": "45", "404": "1"}],
            "defenderLossesInThisRound": [{"401": "15", "402": "45", "404": "1"}],
            "statistic": {
                "hitsAttacker": "361",
                "hitsDefender": "66",
                "absorbedDamageAttacker": "4448",
                "absorbedDamageDefender": "3772",
                "fullStrengthAttacker": "590656",
                "fullStrengthDefender": "16740"
            },
                "c": {
                    "4492924":
                        {
                            @foreach ($attacker_units_start->units as $unit)
                            "{{ $unit->unitObject->id }}": {{ $unit->amount }},
                            @endforeach
                        }
                },
            "defenderShips": [{
                @foreach ($defender_units_start->units as $unit)
                "{{ $unit->unitObject->id }}": {{ $unit->amount }},
                @endforeach
            }]  */

    /**
     * @var UnitCollection Unit losses of the attacker player until now which includes previous rounds.
     * TODO: now this only works for a single attacker. Support for multiple attackers should be added later.
     */
    public UnitCollection $attackerLosses;

    /**
     * @var UnitCollection Unit losses of the attacker player in this round.
     *  TODO: now this only works for a single attacker. Support for multiple attackers should be added later.
     */
    public UnitCollection $attackerLossesInThisRound;

    /**
     * @var UnitCollection Unit losses of the defender player until now which includes previous rounds.
     */
    public UnitCollection $defenderLosses;

    /**
     * @var UnitCollection Unit losses of the defender player in this round.
     */
    public UnitCollection $defenderLossesInThisRound;

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
     * @var UnitCollection The units of attacker player remaining at the end of the round.
     */
    public UnitCollection $attackerShips;

    /**
     * @var UnitCollection The units of defender player remaining at the end of the round.
     */
    public UnitCollection $defenderShips;

}