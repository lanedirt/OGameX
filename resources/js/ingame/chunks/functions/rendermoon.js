function renderMoon(galaxyContentObject, planet, systemData) {
  $("#galaxyRow" + galaxyContentObject.position + " .cellMoon").html(
    `<a href="javascript: void(0);" onclick="${getEspionageMission(galaxyContentObject, planet, systemData)}"><div class="micromoon ${planet.imageInformation}"></div></a>`,
  );
  $("#galaxyRow" + galaxyContentObject.position + " .cellMoon .micromoon")
    .append(getActivityStar(planet.activity))
    .append(addFleetContainer(galaxyContentObject.position, planet.planetType))
    .append(getFleetIcon(planet.fleet, galaxyContentObject.position, planet.planetType))
    .attr("data-moon-id", planet.planetId)
    .attr("rel", "moon" + galaxyContentObject.position)
    .addClass("tooltipRel tooltipClose tooltipRight js_hideTipOnMobile")
    .append(getMoonTooltip(planet, galaxyContentObject, systemData));
}
