function getEventDebrisTooltip(planet, galaxyContentObject, systemData) {
  let { galaxy, system, position } = galaxyContentObject;
  let darkMatterObject = planet.resources.darkMatter;
  let darkmatter = number_format(darkMatterObject.amount);
  let recyclersToSend = planet.requiredShips;
  let linkHTML = "";

  if (!systemData.canFly) {
    linkHTML += `<li>${loca.LOCA_FLEET_NO_FREE_SLOTS}</li>`;

    if (!systemData.hasAdmiral) {
      linkHTML += `<li><a href="${premiumLink}">${loca.LOCA_HEADER_GETADMIRAL}</a></li>`;
    }
  } else if (systemData.availableRecyclers > 0) {
    let recyclerJS = `sendShips(${8}, ${galaxy}, ${system}, ${position}, ${planet.planetType}, ${recyclersToSend})`;
    linkHTML = `<li><a href="#" onClick="${recyclerJS};return false">${loca.LOCA_GALAXY_DEBRIS_REDUCE}</a></li>`;
  } else {
    linkHTML = `<li><span class="inactiveLink">${loca.LOCA_GALAXY_DEBRIS_REDUCE}</span></li>`;
  }

  let headline = loca.LOCA_FLEET_DEBRIS;
  return `
        <div id="debris${position}" style="display: none;" class="htmlTooltip galaxyTooltip">
            <h1>${headline}</h1>
            <div class="splitLine"></div>
            <ul class="ListImage">
                <li><span id="pos-debris">[${galaxy}:${system}:${position}]</span></li>
                <li><img class="float_left" src="/img/icons/e1b6654d1b29bc65aea0b8fc79be80.png" width="30" height="30" alt="${headline}"/></li>
            </ul>
            <ul class="ListLinks">
                <li class="debris-content">${loca.LOCA_ALL_DARKMATTER}: ${darkmatter}</li>
                ${linkHTML}
            </ul>
        </div>
    `;
}
