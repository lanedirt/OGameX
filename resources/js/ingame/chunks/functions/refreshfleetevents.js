function refreshFleetEvents(force) {
  if (typeof eventlistLink === "undefined") {
    return;
  }

  if (!$("#eventboxContent").is(":hidden") || force === true) {
    $("#eventboxContent").html('<img height="16" width="16" src="/img/icons/3f9884806436537bdec305aa26fc60.gif" />');
    $.ajax({
      url: eventlistLink,
      success: function (response) {
        $("#eventboxContent").html(response);
        toggleEvents.loaded = true;
      },
    });
  }
}
