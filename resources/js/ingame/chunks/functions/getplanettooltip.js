function getPlanetTooltip(planet, galaxyContentObject, systemData) {
  let { galaxy, system, position } = galaxyContentObject;
  return `
        <div id="planet${position}" style="display: none;" class="htmlTooltip galaxyTooltip">
            <h1>${loca.LOCA_ALL_PLANET}: <span class="textNormal">${planet.planetName}</span></h1>
            <div class="splitLine"></div>
            <ul class="ListImage">
                <li><span>[${galaxy}:${system}:${position}]</span></li>
                <li><div class="planetTooltip microplanet ${planet["imageInformation"]}"></div></li>
            </ul>
            <ul class="ListLinks">
                ${getPlanetOrMoonTooltipLinks(planet, galaxyContentObject, systemData)}
            </ul>
        </div>
        `;
}
