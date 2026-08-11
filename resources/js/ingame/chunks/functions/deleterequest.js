function deleteRequest(obj) {
  let simId = $(obj).data("simulationId");

  if (simId === 0) {
    return;
  }

  let creation = $("single-simulation[data-simulation-id=" + simId + "] .creation").html();
  let target = $("single-simulation[data-simulation-id=" + simId + "] .target span").html();
  let question = combatSimLoca.LOCA_COMBATSIM_DELETE_REQUEST.replace("#date#", creation).replace("#target#", target);
  errorBoxDecision(combatSimLoca.LOCA_COMBATSIM_DELETE, question, jsloca.LOCA_ALL_YES, jsloca.LOCA_ALL_NO, function () {
    deleteSim(simId);
  });
}
