/**
 * Common UI Components, that are reused across the Game
 *
 **/

/**
 * Fill level bar display for storage rooms and cargo space
 *
 * @param barContainerClass
 * @param barClass
 * @param premiumBarClass - if additional premium bar is wanted
 *
 **/

function refreshBars(barContainerClass, barClass, premiumBarClass) {
  var $barContainer = $("." + barContainerClass);
  $barContainer.each(function () {
    var $this = $(this),
      amountFull = $this.data("currentAmount"),
      capacity = $this.data("capacity"),
      wPercent = (amountFull / capacity) * 100,
      $bar = $this.find("." + barClass);

    if (wPercent > 100) {
      wPercent = 100;
    } else if (wPercent == 0) {
      wPercent = 0;
    } else if (wPercent < 1.3) {
      wPercent = 1.3;
    }

    $bar.css("width", wPercent + "%");

    if (wPercent < 90) {
      $bar.attr("class", barClass + " filllevel_undermark");
    } else if (wPercent > 90 && wPercent < 100) {
      $bar.attr("class", barClass + " filllevel_middlemark");
    } else {
      $bar.attr("class", barClass + " filllevel_overmark");
    }

    if (premiumBarClass) {
      var $premiumBar = $this.find("." + premiumBarClass),
        wPercentPremium = $premiumBar.data("premiumPercent");

      if (wPercent + wPercentPremium > 100) {
        wPercentPremium = 100 - wPercent;
      }

      $premiumBar.css("width", wPercentPremium + "%");
    }
  });
}
