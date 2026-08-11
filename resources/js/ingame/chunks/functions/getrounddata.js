/* Rounds start */

function getRoundData(round, participant, side = "attacker") {
  let remainingTechs = {};
  let lostTechs = {};
  let roundData = {};

  if (typeof combatData === "undefined") {
    return roundData;
  }

  let { combatRounds } = combatData;

  if (!combatRounds[round]) {
    return roundData;
  }

  let currentRound = combatRounds[round];

  if (participant !== "all") {
    if (currentRound[`${side}Ships`] && currentRound[`${side}Ships`][participant]) {
      remainingTechs = currentRound[`${side}Ships`][participant];
    }

    if (currentRound[`${side}Losses`] && currentRound[`${side}Losses`][participant]) {
      lostTechs = currentRound[`${side}Losses`][participant];
    }
  } else {
    if (currentRound[`${side}ShipsTotal`]) {
      remainingTechs = currentRound[`${side}ShipsTotal`];
    }

    if (currentRound[`${side}LossesInThisRoundTotal`]) {
      lostTechs = currentRound[`${side}LossesInThisRoundTotal`];
    }
  }

  Object.keys(remainingTechs).map((techId) => {
    if (!roundData[techId]) {
      roundData[techId] = {
        remaining: 0,
        lost: 0,
      };
    }

    roundData[techId].remaining = remainingTechs[techId];
  });
  Object.keys(lostTechs).map((techId) => {
    if (!roundData[techId]) {
      roundData[techId] = {
        remaining: 0,
        lost: 0,
      };
    }

    roundData[techId].lost = lostTechs[techId];
  });
  return roundData;
}
