function clearPosition(position) {
  $("#galaxyRow" + position + " .cellPosition").removeClass("status_abbr_buddy");
  $("#galaxyRow" + position + " .cellPlanet").html("");
  $("#galaxyRow" + position + " .cellPlayerName").html("");
  $("#galaxyRow" + position + " .cellPlanetName").html("");
  $("#galaxyRow" + position + " .cellMoon").html("");
  $("#galaxyRow" + position + " .cellDebris").html("");
  $("#galaxyRow" + position + " .cellAlliance").html("");
  $("#galaxyRow" + position + " .cellAction").html("");
  let rowElement = $("#galaxyRow" + position);
  rowElement.removeClass("inactive_filter");
  rowElement.removeClass("filtered_filter_inactive");
  rowElement.removeClass("vacation_filter");
  rowElement.removeClass("filtered_filter_vacation");
  rowElement.removeClass("strong_filter");
  rowElement.removeClass("filtered_filter_strong");
  rowElement.removeClass("newbie_filter");
  rowElement.removeClass("filtered_filter_newbie");
  rowElement.removeClass("empty_filter");
  rowElement.removeClass("filtered_filter_empty");
}
