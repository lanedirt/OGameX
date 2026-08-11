function saveRequest() {
  let simId = combatSimId;

  if (combatSimProgress === true) {
    let creation = $("single-simulation[data-simulation-id=" + simId + "] .creation").html();
    let target = $("single-simulation[data-simulation-id=" + simId + "] .target span").html();
    let question = combatSimLoca.LOCA_COMBATSIM_RESULTS_ERASE.replace("#date#", creation).replace("#target#", target);
    errorBoxDecision(jsloca.LOCA_NOTIFY_WARNING, question, jsloca.LOCA_ALL_YES, jsloca.LOCA_ALL_NO, function () {
      saveSim();
    });
  } else {
    saveSim();
  }
}
