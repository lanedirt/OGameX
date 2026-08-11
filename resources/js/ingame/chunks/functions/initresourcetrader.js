function initResourceTrader() {
  $(".big_tabs").tabs({
    activate: hideTipsOnTabChange,
  });
  $(".resource_link").on("click", onSelectResource);
  $(".btn_calltrader").on("click", callTrader);
}
