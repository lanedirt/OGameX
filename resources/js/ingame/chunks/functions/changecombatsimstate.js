function changeCombatSimState(simId, state) {
  let element = $(`combatsim-section[overview] single-simulation[data-simulation-id='${simId}'] .state .status`);
  let action = $(
    `combatsim-section[overview] single-simulation[data-simulation-id='${simId}'] sim-actions button.overlay`,
  );
  let shortAction = $("#showCombatResultShortInfo");
  action.hide();

  switch (state) {
    case 1:
      element.text(jsloca.COMBATSIM_PENDING).removeClass("planning done").addClass("pending");
      break;

    case 2:
      element.text(jsloca.COMBATSIM_DONE).removeClass("planning pending").addClass("done");
      action.show();
      shortAction.removeAttr("disabled").attr("data-target", action.data("target"));
      break;

    default:
      element.text(jsloca.COMBATSIM_PLANNING).removeClass("pending done").addClass("planning");
  }
}
