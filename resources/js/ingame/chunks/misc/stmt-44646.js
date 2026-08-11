PercentSelector.setPercent = function (bar, newPercent, animate) {
  var $bar = $(bar);
  var step = $bar.attr("step");
  if (!step) step = 1;
  else step = parseInt(step);
  newPercent = Math.round(newPercent / step) * step; //short circuit if the percent is not changing!

  if (newPercent == parseInt($bar.attr("percent"))) return;
  $bar.attr("percent", newPercent);

  if (PercentSelector.fallbackMode) {
    $bar.children(".PBfallbackColor").css("width", ($bar.innerWidth() * newPercent) / 100.0); //console.log("setting percent to: " + newPercent);
  } else {
    if (animate) {
      $bar.children(".PBcolorGrad").css("-webkit-transition", "-webkit-transform 0.6s ease-in");
      $bar.children(".PBcolorGrad").css("-moz-transition", "-moz-transform 0.6s ease-in");
    } else {
      $bar.children(".PBcolorGrad").css("-webkit-transition", "-webkit-transform 0.1s ease-in"); //turn off the animation in case it's on!

      $bar.children(".PBcolorGrad").css("-moz-transition", "-moz-transform 0.1s ease-in"); //turn off the animation in case it's on!
    }

    var yTrans = Math.round(($bar.children(".PBcolorGrad").outerHeight() * 0.9 * (100 - newPercent)) / 100.0);
    var xTrans = Math.round($bar.children(".PBcolorGrad").innerWidth() * ((100 - newPercent) / 100.0));

    if (animate) {
      setTimeout(function () {
        $bar.children(".PBcolorGrad").css("-webkit-transform", "translate(-" + xTrans + "px, -" + yTrans + "px)");
        $bar.children(".PBcolorGrad").css("-moz-transform", "translate(-" + xTrans + "px, -" + yTrans + "px)");
        $bar.children(".PBcolorGrad").css("-ms-transform", "translate(-" + xTrans + "px, -" + yTrans + "px)");
      }, 1);
    } else {
      $bar.children(".PBcolorGrad").css("-webkit-transform", "translate(-" + xTrans + "px, -" + yTrans + "px)");
      $bar.children(".PBcolorGrad").css("-moz-transform", "translate(-" + xTrans + "px, -" + yTrans + "px)");
      $bar.children(".PBcolorGrad").css("-ms-transform", "translate(-" + xTrans + "px, -" + yTrans + "px)");
    }
  }
};
