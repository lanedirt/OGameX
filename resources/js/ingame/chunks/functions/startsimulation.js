function startSimulation(obj) {
  let body = {
    _token: token,
    simId: combatSimId,
  };
  $.ajax({
    url: simBackendUrl + "&action=startSim",
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
        loadSimDetails();
        $("#simulateCombatPlanning").prop("disabled", true);
        showNotification(json.message, "success");
      }
    },
    error: function () {},
  });
}
