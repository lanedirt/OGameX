function initItemActivation(currItemUuid) {
  inventoryObj.initalizeSlider(
    inventoryObj.items_inventory,
    "js_activeItemSlider",
    395,
    172,
    " ",
    true,
    " ",
    true,
    true,
  );

  if (typeof currItemUuid == "undefined" || currItemUuid.length == 0 || currItemUuid == 1) {
    // we can use this selector, because all Items in the set can be activated
    var $firstItem = $(".item_img_box .detail_button").filter(":first"); // there can only be no items if we're on a moon:

    if ($firstItem.length === 0) {
      $("#noItems").show();
      $("#itemDetailBox").hide();
      return;
    } else {
      $.bbq.pushState({
        item: $firstItem.attr("ref"),
      });
    }
  } else {
    $.bbq.pushState({
      item: currItemUuid,
    });
    $(window).trigger("hashchange");
  } // show cooldown for all active items:

  $("#activeBuffDetails .js_is_active").each(function () {
    $durationEl = $(this).parent().siblings(".js_duration");
    $pusherEl = $(this).parent().siblings(".pusher");
    startCooldown($durationEl, $pusherEl, 75);
  });
  $(document)
    .undelegate("#activeBuffDetails .detail_button", "click.updateItemDetails")
    .delegate("#activeBuffDetails .detail_button", "click.updateItemDetails", function () {
      if ($(this).hasClass("active")) {
        return;
      }

      $("#activeBuffDetails .detail_button").removeClass("active");
      $(this).addClass("active");
      $.bbq.pushState({
        item: $(this).attr("ref"),
      });
    })
    .undelegate("#activeBuffDetails .build-it", "click.activateItem")
    .delegate("#activeBuffDetails .build-it", "click.activateItem", function () {
      activateItem($(this).attr("ref"));
    })
    .undelegate("#activeBuffDetails .buyAndActivate.dm.build-it_disabled", "click.activateItem")
    .delegate("#activeBuffDetails .buyAndActivate.dm.build-it_disabled", "click.activateItem", function () {
      if (vacation) {
        return;
      }

      var uuid = $(this).attr("ref");

      if ($("#js_activeItemSlider>li a[ref='" + uuid + "']").length == 0) {
        return;
      }

      errorBoxDecision(
        LocalizationStrings.error,
        translation.buyDMDecision,
        LocalizationStrings.yes,
        LocalizationStrings.no,
        function () {
          window.location.href = $("#darkmatter_box a").attr("href");
        },
      );
    })
    .undelegate("#activeBuffDetails .close_detail", "click.changeHash")
    .delegate("#activeBuffDetails .close_detail", "click.changeHash", function () {
      $.bbq.pushState({
        item: "",
      });
    });
}
