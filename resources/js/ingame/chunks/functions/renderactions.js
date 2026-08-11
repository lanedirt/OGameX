function renderActions(galaxyContentObject, systemData) {
  if (systemData.playerId !== galaxyContentObject.player.playerId) {
    $("#galaxyRow" + galaxyContentObject.position + " .cellAction").html(
      `${getActions(galaxyContentObject, systemData)}`,
    );
  } else {
    let result = `
        ${getDiscoveryLinkIcon(galaxyContentObject)}
        ${`<div class="emptyAction"></div>`}
        ${`<div class="emptyAction"></div>`}
        ${`<div class="emptyAction"></div>`}
        ${`<div class="emptyAction"></div>`}
       `;
    $("#galaxyRow" + galaxyContentObject.position + " .cellAction")
      .html(getDiscoveryLinkIcon(galaxyContentObject))
      .html(`${result}`);
  }
}
