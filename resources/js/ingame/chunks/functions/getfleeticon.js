function getFleetIcon(fleetArray, planetPosition, planetType) {
  if (!fleetArray || !fleetArray.length) {
    return "";
  }

  $(`#ownFleetStatus_${planetPosition}_${planetType}`)
    .removeClass("fleetNeutral")
    .addClass("tooltip")
    .addClass(fleetArray[0]["class"])
    .attr("title", fleetArray[0]["text"]);
}
