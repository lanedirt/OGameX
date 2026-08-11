/*
 * Countdown fuer die Eventliste
 */

function eventboxCountdown(htmlObj, leftoverTime, parentElement, checkEventsUrl, checkEventIds) {
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
      $(thisObj.timeHtmlObj).html(timestring);
    } else {
      timerHandler.removeCallback(thisObj.timer);
      $(thisObj.timeHtmlObj).html(LocalizationStrings.status.ready); // checkEvents NICHT spammen:

      if (!timerHandler.checkEventsAlreadyQueued) {
        timerHandler.checkEventsAlreadyQueued = true;
        setTimeout(function () {
          $.post(
            checkEventsUrl,
            {
              ids: checkEventIds,
            },
            function (data) {
              var rowIDs = $.parseJSON(data);

              for (var index in rowIDs["rows"]) {
                $(parentElement)
                  .find("#eventRow-" + rowIDs["rows"][index])
                  .remove();
                $(".union" + rowIDs["rows"][index]).remove();
              }

              $(".eventFleet").removeClass("odd");
              $(".partnerInfo").removeClass("part-even");
              $(".eventFleet:odd").addClass("odd");
              $(".partnerInfo:even").addClass("part-even");
              timerHandler.checkEventsAlreadyQueued = false;
            },
          );
        }, 2500);
      } // else: wir sind noch innerhalb der 2,5 Sekunden vom letzten Aufruf (durch anderes Event)
    }
  };

  if (thisObj.timeHtmlObj) {
    // oldcountdown objekt
    thisObj.countdown = new oldcountdown(leftoverTime, 3);
    thisObj.timer = timerHandler.appendCallback(thisObj.updateCountdown);
    thisObj.updateCountdown();
  }
}
