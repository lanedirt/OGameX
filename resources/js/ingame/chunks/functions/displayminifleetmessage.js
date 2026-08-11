function displayMiniFleetMessage(response, addCoordinatesToMessage = true) {
  var message = response.message;

  if (addCoordinatesToMessage && typeof response.coordinates != "undefined" && response.coordinates) {
    message +=
      " [" +
      response.coordinates.galaxy +
      ":" +
      response.coordinates.system +
      ":" +
      response.coordinates.position +
      "]";
  }

  if (response.success) {
    var symbolSelector = "#ownFleetStatus_" + response.coordinates.position + "_" + response.planetType;

    switch (response.type) {
      case 1:
        $(symbolSelector).removeClass("fleetNeutral");
        $(symbolSelector).attr("title", galaxyLoca.fleetAttacking).addClass("fleetHostile").addClass("tooltip");
        break;

      case 2:
        $(symbolSelector).attr("title", galaxyLoca.fleetUnderway).addClass("fleetNeutral").addClass("tooltip");
        break;

      case 3:
        $(symbolSelector).attr("title", galaxyLoca.fleetUnderway).addClass("fleetNeutral").addClass("tooltip");
        break;

      case 4:
        $(symbolSelector).attr("title", galaxyLoca.fleetUnderway).addClass("fleetNeutral").addClass("tooltip");
        break;
    }

    addToTable(message, "success", response.shipsSent);
    showNotification(message, "success");
    $("#slotUsed").html(tsdpkt(response.slots));
    setShips("probeValue", tsdpkt(response.probes));
    setShips("recyclerValue", tsdpkt(response.recyclers));
    setShips("missileValue", tsdpkt(response.missiles));
  } else {
    addToTable(message, "error");
    showNotification(message, "error");
  }

  shipsendingDone = 1;
}
