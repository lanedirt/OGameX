function getPlanetOrMoonTooltipLinks(planet, galaxyContentObject, systemData) {
  let linkHTML = getActivityElement(planet.activity);
  let { currentPlanetId } = systemData;

  if (planet.planetId === currentPlanetId) {
    linkHTML += loca.LOCA_FLEET_NO_ACTION_AVAILABLE;
    return linkHTML;
  }

  let { galaxy, system, position, player } = galaxyContentObject;

  if (player.isOnVacation) {
    linkHTML += loca.LOCA_FLEET_PLAYER_UMODE;
    return linkHTML;
  }

  if (!systemData.canFly) {
    linkHTML += `<li>${loca.LOCA_FLEET_NO_FREE_SLOTS}</li>`;

    if (!systemData.hasAdmiral) {
      linkHTML += `<li><a href="${premiumLink}">${loca.LOCA_HEADER_GETADMIRAL}</a></li>`;
    }

    return linkHTML;
  }

  if (planet.availableMissions) {
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

    if (galaxyContentObject.actions.canMissileAttack && !player.isAdmin && systemData.availableMissiles > 0) {
      let holdMissionAvailable = planet.availableMissions.find((availMission) => availMission.missionType === 5);

      if (systemData.showOutlawWarning && !systemData.isOutlaw && player.isStrong && !holdMissionAvailable) {
        linkHTML += `<li><a href="#"
                            onClick="outlawWarning(${constants.missleattack}, ${galaxy}, ${system}, ${position}, ${planet.planetType}, ${systemData.availableMissiles});return false;">
                            ${loca.LOCA_FLEET_MISSILEATTACK}
                        </a></li>`;
      } else {
        linkHTML += `<li><a class="overlay" href="${galaxyContentObject.actions.missileAttackLink}&planetType=${planet.planetType}" data-overlay-modal='true' data-overlay-title="${loca.LOCA_FLEET_MISSILEATTACK}">${loca.LOCA_FLEET_MISSILEATTACK}</a></li>`;
      }
    }

    return linkHTML;
  }

  return loca.LOCA_FLEET_NO_ACTION_AVAILABLE;
}
