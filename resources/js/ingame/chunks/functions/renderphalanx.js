function renderPhalanx(galaxyContentObject) {
  let { player } = galaxyContentObject;

  if (player && galaxyContentObject.actions.canPhalanx) {
    if (galaxyContentObject.actions.phalanxInactive) {
      // Show inactive phalanx with tooltip
      $("#galaxyRow" + galaxyContentObject.position + " .cellPlanetName").append(
        `<div class="tooltip js_hideTipOnMobile phalanxInctive" title="${galaxyContentObject.actions.phalanxInactiveReason}"></div>`,
      );
    } else {
      // Show active phalanx with click handler
      $("#galaxyRow" + galaxyContentObject.position + " .cellPlanetName").append(
        '<a class="phalanxlink" href="javascript:void(0);"></a>',
      );
      $("#galaxyRow" + galaxyContentObject.position + " .cellPlanetName .phalanxlink")
        .append(`<div class="tooltip js_hideTipOnMobile phalanxActive"></div>`)
        .attr("title", "Use phalanx");

      // Add onclick handler for phalanx scan
      $("#galaxyRow" + galaxyContentObject.position + " .cellPlanetName .phalanxlink").on("click", function (e) {
        e.preventDefault();
        scanWithPhalanx(galaxyContentObject.galaxy, galaxyContentObject.system, galaxyContentObject.position);
      });
    }
  }
}
