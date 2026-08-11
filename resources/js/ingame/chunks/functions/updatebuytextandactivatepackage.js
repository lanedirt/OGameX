function updateBuyTextAndActivatePackage($buttonElem, $package) {
  $buttonElem
    .html(
      !$(".buy_resources.content_inner").hasClass("productionBasedPackages") || $buttonElem.data("sufficientDarkMatter")
        ? loca.buyNow
        : loca.getDM,
    )
    .attr("class", "btn_premium small");
  $package.addClass("premium");
}
