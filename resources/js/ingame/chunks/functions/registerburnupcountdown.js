function registerBurnUpCountDown(elementId) {
  var burnUpCountDownElement = $(elementId);
  var duration = $(elementId).data("duration");

  if (duration > 0) {
    if (!burnUpCountDownForStationScreen[elementId]) {
      burnUpCountDownForStationScreen[elementId] = new simpleCountdown(burnUpCountDownElement, duration, function () {
        location.reload();
      });
    }
  }
}
