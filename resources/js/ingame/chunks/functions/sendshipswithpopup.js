function sendShipsWithPopup(order, galaxy, system, planet, planettype, shipCount) {
  params = {
    mission: order,
    galaxy: galaxy,
    system: system,
    position: planet,
    type: planettype,
    shipCount: shipCount,
    _token: token,
  };
  $.ajax(miniFleetLink, {
    data: params,
    dataType: "json",
    type: "POST",
    success: function (data) {
      token = data.newAjaxToken;
      updateOverlayToken("phalanxSystemDialog", data.newAjaxToken);
      updateOverlayToken("phalanxDialog", data.newAjaxToken);

      if (data.response.success) {
        fadeBox(
          data.response.message +
            " " +
            data.response.coordinates.galaxy +
            ":" +
            data.response.coordinates.system +
            ":" +
            data.response.coordinates.position,
          !data.response.success,
        );
      } else {
        fadeBox(data.response.message, true);
      }
    },
  });
}
