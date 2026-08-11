function getEmptySlotActions(galaxyContentObject, systemData) {
  let emptyLink = `<div class="emptyAction"></div>`;
  let coloniseMission = galaxyContentObject.availableMissions.find((availMission) => availMission.missionType === 7);
  let colonisationLink = "";

  if (!systemData.canColonize || !coloniseMission || coloniseMission.link === "#") {
    colonisationLink = `<div class="tooltip planetMoveIcons colonize-inactive icon tpd-hideOnClickOutside"
                      title="${coloniseMission ? coloniseMission.description : loca.LOCA_GALAXY_ERROR_COLONIZATION}"></div>`;
  } else {
    colonisationLink = `<a href="${coloniseMission.link}" class="tooltip planetMoveIcons colonize-active icon tpd-hideOnClickOutside ipiHintable" data-ipi-hint="ipiGalaxyColonize">
                    <div class="tooltip planetMoveIcons colonize-active icon tpd-hideOnClickOutside"
                      title="${coloniseMission.description}"></div></a>`;
  }

  let planetMove = galaxyContentObject.availableMissions.find((availMission) => availMission.missionType === 0);
  let planetMoveLink = "";

  if (planetMove === undefined) {
    planetMoveLink = `<div class="emptyAction"></div>`;
  } else if (planetMove.planetMovePossible === true) {
    planetMoveLink = `<a class="planetMoveIcons planetMoveDefault tooltip icon js_hideTipOnMobile"
               href="javascript: void(0);"
               onClick="movePlanet(
                       '${planetMove.moveLink}',
                       {'position':${galaxyContentObject.position},
                       'galaxy': ${galaxyContentObject.galaxy},
                       'system': ${galaxyContentObject.system}},
                       '${planetMove.galaxyLink}'
                       ); return false;"
               title="${planetMove.title}"
            ><div class="planetMoveIcons planetMoveDefault tooltip icon js_hideTipOnMobile"
                      title="${planetMove.title}"></div></a>`;
  } else {
    planetMoveLink = `<div class="planetMoveIcons planetMoveInactive tooltip icon"
                      title="${planetMove.title}"></div>`;
  }

  const discoverLink = getDiscoveryLinkIcon(galaxyContentObject);
  return `
        ${discoverLink}
        ${colonisationLink}
        ${planetMoveLink}
        ${emptyLink}
        ${emptyLink}
        `;
}
