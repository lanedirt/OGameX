function initAllianceDepot() {
  $(".overlayDiv #allydepotlayer select").ogameDropDown();
  $(".holdingTime:first-child").show();

  for (var id in supplyTimes) {
    new simpleCountdown($("#holdingTime-" + id), supplyTimes[id]);
  }

  $("#supplyTimeInput")
    .focus(function () {
      clearInput(this);
    })
    .keyup(function () {
      var deuterium = getValue($("#resources_deuterium").text());
      var costs = getValue($("#deutCosts").text());
      checkIntInput(this, 1, Math.floor(deuterium / costs));
    });
}
