function movementImageCountdown(htmlObj, leftoverTime, duration, isReturn, isRTL, routeLength) {
  if (typeof htmlObj !== "object") {
    return;
  }

  var thisObj = this; // diese elemente werden veraendert

  thisObj.timeHtmlObj = htmlObj;

  this.updateCountdown = function () {
    thisObj.countdown.getCurrentTimestring();
    var timestamp = thisObj.countdown.getLeftoverTime();
    var timestring = thisObj.countdown.getCurrentTimestring();

    if (timestamp > 0) {
      percent = clampFloat(timestamp / duration, 0.0, 1.0);

      if (!isReturn) {
        pixel = Math.abs(routeLength - routeLength * percent);
      } else {
        pixel = Math.abs(routeLength * percent);
      }

      pixel = clampInt(Math.round(pixel), 0, routeLength);

      if (isRTL) {
        thisObj.timeHtmlObj.style["marginRight"] = pixel + "px";
      } else {
        thisObj.timeHtmlObj.style["marginLeft"] = pixel + "px";
      }
    }
  };

  if (thisObj.timeHtmlObj) {
    // oldcountdown objekt
    thisObj.countdown = new oldcountdown(leftoverTime, 3);
    timerHandler.appendCallback(thisObj.updateCountdown);
    thisObj.updateCountdown();
  }
}
