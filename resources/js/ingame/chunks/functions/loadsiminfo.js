function loadSimInfo(obj) {
  let simId = $(obj).data("simulationId");

  if (combatSimChanged === true) {
    let creation = $("single-simulation[data-simulation-id=" + simId + "] .creation").html();
    let target = $("single-simulation[data-simulation-id=" + simId + "] .target span").html();
    let question = combatSimLoca.LOCA_COMBATSIM_UNSAVED.replace("#date#", creation).replace("#target#", target);
    errorBoxDecision(jsloca.LOCA_NOTIFY_WARNING, question, jsloca.LOCA_ALL_YES, jsloca.LOCA_ALL_NO, function () {
      loadSim(simId);
    });
  } else {
    loadSim(simId);
  }
}
