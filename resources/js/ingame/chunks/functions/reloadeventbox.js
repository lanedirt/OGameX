function reloadEventbox(data) {
  var evalData;

  if (typeof data === "string") {
    evalData = $.parseJSON(data);
  } else {
    evalData = data;
  }

  var type = typeof evalData["eventText"];
  var actionSum = parseInt(evalData["friendly"]) + parseInt(evalData["neutral"]) + parseInt(evalData["hostile"]);

  if (actionSum > 0) {
    var $eventList;
    var $eventDetails = $('<p class="event_list">');

    if ($("body").attr("id") === "galaxy") {
      $eventDetails
        .append(
          '<span class="next_event">' +
            eventboxLoca.nextEvent +
            ': <span class="countdown" id="tempcounter" name="countdown"></span></span>',
        )
        .append(
          '<span class="next_event">' +
            eventboxLoca.nextEventText +
            ': <span class="' +
            evalData["eventType"] +
            '">' +
            evalData["eventText"] +
            "</span></span>",
        );
      $eventList = $eventDetails;
    } else {
      var missions = actionSum === 1 ? eventboxLoca.mission : eventboxLoca.missions;
      $eventList = $('<p class="event_list">' + actionSum + " " + missions + ": </p>");

      if (evalData["friendly"]) {
        $eventList.append('<span class="undermark">' + evalData["friendly"] + " " + eventboxLoca.friendly + "</span>");
      }

      if (evalData["neutral"]) {
        if (evalData["friendly"]) {
          $eventList.append(", ");
        }

        $eventList.append('<span class="middlemark">' + evalData["neutral"] + " " + eventboxLoca.neutral + "</span>");
      }

      if (evalData["hostile"]) {
        if (evalData["friendly"] || evalData["neutral"]) {
          $eventList.append(", ");
        }

        $eventList.append('<span class="overmark">' + evalData["hostile"] + " " + eventboxLoca.hostile + "</span>");
      }

      $eventDetails
        .append(
          '<span class="next_event">' +
            eventboxLoca.nextEvent +
            ': <span class="countdown" id="tempcounter" name="countdown"></span></span>',
        )
        .append(
          '<span class="next_event">' +
            eventboxLoca.nextEventText +
            ': <span class="' +
            evalData["eventType"] +
            '">' +
            evalData["eventText"] +
            "</span></span>",
        );
      $eventList.append($eventDetails);
    }

    $("#eventboxFilled p.event_list").remove();
    $("#eventboxFilled").prepend($eventList);
  }

  if (type === "string" || type === "undefined") {
    $("#eventboxLoading").hide();

    if (actionSum > 0) {
      $("#eventboxBlank").hide();
      $("#eventboxFilled").show(); // Dieser Countdown sorgt dafuer, dass nach dem Ablauf des aktuell angezeigten Events jeweils das naechste
      // geladen wird. Das 3 Sek. Delay sorgt dafuer, dass es nicht beliebig haeufig deswegen neu laedt...
      // Da es immer nur eine Eventbox gibt, muss der Kram im Gegensatz zu der Eventliste nicht weiter
      // abgesichert werden.

      if (reloadEventBoxTimer !== null) {
        timerHandler.removeCallback(reloadEventBoxTimer.timer);
      }

      reloadEventBoxTimer = new simpleCountdown(
        getElementByIdWithCache("tempcounter"),
        evalData["eventTime"],
        function () {
          setTimeout(getAjaxEventbox, 3000);
        },
      );
    } else {
      $("#eventboxBlank").show();
      $("#eventboxFilled").hide();
    }
  }
}
