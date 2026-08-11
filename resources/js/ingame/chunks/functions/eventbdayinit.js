function eventBDayInit() {
  var $event_box = $(".event_box");
  $("#eventBDayWrapper").click(function (e) {
    if (
      $(e.target).attr("id") === "BDayContent" ||
      $(e.target).attr("id") === "BDayEventClose" ||
      $(e.target).closest("#BDayHeader").length > 0
    ) {
      $event_box.hide();
      $(".bday_box").removeClass("active");
    }

    e.stopPropagation();
  });
  $(".bday_box").click(function (e) {
    $(".bday_box").removeClass("active");
    var boxId = $(e.target).closest(".bday_box").attr("id").replace("box", "");
    $event_box.removeClass().addClass("event_box " + eventBoxData[boxId].eventTypeClass);

    if (eventBoxData[boxId] !== undefined) {
      $event_box.attr("id", "eventBox" + boxId).show();
      $("#box" + boxId).addClass("active");

      if (eventBoxData[boxId].eventTypeClass !== "future") {
        $("#eventBox" + boxId + " #BDayEventDate").html(eventBoxData[boxId].date + ": ");
      } else {
        $("#eventBox" + boxId + " #BDayEventDate").html("");
      }

      $("#eventBox" + boxId + " #BDayEventTitle").html(eventBoxData[boxId].title);
      $("#eventBox" + boxId + " #BDayofficerImg").attr("src", eventBoxData[boxId].eventOfficerImgSrc);

      if (eventBoxData[boxId].eventTypeClass !== "future" || eventBoxData[boxId].txtChronic !== undefined) {
        $("#eventBox" + boxId + " #BDayEventTxtChronic").html(eventBoxData[boxId].txtChronic);
        $("#eventBox" + boxId + " #BDayChronic").show();
      } else {
        $("#eventBox" + boxId + " #BDayChronic").hide();
      }

      if (eventBoxData[boxId].eventImgSrc === undefined && eventBoxData[boxId].eventTypeClass === "future") {
        $("#eventBox" + boxId + " #BDayEventImg").attr("src", "/img/icons/d995359d038c9a0c21aed16b3cc162.png");
      } else {
        $("#eventBox" + boxId + " #BDayEventImg").attr("src", eventBoxData[boxId].eventImgSrc);
      }

      if (eventBoxData[boxId].eventTxtDesc === undefined) {
        $("#eventBox" + boxId + " #BDayEventTxtDesc").html("");
      } else {
        $("#eventBox" + boxId + " #BDayEventTxtDesc").html(eventBoxData[boxId].eventTxtDesc);
      }

      $("#eventBox" + boxId + " #BDayEventPastTxt").hide();

      if (eventBoxData[boxId].eventTypeClass === "past") {
        $("#eventBox" + boxId + " #BDayEventPastTxt")
          .html(eventPastTxt)
          .show();
      }

      $("#eventBox" + boxId + " #BDayEventBtnCTA").hide();

      if (eventBoxData[boxId].eventTypeClass !== ("past" || "future") && eventBoxData[boxId].btnCTA !== undefined) {
        $("#eventBox" + boxId + " #BDayEventBtnCTA")
          .html(eventBoxData[boxId].btnCTA)
          .attr("href", eventBoxData[boxId].btnCTALink)
          .show();
      }
    }
  });
}
