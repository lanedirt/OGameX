function displayCombatResearchPercentages(side, participant) {
  let combatResarchPercentages = getCombatResearchPercentages(side, participant);
  $(`#combatSimRounds .combat_participant.${side} .${side}Weapon span`).text(
    `${combatResarchPercentages.weaponPercentage}%`,
  );
  $(`#combatSimRounds .combat_participant.${side} .${side}Shield span`).text(
    `${combatResarchPercentages.shieldPercentage}%`,
  );
  $(`#combatSimRounds .combat_participant.${side} .${side}Cover span`).text(
    `${combatResarchPercentages.armorPercentage}%`,
  );
}
