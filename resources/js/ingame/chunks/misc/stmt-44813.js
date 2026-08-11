PercentSelector.handlers.mouseMove = function (event) {
  if (PercentSelector.handlers.mouseDragging) {
    event.preventDefault();
    var bar = PercentSelector.fallbackMode ? event.currentTarget : event.originalEvent.target.parentNode;
    PercentSelector.setPercentFromPageX(bar, event.pageX);
  }
};
