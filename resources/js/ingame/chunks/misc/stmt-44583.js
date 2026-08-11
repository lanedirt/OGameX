//This will init a bar (calling this directly is useful for re-doing a bar's sizes in the event it's been resized).

PercentSelector.initBar = function (bar) {
  if (!bar) return;
  var $bar = $(bar);
  var height = $bar.innerHeight();
  $bar.children(".PBcolorGrad").remove();
  $bar.children(".PBoverlay").remove();
  var opcAttr = $bar.attr("onpercentchange");

  if (opcAttr) {
    if (typeof opcAttr == "function") {
      $bar.get(0).onpercentchange = opcAttr;
    } else if (typeof opcAttr == "string") {
      if (/^function/.test(opcAttr)) {
        eval("$bar.get(0).onpercentchange = " + opcAttr);
      } else {
        eval("$bar.get(0).onpercentchange = function() {" + opcAttr + "}");
      }
    }
  }

  if (!PercentSelector.fallbackMode) {
    $bar.append($("<canvas class='PBoverlay'></canvas>").css("height", height).css("width", $bar.innerWidth()));
    $bar.append(
      $("<div class='PBcolorGrad'></div>")
        .css("height", height * 20)
        .css("top", -(2 * height)),
    );
    PercentSelector.createOverlay($bar);
  } else {
    $bar.addClass("fallback");
    $bar.append($("<div class='PBfallbackColor'></div>").css("height", height).css("width", $bar.innerWidth())); //$bar.append($("<div class='PBfallbackOverlay'></div>").css("height", height).css("width", $bar.innerWidth()).css("margin-top", -$bar.innerHeight()));
  }

  if ($bar.attr("percent") != null) {
    //ok, I know this is odd. It's because setPercent ignores the change if it's changing to the percent
    // the bar is already at. It remembers what percent it's at using the percent attribute. So trying to
    // initialize it to the percent attribute causes problems. So I just "reset" the attribute to 100% and then
    // re-initialize to the percent given.
    var percent = parseInt($bar.attr("percent"));
    $bar.attr("percent", 100);
    PercentSelector.setPercent($bar, percent, true);
  }

  if (!bar.isBound) {
    if (!($bar.attr("enabled") && $bar.attr("enabled").toLowerCase() == "false")) {
      var $bindBar = $bar;

      if (document.createTouch == undefined) {
        $bindBar.bind("mousedown", PercentSelector.handlers.mouseDown);
        $bindBar.bind("mousemove", PercentSelector.handlers.mouseMove);
        $bindBar.bind("mouseup", PercentSelector.handlers.mouseUp);
        $bindBar.bind("mouseout", PercentSelector.handlers.mouseOut);
      } else {
        $bindBar.bind("touchstart", PercentSelector.handlers.touchStart);
        $bindBar.bind("touchmove", PercentSelector.handlers.touchMove);
        $bindBar.bind("touchend", PercentSelector.handlers.touchEnd);
      }
    }

    bar.isBound = true; //to prevent multi-binding!
  }
};
