function getRoundStatistic(round) {
  let statistic = {
    absorbedDamageAttacker: 0,
    absorbedDamageDefender: 0,
    fullStrengthAttacker: 0,
    fullStrengthDefender: 0,
    hitsAttacker: 0,
    hitsDefender: 0,
  };

  if (typeof combatData === "undefined") {
    return statistic;
  }

  let { combatRounds } = combatData;

  if (!combatRounds[round]) {
    return statistic;
  }

  let currentRound = combatRounds[round];

  if (currentRound.statistic) {
    statistic = currentRound.statistic;
  }

  return statistic;
}
