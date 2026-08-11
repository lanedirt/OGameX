function countdownWithTickFunction(
  htmlObj,
  leftoverTime,
  totalTime,
  countdownDoneFunction,
  countdownTickFunction,
  maxDigits,
) {
  if (typeof htmlObj !== "object") {
    return;
  }

  var thisObj = this; // diese elemente werden veraendert

  thisObj.timeHtmlObj = htmlObj;

  if (typeof $(htmlObj).attr("data-oldcountdown") != "undefined") {
    timerHandler.removeCallback($(htmlObj).attr("data-oldcountdown"));
  }

  this.updateCountdown = function () {
    timestamp = thisObj.countdown.getLeftoverTime();
    timestring = thisObj.countdown.getCurrentTimestring();

    if (timestamp > 0) {
      thisObj.timeHtmlObj.innerHTML = timestring;

      if (typeof countdownTickFunction == "string" && $.isFunction(window[countdownTickFunction])) {
        window[countdownTickFunction](timestamp, totalTime);
      } else if ($.isFunction(countdownTickFunction)) {
        countdownTickFunction(timestamp, totalTime);
      }
    } else {
      timerHandler.removeCallback(thisObj.timer);
      thisObj.timeHtmlObj.innerHTML = LocalizationStrings.status.ready;

      if (typeof countdownDoneFunction == "string" && $.isFunction(window[countdownDoneFunction])) {
        window[countdownDoneFunction]();
      } else if ($.isFunction(countdownDoneFunction)) {
        countdownDoneFunction();
      }
    }
  };

  if (thisObj.timeHtmlObj) {
    // oldcountdown objekt
    thisObj.countdown = new oldcountdown(leftoverTime, maxDigits);
    thisObj.timer = timerHandler.appendCallback(thisObj.updateCountdown);
    thisObj.updateCountdown();
    $(htmlObj).attr("data-oldcountdown", thisObj.timer);
  }

  return thisObj;
}
