<?php

namespace OGame\GameMissions\BattleEngine\Models;

use OGame\GameObjects\Models\Units\UnitCollection;

/**
 * Result of evaluating whether a defending fleet should tactically retreat.
 */
class TacticalRetreatDecision
{
    /**
     * @param int $attackerPoints Retreat-weighted attacker fleet points.
     * @param int $defenderPoints Retreat-weighted defender fleet points.
     * @param int $ratio Display ratio (attacker supremacy as 1:N).
     * @param bool $defenderFled Whether the defending planet fleet fled.
     * @param bool $attackerAlsoRetreated Whether the attacker withdrew without fighting.
     * @param int $deuteriumCost Deuterium required (and spent if fled).
     * @param UnitCollection $fleeingUnits Ships that leave combat but stay on the planet.
     * @param string $blockedReason Empty when flee succeeded or was not eligible for ratio reasons.
     */
    public function __construct(
        public int $attackerPoints,
        public int $defenderPoints,
        public int $ratio,
        public bool $defenderFled,
        public bool $attackerAlsoRetreated,
        public int $deuteriumCost,
        public UnitCollection $fleeingUnits,
        public string $blockedReason = '',
    ) {
    }
}
