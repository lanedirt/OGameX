function closeTradeResourcesOverlay(doNotDisableCallTrader) {
  $(".overlayDiv.traderlayer").remove();
  traderObj.reloadResources();

  if (!doNotDisableCallTrader) {
    $(".call_trader_box").children().toggleClass("hidden");
    $("#callTrader").removeClass("traderActive").hide();
    $(".btn_calltrader").attr("disabled", true);
    $(".resource_link").removeClass("oldTraderActive active");
    $("#activeTrader").hide();
  }
}
