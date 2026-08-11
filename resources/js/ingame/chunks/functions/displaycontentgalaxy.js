function displayContentGalaxy(data) {
  // var selector = getTooltipSelector("#inhalt");
  //removeTooltip(selector);
  var json = $.parseJSON(data);
  $("#galaxyContent").html(json.galaxy);
  $("galaxyContent")
    .find("script")
    .each(function () {
      // http://perfectionkills.com/global-eval-what-are-the-options/
      $.globalEval($(this).text());
    });
  tabletInitGalaxyDetails(); //just for the event

  eventBDayInitGalaxy();
  $("#galaxyLoading").hide();

  if (preserveSystemOnPlanetChange) {
    $(".planetlink, .moonlink").querystring({
      galaxy: galaxy,
      system: system,
    });
  }

  getAjaxResourcebox();
}
