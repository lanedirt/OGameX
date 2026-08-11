function saveSimName(obj) {
  let simId = $(obj).data("simulationId");

  if (simId === 0 || simId !== combatSimId) {
    return;
  }

  let simName = $("combatsim-name input[name=simulationName]").val();
  let body = {
    _token: token,
    simId: simId,
    simName: simName,
  };
  $.ajax({
    url: simBackendUrl + "&action=saveSimName",
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
        $("combatsim-list single-simulation[data-simulation-id=" + json.simData.simId + "] .state span.simName").text(
          " - " + json.simData.simName,
        );
        $("combatsim-shortinfo .shortName span").text(json.simData.simName);
        showNotification(json.message, "success");
      }
    },
    error: function () {
      showNotification(combatSimLoca.LOCA_ERROR_DEFAULT, "error");
    },
  });
}
