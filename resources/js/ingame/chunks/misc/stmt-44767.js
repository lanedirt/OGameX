PercentSelector.handlers.touchMove = function (event) {
  PercentSelector.handlers.touchDragging = true;
  var touches = event.originalEvent.touches;
  if (touches.length > 1) return;
  event.preventDefault();
  PercentSelector.setPercentFromPageX(touches[0].target.parentNode, touches[0].pageX);
};
