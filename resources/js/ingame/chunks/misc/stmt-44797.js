PercentSelector.handlers.mouseUp = function (event) {
  PercentSelector.handlers.mouseDragging = false;
  var bar = PercentSelector.fallbackMode ? event.currentTarget : event.originalEvent.target.parentNode;
  PercentSelector.setPercentFromPageX(bar, event.pageX, true); // if (bar.onpercentchange != undefined) {
  //     var x = eval(bar.onpercentchange);
  //
  //     if (typeof x == 'function') {
  //         x($(bar).attr("percent"));
  //     }
  //     // bar.onpercentchange($(bar).attr("percent"));
  // }
  // if(bar.onpercentchange != undefined) {
  //     bar.onpercentchange($(bar).attr("percent"));
  // }
};
