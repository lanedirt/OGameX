function removeCombatSim(simId) {
  if (combatSimId === simId) {
    combatSimId = 0;
    newCombatPlanning();
  }

  $(`combatsim-list single-simulation[data-simulation-id="${simId}"]`).remove();
}
