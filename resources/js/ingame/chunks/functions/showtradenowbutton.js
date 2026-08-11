function showTradeNowButton() {
  if ($("#callTrader").hasClass("traderActive")) {
    $("#callTrader").show();
  } else {
    $("#callTrader").hide();
  }
}
