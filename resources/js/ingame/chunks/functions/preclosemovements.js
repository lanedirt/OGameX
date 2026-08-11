function preCloseMovements() {
  $.each(currentMovementTabExtensionStates, function (id, data) {
    if (data[0] == 0) {
      var elem = $("#fleet" + id + " span.openDetails a");
      var expireTime = elem.attr("data-end-time");
      closeDetails(id, expireTime);
    }
  });
}
