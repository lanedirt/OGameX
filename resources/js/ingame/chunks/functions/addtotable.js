function addToTable(strDataResult, strClass, shipCount) {
  let text = strDataResult.message || strDataResult;

  if (shipCount != null) {
    text += " (" + tsdpkt(shipCount) + ") " + LocalizationStrings.ok;
  }

  if (isMobile) {
    fadeBox(text, strClass != "success");
    return;
  }

  let currentTime = new Date();
  let id = "fleetstatus" + currentTime.getTime();
  let idHtml = 'id="' + id + '"';
  let myClass = 'class="' + strClass + '"';
  let div = "<div " + idHtml + " " + myClass + ">" + text + "</div>";

  if ($("#fleetstatusrow").has("div").length) {
    $("#fleetstatusrow").empty();
  }

  $(div)
    .prependTo("#fleetstatusrow")
    .fadeOut(3000, function () {
      $(this).remove();
    });
}
