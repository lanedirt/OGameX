function initTooltips(selector) {
  initTooltipSkins();
  selector = getTooltipSelector(selector);

  function generateHTML(text) {
    var element = {};
    var splitted = text.split("|");
    var title = $(document.createElement("h1")).html(splitted[0]);
    var splitLine = $(document.createElement("div")).addClass("splitLine");

    if (typeof splitted[2] !== "undefined" && typeof splitted[3] !== "undefined") {
      var title2 = $(document.createElement("h1")).html(splitted[2]);
      var splitLine2 = $(document.createElement("div")).addClass("splitLine");
      element = $(document.createElement("div"))
        .css("display", "none")
        .addClass("htmlTooltip")
        .append(title)
        .append(splitLine)
        .append(splitted[1] + "</br>")
        .append(title2)
        .append(splitLine2)
        .append(splitted[3]);
    } else {
      element = $(document.createElement("div"))
        .css("display", "none")
        .addClass("htmlTooltip")
        .append(title)
        .append(splitLine)
        .append(splitted[1]);
    }

    return element[0];
  }

  removeTooltip(selector);

  function addTooltip(object) {
    var $thisObj = $(object);

    if ($thisObj.data("tooltipLoaded")) {
      return;
    }

    $thisObj.data("tooltipLoaded", true);

    if (isMobile && $thisObj.hasClass("js_hideTipOnMobile")) {
      $thisObj.attr("title", "");
      return;
    }

    var options = getTooltipOptions($thisObj);

    if ($thisObj.hasClass("tooltipCustom")) {
      if (options.hideOn != false) {
        options.hideOn = {
          element: "mouseleave",
          tooltip: "mouseleave",
        };
      }

      options.afterUpdate = function (content) {
        $(content)
          .find(".tooltipCustom")
          .each(function (i, element) {
            var options = getTooltipOptions($thisObj);

            if ($(this).hasClass("tooltipHTML")) {
              options.inline = true;
              options.hideOthers = false;
              Tipped.create(this, generateHTML(sanitizeTooltip($(this).attr("title"))), options);
            } else {
              options.hideOthers = false;
              Tipped.create(this, sanitizeTooltip($(this).attr("title")), options);
            }
          });
      };
    }

    if ($thisObj.hasClass("tooltipHTML")) {
      if (typeof $thisObj.attr("title") == "undefined" || $thisObj.attr("title").trim().length == 0) {
        return;
      }

      Tipped.create($thisObj[0], generateHTML(sanitizeTooltip($thisObj.attr("title"))), options);
      return;
    }

    if ($thisObj.hasClass("tooltipRel")) {
      options.inline = $thisObj.attr("rel");

      if ($thisObj.hasClass("tooltipPersistent")) {
        options.detach = false;
      }

      Tipped.create($thisObj[0], undefined, options);
      return;
    }

    if ($thisObj.hasClass("tooltipAJAX")) {
      $.get($thisObj.attr("rel"), {}, function (data) {
        Tipped.create($thisObj[0], data, options);
      });
      return;
    }

    if (typeof $thisObj.attr("title") == "undefined" || $thisObj.attr("title").trim().length == 0) {
      return;
    }

    Tipped.create($thisObj[0], sanitizeTooltip($thisObj.attr("title")), options);
  }

  $(document)
    .undelegate(selector, "touchstart.tooltipClick")
    .delegate(selector, "touchstart.tooltipClick", function (e) {
      if (Tipped.visible(this)) {
        var event = document.createEvent("MouseEvents");
        event.initMouseEvent("click", true, true, window, 1, 0, 0, 0, 0, false, false, false, false, 0, null);
        this.dispatchEvent(event);
        e.preventDefault();
        e.stopPropagation();
      }
    }); // this fixes tooltips not being closed

  $(document)
    .undelegate(".t_Tooltip.t_visible .close-tooltip", "click")
    .delegate(".t_Tooltip.t_visible .close-tooltip", "click", function (e) {
      Tipped.hide($(e.currentTarget).closest(".t_Tooltip")[0]);
    });

  if (typeof selector == "string") {
    $(document)
      .undelegate(selector, "mouseenter.tooltipLoad touchstart.tooltipLoad")
      .delegate(selector, "mouseenter.tooltipLoad touchstart.tooltipLoad", function (e) {
        addTooltip(this);
        Tipped.show(this);
      });
  } else {
    $(selector).each(function () {
      addTooltip(this);
    });
  }
}
