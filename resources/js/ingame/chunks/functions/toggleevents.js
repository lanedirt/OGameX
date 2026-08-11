function toggleEvents(doNotClose) {
  if ($("#eventboxContent").is(":hidden")) {
    $("#eventboxContent").slideDown("fast");
    $("#js_eventDetailsClosed").hide();
    $("#js_eventDetailsOpen").show();

    if (typeof toggleEvents.loaded == "undefined" || !toggleEvents.loaded) {
      refreshFleetEvents();
    }
  } else {
    if (doNotClose) {
      return;
    }

    $("#eventboxContent").slideUp("fast");
    $("#js_eventDetailsClosed").show();
    $("#js_eventDetailsOpen").hide();
  }

  $("#contentWrapper select").ogameDropDown("hide");
}
