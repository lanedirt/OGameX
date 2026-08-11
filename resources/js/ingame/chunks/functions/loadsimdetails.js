function loadSimDetails() {
  if (combatSimId === 0) {
    $("fleet-content[siminfo]").html('<div class="noentries">' + combatSimLoca.LOCA_COMBATSIM_NO_SIMS_FOUND + "</div>");
  } else {
    let body = {
      _token: token,
      simId: combatSimId,
    };
    $.ajax({
      url: simInfoUrl,
      data: body,
      type: "POST",
      dataType: "json",
      success: function (json) {
        token = json.newAjaxToken;
        $("fleet-content[siminfo]").html(json.content[json.target]);
        updateSimShortInfo($("combatsim-intro combatsim-shortinfo"));

        if (combatSimProgress) {
          let action = $(
            `combatsim-section[overview] single-simulation[data-simulation-id='${combatSimId}'] sim-actions button.overlay`,
          );
          let shortAction = $("#showCombatResultShortInfo");
          shortAction.removeAttr("disabled").attr("data-target", action.data("target"));
        }
      },
    });
  }
}
