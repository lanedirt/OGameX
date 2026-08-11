function getAllianceTooltip(galaxyContentObject) {
  let { player } = galaxyContentObject;
  let { alliance } = player.actions;

  if (!alliance) {
    return "";
  }

  let allianceClass = "";

  if (alliance.allianceClassName && alliance.allianceClassCss) {
    allianceClass = `<li>${loca.LOCA_ALLIANCE_CLASS}: <span class="${alliance.allianceClassCss}">${alliance.allianceClassName}</span></li>`;
  }

  let infoPageLink;

  if (player.isAllianceMember) {
    infoPageLink = `<li><a href="${alliance.infoPageLink}">${alliance.infoPageTitle}</a></li>`;
  } else {
    infoPageLink = `<li><a href="/alliance/info/${player.allianceId}" target="_blank">${alliance.infoPageTitle}</a></li>`;
  }

  let applicationLink = "";

  if (alliance.applicationLink && alliance.applicationTitle) {
    applicationLink = `<li><a href="${alliance.applicationLink}">${alliance.applicationTitle}</a></li>`;
  }

  return `
        <div id="alliance${player.allianceId}" style="display: none;"  class="htmlTooltip galaxyTooltip">
            <h1>
                ${getAllianceSelectedLanguage(player)}
                ${player.allianceName}
            </h1>
            <div class="splitLine"></div>
            <ul class="ListLinks">
                <li class="rank">${loca.LOCA_GALAXY_RANK}: <a href="${alliance.highscoreLink}">${alliance.highscoreTitle}</a></li>
                <li class="members">${loca.LOCA_NETWORK_USERS}: ${alliance.memberCount}</li>
                ${allianceClass}
                ${infoPageLink}
                ${applicationLink}
            </ul>
        </div>
        `;
}
