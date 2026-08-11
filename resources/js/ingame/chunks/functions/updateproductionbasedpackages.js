function updateProductionBasedPackages(collectionData, myPackageType, $myButton, $currentPackage, darkMatter) {
  if (!collectionData["isBuyable"]) {
    $myButton.attr("disabled", "disabled");
    $currentPackage.attr("disabled", "disabled");
    $currentPackage.addClass("disabled");
    $currentPackage.find(".fillup_cost").addClass("overmark").html("-");
  } else if (darkMatter < collectionData["displayCosts"]) {
    $currentPackage.find(".fillup_cost .premium_txt").addClass("overmark").html(tsdpkt(collectionData["displayCosts"]));
    $currentPackage.find(".btn_wrap>a").data("sufficientDarkMatter", 0);
  } else {
    $currentPackage.find(".fillup_cost .premium_txt").html(tsdpkt(collectionData["displayCosts"]));
  }

  $myButton.data("premiumCosts", collectionData["displayCosts"]);
  $currentPackage.find(".resource_box").each(function () {
    let $box = $(this);

    for (let resourceName in collectionData["amounts"]) {
      if ($box.find(".resource_img").hasClass(resourceName)) {
        let $amountDisplay = $box.find(".resource_name");
        $amountDisplay
          .find("input")
          .val(collectionData["amounts"][resourceName])
          .data("original", collectionData["amounts"][resourceName]);

        if (collectionData["isResourceCapped"][resourceName]) {
          $amountDisplay.addClass("overmark");
        }

        if (collectionData["isCapped"]) {
          $myButton.data("isCapped", 1);
        } else {
          $myButton.data("isCapped", 0);
        }
      }
    }
  });
}
