function updateSimShortInfo(element) {
  if (!element || !element.length) {
    return;
  }

  $("combatsim-shortinfo .shortSimId span").text(element.find(".shortSimId span").first().text());
  $("combatsim-shortinfo .shortTarget span").text(element.find(".shortTarget span").first().text());
  $("combatsim-shortinfo .shortAttackerCount span").text(element.find(".shortAttackerCount span").first().text());
  $("combatsim-shortinfo .shortDefenderCount span").text(element.find(".shortDefenderCount span").first().text());
  $("combatsim-shortinfo .shortShipCount span").text(element.find(".shortShipCount span").first().text());
}
