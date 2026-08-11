function getMoonTooltip(planet, galaxyContentObject, systemData) {
  let { galaxy, system, position } = galaxyContentObject;
  return `
        <div id="moon${position}" style="display: none;" class="htmlTooltip galaxyTooltip">
            <h1><span class="textNormal">${planet.planetName}</span></h1>
            <div class="splitLine"></div>
            <ul class="ListImage">
                <li><span id="pos-moon">[${galaxy}:${system}:${position}]</span></li>
                <li><div class="moonTooltip micromoon ${planet["imageInformation"]}"></div></li>
                <li><span id="moonsize" title="${loca.LOCA_GALAXY_MOON_DIAMETER_KM}">${planet.size} ${loca.LOCA_OVERVIEW_JS_KM}</span></li>
            </ul>
            <ul class="ListLinks">
                ${getPlanetOrMoonTooltipLinks(planet, galaxyContentObject, systemData)}
            </ul>
        </div>
        `;
}
