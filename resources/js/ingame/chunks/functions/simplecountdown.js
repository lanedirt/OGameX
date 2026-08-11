/*
 * Einfacher Countdown mit Funktionsaufruf nach Ende des Countdowns
 */

function simpleCountdown(htmlObj, leftoverTime, countdownDoneFunction, countdownTickFunction) {
  if (typeof htmlObj !== "object") {
    return;
  }

  var thisObj = this; // diese elemente werden veraendert

  thisObj.timeHtmlObj = $(htmlObj)[0];

  this.updateCountdown = function () {
    var timestamp = thisObj.countdownObject.getLeftoverTime();
    var timestring = thisObj.countdownObject.getCurrentTimestring();

    if (timestamp > 0) {
      $("#" + thisObj.timeHtmlObj.id).text(timestring);

      if (typeof countdownTickFunction == "string" && $.isFunction(window[countdownTickFunction])) {
        window[countdownTickFunction]();
      } else if ($.isFunction(countdownTickFunction)) {
        countdownTickFunction();
      }
    } else {
      timerHandler.removeCallback(thisObj.timer);
      $("#" + thisObj.timeHtmlObj.id).text(LocalizationStrings.status.ready);

      if (typeof countdownDoneFunction == "string" && $.isFunction(window[countdownDoneFunction])) {
        window[countdownDoneFunction]();
      } else if ($.isFunction(countdownDoneFunction)) {
        countdownDoneFunction();
      }
    }
  };

  if (typeof thisObj.timer != "undefined") {
    timerHandler.removeCallback(thisObj.timer);
  }

  if (thisObj.timeHtmlObj) {
    // oldcountdown objekt
    thisObj.countdownObject = new oldcountdown(leftoverTime, 3);
    thisObj.timer = timerHandler.appendCallback(thisObj.updateCountdown);
    thisObj.updateCountdown();
  }
}
