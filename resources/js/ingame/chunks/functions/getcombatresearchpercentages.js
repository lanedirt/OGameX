function getCombatResearchPercentages(side, participant = "all") {
  let combatResearchPercentages = {
    weaponPercentage: 0,
    shieldPercentage: 0,
    armorPercentage: 0,
  };

  if (typeof combatData === "undefined") {
    return combatResearchPercentages;
  }

  if (!combatData[side]) {
    return combatResearchPercentages;
  }

  if (participant === "all") {
    combatData[side].map((participantData) => {
      if (participantData.weaponPercentage) {
        combatResearchPercentages.weaponPercentage += participantData.weaponPercentage;
      }

      if (participantData.shieldPercentage) {
        combatResearchPercentages.shieldPercentage += participantData.shieldPercentage;
      }

      if (participantData.armorPercentage) {
        combatResearchPercentages.armorPercentage += participantData.armorPercentage;
      }
    });
    combatResearchPercentages.weaponPercentage /= combatData[side].length;
    combatResearchPercentages.shieldPercentage /= combatData[side].length;
    combatResearchPercentages.armorPercentage /= combatData[side].length;
  } else {
    if (combatData[side] && combatData[side][participant]) {
      let participantData = combatData[side][participant];

      if (participantData.weaponPercentage) {
        combatResearchPercentages.weaponPercentage = participantData.weaponPercentage;
      }

      if (participantData.shieldPercentage) {
        combatResearchPercentages.shieldPercentage = participantData.shieldPercentage;
      }

      if (participantData.armorPercentage) {
        combatResearchPercentages.armorPercentage = participantData.armorPercentage;
      }
    }
  }

  return combatResearchPercentages;
}
