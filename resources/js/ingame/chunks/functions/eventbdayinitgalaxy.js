/*
    Document   : eventBirthday
    Created on : 31.08.2012, 12:16:13
    Author     : stefanie.knoth
    Description:
        This file contains the javascript that is neccessary for the Birthday
        Event
*/
function eventBDayInitGalaxy() {
  if (isMobile) {
    $(".js_bday_details").hide();
    $(".bdaySlotBox .name").click(function (e) {
      $(".row")
        .children()
        .each(function () {
          if ($(this).html().trim()) $(this).removeClass("active");
        });
      $(".bdaySlotBox .name").removeClass("active");
      $(".js_detailRow").hide();
      $(this).toggleClass("active");

      if ($(e.target).attr("class").indexOf("planet") !== -1) {
        $(".js_detailRowPlanet17").toggle();
      } else if ($(e.target).attr("class").indexOf("debris") !== -1) {
        $(".js_detailRowDebris17").toggle();
      }
    });
  }
}
