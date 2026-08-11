function submitBuyRequest(event, confirmedProductionLoweredWarning) {
  let $btn = $(event.currentTarget),
    userInputAmounts = {},
    changed = false;

  if (
    $(".buy_resources.content_inner").hasClass("productionBasedPackages") &&
    $btn.data("sufficientDarkMatter") === 0
  ) {
    redirectBuyPremium();
    return;
  }

  if (typeof confirmedProductionLoweredWarning === "undefined") {
    confirmedProductionLoweredWarning = false;
  } else {
    $btn = confirmedProductionLoweredWarning;
    confirmedProductionLoweredWarning = true;
  }

  let isCapped = parseInt($btn.data("isCapped")),
    productionLowered = parseInt($btn.data("productionLowered"));
  $btn
    .parents(".fillup")
    .find(".resource_box")
    .each(function () {
      let $elem = $(this);
      let resourceName = $elem.find(".resource_name > input").data("resourceType");
      let $input = $elem.find(".resource_name input");
      userInputAmounts[resourceName] = parseInt($input.val().split(LocalizationStrings.thousandSeperator).join(""));
      changed = changed || $input.data("original") !== userInputAmounts[resourceName];
    });

  if (changed === false) {
    userInputAmounts = {};
  }

  if (productionLowered && confirmedProductionLoweredWarning !== true) {
    errorBoxDecision(loca.buyNow, loca.warnProductionLowered, loca.yes, loca.no, function () {
      submitBuyRequest(event, $btn);
    });
    return;
  }

  if (isCapped === 1 && changed === false) {
    // this can only happen for production based packages
    errorBoxDecision(loca.buyNow, loca.warnCapped, loca.yes, loca.no, function () {
      reallySubmitBuyRequest($btn);
    });
    return;
  }

  reallySubmitBuyRequest($btn, userInputAmounts);
}
