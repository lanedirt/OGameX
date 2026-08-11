/*
 *	allgemeiner Countdown
 */

function oldcountdown(leftoverTime, maxDigits, countValue) {
  if (maxDigits == null || maxDigits == "") {
    maxDigits = 2;
  }

  var thisObj = this;
  thisObj.countValue = parseInt(countValue) || -1; // config

  thisObj.timestamp = 0;
  thisObj.maxDigits = parseInt(maxDigits); // bei 2 werden keine Sekunden gezeigt, wenn der Zeitraum > 1 h ist

  thisObj.delimiter = " "; // Trennzeichen

  thisObj.approx = ""; // wird vor Zeitstring angefuegt

  thisObj.showunits = true; // Einheiten zeigen

  thisObj.zerofill = false; // nullen auffuellen

  var localTime = new Date();
  thisObj.startTime = localTime.getTime(); // Script-Startzeit

  thisObj.startLeftoverTime = parseInt(leftoverTime); // Sekunden Restzeit

  this.getCurrentTimestring = function () {
    return formatTimeWrapper(
      thisObj.getLeftoverTime(),
      thisObj.maxDigits,
      thisObj.showunits,
      thisObj.delimiter,
      thisObj.zerofill,
      thisObj.approx,
    );
  };

  this.getLeftoverTime = function () {
    var currTime = new Date();
    return Math.round(
      thisObj.startLeftoverTime + ((currTime.getTime() - thisObj.startTime) * thisObj.countValue) / 1000,
    );
  };
}
