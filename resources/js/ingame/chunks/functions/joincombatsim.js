function joinCombatSim(simId, playerId) {
  let body = {
    _token: token,
    simId: simId,
    playerId: playerId,
  };
  $.ajax({
    url: simBackendUrl + "&action=joinSim",
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
        showNotification(json.message, "success");
        window.open(combatSimUrl);
      }
    },
    error: function () {},
  });
}
