PercentSelector.handlers.touchEnd = function (event) {
  touches = event.originalEvent.touches;
  if (touches.length == 0) touches = event.originalEvent.changedTouches;
  if (touches.length > 1) return;
  var bar = touches[0].target.parentNode;
  PercentSelector.setPercentFromPageX(bar, touches[0].pageX, true);

  if (bar.onpercentchange != undefined) {
    bar.onpercentchange($(bar).attr("percent"));
  }

  event.preventDefault();
};
