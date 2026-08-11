PercentSelector.handlers.mouseOut = function (event) {
  if (PercentSelector.handlers.mouseDragging) {
    var bar = PercentSelector.fallbackMode ? event.currentTarget : event.originalEvent.target.parentNode; //         if (bar.onpercentchange != undefined) {
    //             var x = eval(bar.onpercentchange);
    // console.debug(x);
    //             if (typeof x == 'function') {
    //                 x($(bar).attr("percent"));
    //             }
    //             // bar.onpercentchange($(bar).attr("percent"));
    //         }
  }

  PercentSelector.handlers.mouseDragging = false;
};
