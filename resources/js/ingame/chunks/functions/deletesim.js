function deleteSim(simId) {
  let body = {
    _token: token,
    simId: simId,
  };
  $.ajax({
    url: simBackendUrl + "&action=deleteSim",
    data: body,
    type: "POST",
    dataType: "json",
    success: function (json) {
      token = json.newAjaxToken;

      if (json.status === "failure") {
        showNotification(json.errors[0].message, "error");
        return;
      }

      if (json.status === "success") {
        $("combatsim-list single-simulation[data-simulation-id=" + json.simId + "]").remove();
        showNotification(json.message, "success");

        if (combatSimId === json.simId) {
          combatSimId = 0;
          loadSimDetails();
          $("#deleteCombatPlanning").attr("disabled", true).attr("data-simulation-id", 0).data("simulationId", 0);
          newCombatPlanning();
        }

        if ($("combatsim-list owned-sims single-simulation").length === 0) {
          $("combatsim-list owned-sims").append(
            '<div className="noentries">' + combatSimLoca.LOCA_COMBATSIM_NO_SIMS_FOUND + "</div>",
          );
        }

        $("combatsim-list .entryCount .current").html($("combatsim-list single-simulation").length);
      }
    },
    error: function () {
      showNotification(combatSimLoca.LOCA_ERROR_DEFAULT, "error");
    },
  });
}
