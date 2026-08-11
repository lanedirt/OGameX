function recallShipCountdown(fleetId, currentRecallTime) {
  var thisObj = this;
  var $element = $(".reversal_time[ref='" + fleetId + "']");

  if (isMobile && $element.length) {
    this.updateCountdown = function () {
      var timestamp = thisObj.countdown.getLeftoverTime();
      var formattedDate = getFormatedDate(new Date(timestamp * 1000 + timeDiff), "[d].[m].[Y] [H]:[i]:[s]");
      $element.html(formattedDate);
    }; // countdown objekt

    thisObj.countdown = new oldcountdown(currentRecallTime, 3, 2);
    thisObj.timer = timerHandler.appendCallback(thisObj.updateCountdown);
    thisObj.updateCountdown();
  }
}
