function getActions(galaxyContentObject, systemData) {
  let { galaxy, system, position, player } = galaxyContentObject;
  let { actions } = player;
  let mainPlanet = galaxyContentObject.planets.find((planet) => planet.planetType === 1);
  let holdMissionAvailable = mainPlanet.availableMissions.find((availMission) => availMission.missionType === 5);
  let espionageReportAvailable = mainPlanet.availableMissions.find(
    (availMission) => availMission.missionType === 6 && availMission.reportId,
  );
  let espionageClass = "";

  if (espionageReportAvailable) {
    espionageClass = "hueRotate";
  }

  let espionageLink = "";

  if (player.isAdmin) {
    espionageLink = `<div class="emptyAction"></div>`;
  } else {
    if (galaxyContentObject.actions.canEspionage === false) {
      espionageLink = `<a class="tooltip js_hideTipOnMobile espionage"
                   title="${loca.LOCA_FLEET_NO_ESPIONAGE}"
                   href="javascript: void(0);"
                >
                    <span class="icon icon_eye grayscale"></span>
                </a>`;
    } else {
      let ipiHint = "ipiGalaxyActionSpy";

      if (galaxyContentObject.player.isOnVacation) {
        ipiHint = "ipiGalaxyActionSpyVacation";
      }

      espionageLink = `
                <a class="tooltip js_hideTipOnMobile espionage ipiHintable"
                       title="${loca.LOCA_FLEET_ESPIONAGE}"
                       href="javascript: void(0);"
                       onclick="${getEspionageMission(galaxyContentObject, mainPlanet, systemData)}"
                       data-ipi-hint="${ipiHint}"
                    >
                    <span class="icon icon_eye ${espionageClass}"></span>
                </a>`;
    }
  }

  let messageLink = "";

  if (actions.message.available) {
    if (!actions.message.disabledChatBar) {
      messageLink = `<a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerId="${player.playerId}" title="${actions.message.title}"><span class="icon icon_chat"></span></a>`;
    } else {
      messageLink = `<a href="${actions.message.link}" class="tooltip" data-playerId="${player.playerId}" title="${actions.message.title}"><span class="icon icon_chat"></span></a>`;
    }
  } else if (actions.support && actions.support.available) {
    // Support button for admins (TODO: Implement proper support contact when messaging system is ready)
    messageLink = `
                <a href="${actions.support.link}"
                    target="_blank" title="${actions.support.title}"
                    class="tooltip js_hideTipOnMobile icon">
                        <span class="support_icon icon icon_mail"></span>
                </a>
            `;
  } else {
    messageLink = `<div class="emptyAction"></div>`;
  }

  let buddyLink = "";

  if (actions.buddies.available) {
    buddyLink = `
            <a class="tooltip buddyrequest ipiHintable"
               title="Buddy request to player"
               href="javascript:void(0);"
               data-playerid="${actions.buddies.playerId}"
               data-playername="${actions.buddies.playerName}"
               data-ipi-hint="ipiGalaxySendBuddyRequest"
            >
                <span class="icon icon_user"></span>
            </a>`;
  } else {
    buddyLink = `<div class="emptyAction"></div>`;
  }

  let missileLink = "";

  if (
    galaxyContentObject.actions.canMissileAttack &&
    !player.isAdmin &&
    galaxy &&
    system &&
    position &&
    systemData.availableMissiles > 0
  ) {
    if (systemData.showOutlawWarning && !systemData.isOutlaw && player.isStrong && !holdMissionAvailable) {
      missileLink = `
                <a class="tooltip js_hideTipOnMobile missleattack"
                       title="${loca.LOCA_FLEET_MISSILEATTACK}"
                       href="javascript: void(0);"
                       onclick="outlawWarning(
                       10,
                       ${galaxy},
                       ${system},
                       ${position},
                       1,
                       ${systemData.availableMissiles}
                               ); return false;"
                    >
                    <span class="icon icon_missile"></span>
                </a>`;
    } else {
      missileLink = `<a class="tooltip js_hideTipOnMobile overlay missleattack"
               title="${loca.LOCA_FLEET_MISSILEATTACK}"
               href="${galaxyContentObject.actions.missileAttackLink}&planetType=${mainPlanet.planetType}"
               data-overlay-modal='true'
               data-overlay-title="${loca.LOCA_FLEET_MISSILEATTACK}"
            >
                <span class="icon icon_missile"></span>
            </a>`;
    }
  } else if (player.isAdmin) {
    missileLink = `<div class="emptyAction"></div>`;
  } else {
    missileLink = `<a class="tooltip js_hideTipOnMobile missleattack"
               title="${loca.LOCA_FLEET_MISSILEATTACK}"
               href="javascript: void(0);"
            >
                <span class="icon icon_missile grayscale"></span>
            </a>`;
  }

  const discoverLink = getDiscoveryLinkIcon(galaxyContentObject);
  return `
        ${discoverLink}
        ${espionageLink}
        ${messageLink}
        ${buddyLink}
        ${missileLink}
        `;
}
