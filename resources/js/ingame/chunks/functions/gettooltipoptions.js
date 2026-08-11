function getTooltipOptions(element) {
  var $thisObj = $(element);
  var options = {
    skin: "cloud",
    size: "x-small",
    maxWidth: 400,
    closeButton: false,
    hideOn: {
      element: "mouseleave",
      tooltip: "mouseleave",
    },
    hideOnClickOutside: true,
  }; // we need longer tooltips on galaxy

  if (window.location.href.indexOf("galaxy") !== -1) {
    options.maxWidth = 400;
  }

  if ($thisObj.hasClass("tooltipPremium")) {
    options.skin = "premium";
  }

  if ($thisObj.hasClass("tooltipLeft")) {
    options.position = {
      target: "leftmiddle",
      tooltip: "righttop",
    };
  } else if ($thisObj.hasClass("tooltipRight")) {
    options.position = {
      target: "rightmiddle",
      tooltip: "lefttop",
    };
  } else if ($thisObj.hasClass("tooltipBottom")) {
    options.position = {
      target: "bottommiddle",
      tooltip: "topmiddle",
    };
  }

  if ($thisObj.data("tooltip-width")) {
    options.maxWidth = $thisObj.data("tooltip-width");
  }

  if ($thisObj.hasClass("hideTooltipOnMouseenter")) {
    options.hideOn.tooltip = "mouseenter";
  }

  if (isMobile || $thisObj.hasClass("tooltipClose")) {
    options.hideOthers = true; // options.hideOn = false;
  }

  if ($thisObj.hasClass("hideOthers")) {
    options.hideOthers = true;
  }

  options.afterUpdate = function (content, element) {
    if (isMobile && $thisObj.data("tooltip-button")) {
      var $buttonDiv = $(document.createElement("div")).addClass("tooltipButton");
      $(document.createElement("a"))
        .addClass("btn_blue")
        .attr("href", "javascript:void(0);")
        .html($thisObj.data("tooltip-button"))
        .bind("click", function (e) {
          if ($(element).not("a") && $(element).find("a").length) {
            element = $(element).find("a")[0];
          }

          var event = document.createEvent("MouseEvents");
          event.initMouseEvent("click", true, true, window, 1, 0, 0, 0, 0, false, false, false, false, 0, null);
          element.dispatchEvent(event);
        })
        .appendTo($buttonDiv);
      $(content).append($buttonDiv);
    }

    if (isMobile || $thisObj.hasClass("tooltipClose")) {
      var $closeBtn = $(document.createElement("div")).addClass("close-tooltip");
      $(content).prepend($closeBtn);
    }

    Tipped.refresh(element);
  };

  return options;
}
