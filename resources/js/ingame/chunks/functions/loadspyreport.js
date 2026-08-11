function loadSpyReport(hashId, currentTarget, isBaseDefender, attackType) {
  if (!currentTarget || !currentTarget.length) {
    return;
  }

  let body = {
    _token: token,
  };

  if (hashId) {
    body["reportHash"] = hashId;
  }

  $.ajax({
    url: simBackendUrl + "&action=loadReport",
    data: body,
    type: "POST",
    dataType: "json",
    success: function (json) {
      if (typeof json.planetData == "object") {
        token = json.newAjaxToken;
        adjustResearchClassBonuses(json.planetData);
        fillData($(currentTarget.closest("fleet-content")[0]), json.planetData, isBaseDefender, attackType);
        $("fleet-content[data-participant-id=0][data-attack-type=2] input[name='apikey']").val(hashId);
        showNotification(json.message, "success");
        simChanged(currentTarget);
      } else {
        token = json.newAjaxToken;

        if (json.errors && json.errors.length) {
          showNotification(json.errors[0].message, "error");
        }
      }
    },
    error: function () {
      showNotification(combatSimLoca.LOCA_ERROR_DEFAULT, "error");
    },
  });
}
