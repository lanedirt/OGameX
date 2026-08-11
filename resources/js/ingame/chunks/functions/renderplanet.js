function renderPlanet(galaxyContentObject, planet, systemData) {
  $("#galaxyRow" + galaxyContentObject.position + " .cellPlanetName").html(
    `<span class="${galaxyContentObject.player.isBuddy ? "status_abbr_buddy" : ""}">${planet.planetName}</span>`,
  );
  $("#galaxyRow" + galaxyContentObject.position + " .cellPlanet").html(
    `<a href="javascript: void(0);" onclick="${getEspionageMission(galaxyContentObject, planet, systemData)}"><div class="microplanet"></div></a>`,
  );
  $("#galaxyRow" + galaxyContentObject.position + " .cellPlanet .microplanet")
    .addClass(planet.imageInformation)
    .append(getActivityStar(planet.activity))
    .append(addFleetContainer(galaxyContentObject.position, planet.planetType))
    .append(getFleetIcon(planet.fleet, galaxyContentObject.position, planet.planetType))
    .attr("data-planet-id", planet.planetId)
    .addClass("planetTooltip tooltipRel tooltipPersistent tooltipClose tooltipRight js_hideTipOnMobile")
    .attr("rel", "planet" + galaxyContentObject.position)
    .append(getPlanetTooltip(planet, galaxyContentObject, systemData));
}
