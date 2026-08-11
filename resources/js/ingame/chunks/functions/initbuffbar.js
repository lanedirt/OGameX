function initBuffBar() {
  let slider = $(".sliderWrapper .active_items");
  slider.anythingSlider({
    buildNavigation: false,

    /* keine navigation zu einzelnen seiten */
    buildStartStop: false,

    /* keine start oder stop buttons */
    infiniteSlides: false,

    /* round robin abschalten */
    stopAtEnd: true,
    /* zurueckspulen verhindern */
  });
  slider.removeClass("hidden");
  $(window)
    .unbind("hashchange.openBuffBar")
    .bind("hashchange.openBuffBar", function (hash) {
      var url = $.deparam.fragment(hash.fragment);

      if (typeof url["item"] != "undefined") {
        var $activateItem = $("#buffBar .activate_item");
        $("#buffBar a").removeClass("active");
        var itemUuid = url["item"];

        if (itemUuid != "") {
          if ($("#activeBuffDetails:visible").length) {
            if (typeof inventoryObj.items_inventory[itemUuid] == "undefined") {
              var $firstItem = $("#activeBuffDetails .detail_button").filter(":first");
              $.bbq.pushState({
                item: $firstItem.attr("ref"),
              });
              return;
            }

            $("#buffBar a[ref='" + url["item"] + "']").addClass("active");
            $activateItem.addClass("active");
            $("#noItems").hide();
            $("#itemDetailBox").show();
            updateItemDetails(itemUuid);
          } else {
            $("#buffBar a[ref='" + url["item"] + "']").addClass("active");
            $activateItem.addClass("active");
            gfSlider.slideIn(getElementByIdWithCache("detail"), itemUuid);
          }
        } else {
          if ($("#activeBuffDetails .detail_button").filter(":first").length === 0) {
            $("#noItems").show();
            $("#itemDetailBox").hide();
          }

          $("#activeBuffDetails .close_details").click();
          $activateItem.removeClass("active");
        }
      }
    });
  $(document)
    .undelegate("#buffBar a", "click.openDetails")
    .delegate("#buffBar a", "click.openDetails", function () {
      if ($(this).hasClass("active")) {
        $.bbq.pushState({
          item: "",
        });
      } else {
        $.bbq.pushState({
          item: $(this).attr("ref"),
        });
      }
    });
  $("#buffBar")
    .unbind("click.openDetails")
    .bind("click.openDetails", function (event) {
      if (!$(event.target).is("#buffBar .activate_item") && !$(event.target).is(".arrow a")) {
        $("#buffBar .activate_item").click();
      }
    });
  $("#buffBar .active_items div:not(.activate_item)").each(function () {
    $durationEl = $(this).find(".js_duration");
    $pusherEl = $(this).find(".pusher");
    startCooldown($durationEl, $pusherEl, 32);
  });
  $(window).trigger("hashchange");
}
