function tabletInitGalaxyDetails() {
  if (!isMobile) {
    return false;
  }

  var clickEls = ["js_planet", "js_moon", "js_debris", "js_playerName", "js_allyTag"]; //    $('.js_detailRow').hide();

  $(".js_detailRow").css("display", "none"); // $('.planetname, .planetname1').each(function() {
  //     $newNode = $('<div class="' + $(this).attr('class') + '"/>');
  //     $(this).prev().append($newNode);
  //     $newNode.html($(this).html());
  //     $(this).remove();
  // });

  $(".js_detailRow").each(function (i) {
    i = $(this).attr("rel");

    for (var j = 0; j < clickEls.length; j++) {
      var currEl = $("." + clickEls[j] + i);

      if (currEl === undefined || currEl.length == 0) {
        if (i == 16) continue;
        if (i == 17) continue;
        return;
      }

      if (currEl.attr("class").indexOf("js_no_action") >= 0) {
        continue;
      }

      currEl.unbind();
      currEl.bind("click.planet", function (e) {
        if ($(this).hasClass("active")) {
          $(".row *.active").removeClass("active"); //                    $('.js_detailRow').hide();

          $(".js_detailRow").css("display", "none");
        } else {
          $(".row *.active").removeClass("active");
          if ($(".bdaySlotBox")) $(".bdaySlotBox .name").removeClass("active"); //                    $('.js_detailRow').hide();

          $(".js_detailRow").css("display", "none"); //Show Detailbox for clicked Element

          if ($(this).html().trim()) {
            $(this).addClass("active");

            if ($(this).attr("class").indexOf("js_planet") >= 0) {
              //                            $('.js_detailRowPlanet'+ i).toggle().find('.active_row_details_content');
              elem = ".js_detailRowPlanet" + i;
            } else if ($(this).attr("class").indexOf("js_moon") >= 0) {
              //                            $('.js_detailRowMoon'+ i).toggle();
              elem = ".js_detailRowMoon" + i;
            } else if ($(this).attr("class").indexOf("js_debris") >= 0) {
              //                            $('.js_detailRowDebris'+ i).toggle();
              elem = ".js_detailRowDebris" + i;
            } else if ($(this).attr("class").indexOf("js_playerName") >= 0) {
              //                            $('.js_detailRowPlayer'+ i).toggle();
              elem = ".js_detailRowPlayer" + i;
            } else if ($(this).attr("class").indexOf("js_allyTag") >= 0) {
              //                            $('.js_detailRowAlliance'+ i).toggle();
              elem = ".js_detailRowAlliance" + i;
            }

            $(elem).css("display", "table-row");
          }
        }
      });
    }
  });
  $("a.planetMoveIcons").bind("click", function (e) {
    e.stopPropagation();
  });
}
