function loadPlanetAction(planetId, currentTarget, isBaseDefender, attackType) {
  let body = {
    _token: token,
  };

  if (planetId) {
    body["planetId"] = planetId;
  }

  $.ajax({
    url: simBackendUrl + "&action=loadPlanet",
    data: body,
    type: "POST",
    dataType: "json",
    success: function (json) {
      if (typeof json.planetData == "object") {
        token = json.newAjaxToken;
        fillData($(currentTarget.closest("fleet-content")[0]), json.planetData, isBaseDefender, attackType);
        showNotification(combatSimLoca.LOCA_COMBATSIM_PLANET_LOADED, "success");
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
