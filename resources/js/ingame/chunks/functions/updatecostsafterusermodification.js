function updateCostsAfterUserModification($clickedInput) {
  let $package = $clickedInput.parents(".fillup"),
    $button = $package.find(".btn_wrap>a"),
    $priceDisplay = $package.find(".premium_txt"),
    $resourceBoxes = $package.find(".resource_box"),
    newPrice = 0,
    darkMatter = $(".buy_resources").data("darkMatter");
  $resourceBoxes.each(function () {
    let $input = $(this).find(".resource_name input");
    let currentInputValue = parseInt($input.val().split(LocalizationStrings.thousandSeperator).join("")) || 0;
    let currentPrice = 0;

    if ($input.data("original") > 0) {
      currentPrice = Math.round(($input.data("originalPrice") * currentInputValue) / $input.data("dailyProduction"));
    }

    if (currentInputValue > 0 && currentPrice < $button.data("minPremiumCosts")) {
      currentPrice = $button.data("minPremiumCosts");
    }

    newPrice += currentPrice;
  });
  $priceDisplay.html(tsdpkt(newPrice));
  $button.data("premiumCosts", newPrice);

  if (darkMatter < newPrice) {
    $priceDisplay.addClass("overmark");
    $button.data("sufficientDarkMatter", 0);
  } else {
    $priceDisplay.removeClass("overmark");
    $button.data("sufficientDarkMatter", 1);
  } // switch text and set to premium

  updateBuyTextAndActivatePackage($button, $package);
}
