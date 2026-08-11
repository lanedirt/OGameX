function updatePercentageBasedPackages(dataFromBuy, myPackageType, $myButton, $currentPackage, $largePackageNumber) {
  if (!dataFromBuy["isBuyable"]) {
    $myButton.attr("disabled", "disabled");
    $currentPackage.attr("disabled", "disabled");
    $currentPackage.addClass("disabled");
  }

  $myButton.data("premiumCosts", dataFromBuy["costs"]);
  $myButton.data("premiumValue", dataFromBuy["resources"]);
  $myButton.data("newValueFormatted", dataFromBuy["newValueFormatted"]);

  if (dataFromBuy["displayCosts"]) {
    $currentPackage.find(".fillup_cost .premium_txt").html(dataFromBuy["formattedCosts"]);
  } else {
    $currentPackage.find(".fillup_cost").addClass("overmark").html("-"); // also kills span with .premium_txt
  }

  if (myPackageType === $largePackageNumber) {
    var buyButtonClass = $myButton.data("buyButtonClass");

    if (!dataFromBuy["isCapped"] && $currentPackage.children("." + buyButtonClass).length) {
      // package was capped but isn't capped anymore
      $currentPackage
        .children("." + buyButtonClass)
        .removeClass(buyButtonClass)
        .addClass("fillup_100percent");
      $currentPackage.children(".fillup_txt").html(loca.fillUpTo);
    }
  }
}
