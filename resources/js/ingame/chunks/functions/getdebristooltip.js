function getDebrisTooltip(planet, galaxyContentObject, systemData) {
  let { galaxy, system, position } = galaxyContentObject;
  let { metal, crystal, deuterium } = planet.resources;
  let recyclersToSend = Math.min(
    planet.requiredShips,
    position === 16 ? systemData.availablePathfinders : systemData.availableRecyclers,
  );
  let linkHTML = "";

  if (!systemData.canFly) {
    linkHTML += `<li>${loca.LOCA_FLEET_NO_FREE_SLOTS}</li>`;

    if (!systemData.hasAdmiral) {
      linkHTML += `<li><a href="${premiumLink}">${loca.LOCA_HEADER_GETADMIRAL}</a></li>`;
    }
  } else if (
    recyclersToSend &&
    (position === 16 ? systemData.availablePathfinders : systemData.availableRecyclers) &&
    planet.recyclePossible
  ) {
    linkHTML = `<li><a href="#"
            onClick="sendShips(${8}, ${galaxyContentObject.galaxy}, ${galaxyContentObject.system}, ${galaxyContentObject.position}, ${planet.planetType}, ${recyclersToSend});return false;">
                ${loca.LOCA_GALAXY_DEBRIS_REDUCE}
            </a></li>`;
  } else {
    linkHTML = `<li><span class="inactiveLink">${loca.LOCA_GALAXY_DEBRIS_REDUCE}</span></li>`;
  }

  return `
        <div id="debris${position}" style="display: none;" class="htmlTooltip galaxyTooltip">
            <h1>${loca.LOCA_FLEET_DEBRIS}</h1>
            <div class="splitLine"></div>
            <ul class="ListImage">
                <li><span id="pos-debris">[${galaxy}:${system}:${position}]</span></li>
                <li><div class="debrisTooltip microdebris ${planet["imageInformation"]}"></div></li>
            </ul>
            <ul class="ListLinks">
                <li class="debris-content">${loca.LOCA_ALL_METAL}: ${number_format(metal.amount, 0)}</li>
                <li class="debris-content">${loca.LOCA_ALL_CRYSTAL}: ${number_format(crystal.amount, 0)}</li>
                <li class="debris-content">${loca.LOCA_ALL_DEUTERIUM}: ${number_format(deuterium.amount, 0)}</li>
                <li class="debris-recyclers">${position === 16 ? loca.LOCA_GALAXY_PATHFINDER_NEEDED : loca.LOCA_GALAXY_RECYCLER_NEEDED}: ${number_format(planet.requiredShips, 0)}</li>
                ${linkHTML}
            </ul>
        </div>
        `;
}
