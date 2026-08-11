function getEventPlanetTooltip(planet, galaxyContentObject, systemData) {
  let { galaxy, system, position } = galaxyContentObject;
  let linkHTML = "";

  if (!systemData.canFly) {
    linkHTML += `<li>${loca.LOCA_FLEET_NO_FREE_SLOTS}</li>`;

    if (!systemData.hasAdmiral) {
      linkHTML += `<li><a href="${premiumLink}">${loca.LOCA_HEADER_GETADMIRAL}</a></li>`;
    }
  } else {
    planet.availableMissions.map((mission) => {
      if (mission.missionType === constants.espionage) {
        if (mission.canSpy) {
          let espionageMissionFunction = getEspionageMission(galaxyContentObject, planet, systemData);

          if (espionageMissionFunction) {
            linkHTML += `<li><a href="#"
                                onClick="${espionageMissionFunction}">
                                ${mission.name}
                            </a></li>`;
          }
        }

        if (mission.reportId && mission.reportLink) {
          linkHTML += `<li><a href="${mission.reportLink}" class="overlay">${loca.LOCA_MESSAGES_ESPIONAGEREPORT}</a></li>`;
        }
      } else {
        linkHTML += `<li><a href="${mission.link}">${mission.name}</a></li>`;
      }
    });
  }

  return `
        <div id="planet${position}" style="display: none;" class="htmlTooltip galaxyTooltip">
            <h1>${loca.LOCA_ALL_PLANET}: <span class="textNormal">${planet.planetName}</span></h1>
            <div class="splitLine"></div>
            <ul class="ListImage">
                <li><span>[${galaxy}:${system}:${position}]</span></li>
                <li><img src="${planet["imageInformation"]}" alt="" height="30" width="30"></li>
            </ul>
            <ul class="ListLinks">
                ${linkHTML}
            </ul>
        </div>
        `;
}
