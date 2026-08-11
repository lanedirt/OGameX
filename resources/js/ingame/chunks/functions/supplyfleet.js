function supplyFleet(data) {
  var data = $.parseJSON(data);

  if (data.status) {
    getAjaxResourcebox();
    /*$("#holdingTime-" + data.id).remove();
     var $holdingTime = $('<span class="countdown holdingTime" id="holdingTime-' + data.id + '"></span>')
        .show()
        .appendTo($('#holdingTimeCell'));
    */

    supplyTimes[data.id] = data.time;
    new simpleCountdown($("#holdingTime-" + data.id), data.time);
  }

  errorBoxAsArray(data["errorbox"]);
}
