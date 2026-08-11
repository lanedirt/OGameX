function updateItemDetails(uuid) {
  var item = getItem(uuid);
  $("#activeBuffDetails .detail_button").removeClass("active");
  $('#activeBuffDetails .detail_button[ref="' + uuid + '"]').addClass("active");
  $("#activeBuffDetails .js_itemName").html(item.name);
  $("#activeBuffDetails .js_itemEffect").html(item.effect);
  $("#activeBuffDetails .js_itemAmount").html(item.amount);

  if (item.firstStatus) {
    $("#activeBuffDetails .js_itemDurationStatus").html(translation.durationType[item.firstStatus]);
  } else {
    $("#activeBuffDetails .js_itemDurationStatus").html(translation.durationType.effecting);
  }

  if (item.duration) {
    if (item.durationExtension) {
      $("#activeBuffDetails .js_itemDuration").html(
        formatTimeWrapper(item.duration, 2, true, " ", false, "") + item.durationExtension,
      );
    } else {
      $("#activeBuffDetails .js_itemDuration").html(formatTimeWrapper(item.duration, 2, true, " ", false, ""));
    }
  } else if (item.duration === null) {
    if (item.moonOnlyItem) {
      $("#activeBuffDetails .js_itemDuration").html(translation.permanentMoon);
    } else {
      $("#activeBuffDetails .js_itemDuration").html(translation.permanent);
    }
  } else {
    $("#activeBuffDetails .js_itemDuration").html(translation.now);
  }

  if (item.timeLeft) {
    $("#activeBuffDetails .js_itemTimeLeftTxt").show();
    $("#activeBuffDetails .js_itemTimeLeft").html(formatTimeWrapper(item.timeLeft, 2, true, " ", false, ""));
  } else {
    $("#activeBuffDetails .js_itemTimeLeftTxt").hide();
  }

  var $activateBtn = $("#activationButton");
  $activateBtn.attr("ref", uuid);
  $activateBtn.removeClass("buyAndActivate activateItem build-it_disabled build-it dm bp").addClass(item.currency);

  if (item.amount > 0) {
    $activateBtn
      .addClass("activateItem")
      .html("<span>" + (item.timeLeft > 0 && item.extendable ? translation.extend : translation.activate) + "</span>")
      .addClass(item.canBeActivated ? "build-it" : "build-it_disabled");
  } else {
    var buyString = item.timeLeft > 0 && item.extendable ? translation.buyAndExtend : translation.buyAndActivate;
    buyString = buyString.replace(/%price%/, tsdpkt(item.costs));
    buyString = buyString.replace(/%currency%/, translation.currencies[item.currency]);
    $activateBtn
      .addClass("buyAndActivate")
      .html("<span>" + buyString + "</span>")
      .addClass(item.canBeBoughtAndActivated && item.hasEnoughCurrency ? "build-it" : "build-it_disabled");
  }
}
