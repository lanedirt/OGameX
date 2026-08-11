function registerRepairTimeCountDown(elementId) {
  var repairTimeCountDownElement = $(elementId);
  var duration = $(elementId).data("duration");

  if (duration > 0) {
    if (!repairTimeDownForStationScreen[elementId]) {
      repairTimeDownForStationScreen[elementId] = new simpleCountdown(
        repairTimeCountDownElement,
        duration,
        function () {
          location.reload();
        },
      );
    }
  }
}
