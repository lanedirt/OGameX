function loadContentNew(galaxy, system) {
  if (!canSwitchGalaxy && notEnoughDeuteriumMessage) {
    fadeBox(notEnoughDeuteriumMessage, true);
    return;
  }

  $("#galaxyLoading").show();

  if (0 === galaxy.length || $.isNumeric(+galaxy) === false) {
    galaxy = 1;
  }

  if (0 === system.length || $.isNumeric(+system) === false) {
    system = 1;
  }

  $("input#galaxy_input").val(galaxy);
  $("input#system_input").val(system);
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

  if (inProgress === false) {
    inProgress = true;
    $.post(
      galaxyContentLink,
      {
        galaxy: galaxy,
        system: system,
        _token: token,
      },
      renderContentGalaxy,
    );
  }
}
