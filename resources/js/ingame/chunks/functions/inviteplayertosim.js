function invitePlayerToSim(obj) {
  let simId = $(obj).data("simulationId");
  let playerId = $(obj).data("playerId");

  if ($("shared-participant").length >= combatSimMaxParticipants) {
    showNotification(
      combatSimLoca.LOCA_COMBATSIM_TOO_MUCH_PARTICIPANTS.replace("#number#", combatSimMaxParticipants),
      "error",
    );
    return;
  }

  if (combatSimId !== 0 && simId === combatSimId) {
    let body = {
      _token: token,
      simId: simId,
      playerId: playerId,
    };
    $.ajax({
      url: simBackendUrl + "&action=invitePlayer",
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
          loadSimDetails();
        }
      },
    });
  }
}
