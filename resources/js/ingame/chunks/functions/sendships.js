function sendShips(order, galaxy, system, planet, planettype, shipCount, additionalParams) {
  if (shipsendingDone == 1) {
    shipsendingDone = 0;
    params = {
      mission: order,
      galaxy: galaxy,
      system: system,
      position: planet,
      type: planettype,
      shipCount: shipCount,
      _token: token,
    };

    if (additionalParams && typeof additionalParams === "object") {
      Object.keys(additionalParams).map((key) => {
        if (!params[key]) {
          params[key] = additionalParams[key];
        }
      });
    }

    $.ajax(miniFleetLink, {
      data: params,
      dataType: "json",
      type: "POST",
      success: function (data) {
        token = data.newAjaxToken;
        updateOverlayToken("phalanxSystemDialog", data.newAjaxToken);
        updateOverlayToken("phalanxDialog", data.newAjaxToken);
        getAjaxEventbox();
        displayMiniFleetMessage(data.response);
        refreshFleetEvents(true);
      },
    });
  }
}
