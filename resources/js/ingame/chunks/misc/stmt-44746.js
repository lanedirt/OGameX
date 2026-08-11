PercentSelector.handlers.touchStart = function (event) {
  var touches = event.originalEvent.touches;
  if (touches.length > 1) return;
  event.preventDefault();
  PercentSelector.handlers.touchDragging = false;
};
