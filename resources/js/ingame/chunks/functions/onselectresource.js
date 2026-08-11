function onSelectResource(e) {
  var $resource = $(e.currentTarget); // if trader was not previously selected

  if (!$resource.hasClass("active")) {
    // mark new resource as active
    $(".resource_link").removeClass("active");
    $resource.addClass("active"); // blue
    // make the button sharp

    $(".btn_calltrader").attr("disabled", false).data("offerId", $resource.data("resourceId"));
    var $getNewTrader = $(".getNewTraderDiv"); // if the get-new-trader-button is hidden XOR we click on the resource of our last (still active) trader

    if (
      ($getNewTrader.hasClass("hidden") && !$resource.hasClass("oldTraderActive")) ||
      (!$getNewTrader.hasClass("hidden") && $resource.hasClass("oldTraderActive"))
    ) {
      // switch visibility of get-new-trader-button and open-last-trader-button
      $getNewTrader.parent().children().toggleClass("hidden");
    }
  }
}
