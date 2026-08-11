function getDiscoveryLinkIcon(galaxyContentObject) {
  let discoverLink = "";

  if (constants.lifeformEnabled === true) {
    const discoverMission = galaxyContentObject.availableMissions.find(
      (mission) => mission.missionType === constants.discover,
    );

    if (typeof discoverMission !== "undefined") {
      if (discoverMission.canSend === true) {
        const titleText = galaxyLoca.discoverySend + " " + discoverMission.discoveryCount;
        discoverLink = `<div class="planetDiscoverIcons planetDiscoverDefault icon"><a href="#"
                    class="tooltip js_hideTipOnMobile ipiHintable planetDiscover position${galaxyContentObject.position}"
                    data-ipi-hint="ipiDiscoverLifeform"
                    onClick="discoverPlanet(
                        '${discoverMission.link}',
                        {
                            'galaxy': ${galaxyContentObject.galaxy},
                            'system': ${galaxyContentObject.system},
                            'position':${galaxyContentObject.position},
                            '_token': token
                        }
                    ); return false;"
                    title="${titleText}">
                </a></div>`;
      } else {
        discoverLink = `<div class="planetDiscoverIcons planetDiscoverUnavailable tooltip ipiHintable icon js_hideTipOnMobile"
                    data-ipi-hint="ipiDiscoverLifeform"
                    title="${discoverMission.canSend}">
                </div>`;
      }
    }
  }

  return discoverLink;
}
