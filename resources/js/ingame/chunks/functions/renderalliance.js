function renderAlliance(galaxyContentObject, systemData) {
  let { player } = galaxyContentObject;

  if (player.allianceId) {
    $("#galaxyRow" + galaxyContentObject.position + " .cellAlliance").html(`
                <span
                class="${player.isAllianceMember ? "status_abbr_ally_own" : ""} tooltipRel tooltipClose tooltipRight js_hideTipOnMobile"
                rel="alliance${player.allianceId}">
                    ${player.allianceTag} ${getAllianceTooltip(galaxyContentObject, systemData)}
                </span>
            `);
  }
}
