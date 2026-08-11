function initBuyResources() {
  refreshBars("bar_container", "filllevel_bar");
  $(".fill_resource").on("click", ".fillup", onChangeToPremium).on("click", ".btn_premium", submitBuyRequest);
  $(".fillup").on("keyup", ".resource_name input", handleInputForResourcePackages);
  initThousandSeparator();
}
