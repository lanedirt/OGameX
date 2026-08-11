function loadContent(galaxy, system) {
  $("#galaxyLoading").show();

  if (0 === galaxy.length || !$.isNumeric(+galaxy)) {
    galaxy = 1;
  }

  if (0 === system.length || !$.isNumeric(+system)) {
    system = 1;
  }

  $("#galaxy_input").val(galaxy);
  $("#system_input").val(system);
  let phalanxSystemLink = $("#galaxyHeader .phalanxlink.btn_system_action");

  if (phalanxSystemLink.length) {
    phalanxSystemLink.attr(
      "href",
      phalanxSystemLink
        .attr("href")
        .replace(/(galaxy=)\d+/, "$1" + galaxy)
        .replace(/(system=)\d+/, "$1" + system),
    );
  }

  $.post(
    contentLink,
    {
      galaxy: galaxy,
      system: system,
      _token: token,
    },
    displayContentGalaxy,
  );
}
