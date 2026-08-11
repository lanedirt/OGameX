function renderDebris(galaxyContentObject, planet, systemData) {
  $("#galaxyRow" + galaxyContentObject.position + " .cellDebris").html(
    `<a href="javascript: void(0);"><div class="microdebris ${planet.imageInformation}"></div></a>`,
  );
  $("#galaxyRow" + galaxyContentObject.position + " .cellDebris .microdebris")
    .append(addFleetContainer(galaxyContentObject.position, planet.planetType))
    .append(getFleetIcon(planet.fleet, galaxyContentObject.position, planet.planetType))
    .attr("rel", "debris" + galaxyContentObject.position)
    .addClass("tooltipRel tooltipClose tooltipRight js_hideTipOnMobile")
    .append(getDebrisTooltip(planet, galaxyContentObject, systemData));
}
