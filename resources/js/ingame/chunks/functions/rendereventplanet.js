function renderEventPlanet(planet, galaxyContentObject, systemData) {
  if (!planet) {
    return;
  }

  let lastPosition = $("div.expeditionDebrisSlotBoxRow");
  let positionNumber = parseInt(lastPosition.find(".cellPosition").text()) + 1;
  lastPosition.after(`
        <div class="eventSlotRow">
            <div class="eventSlotBoxCell cellPosition">${positionNumber}</div>
            <div class="bdaySlotBox"  id="galaxyRow17planet">
                <div>
                    <h3 class="title float_left">${loca.LOCA_EVENTH_ENEMY_INFINITELY_SPACE}:</h3>
                </div>
                <div class="birthdayNameWrapper">
                    <div id="birthdayName" class="name float_left tooltipRel tooltipClose tooltipRight js_hideTipOnMobile js_bday_planet"
                       rel="planet17"
                    >
                        <div style="position: relative;width: 30px;height: 30px;display: inline-block;">
                            <img class="float_left"
                                src="${planet.imageInformation}"
                                width="30"
                                height="30"
                            />
                            ${addFleetContainer(galaxyContentObject.position, planet.planetType)}
                        </div>${planet.planetName}
                        ${getEventPlanetTooltip(planet, galaxyContentObject, systemData)}
                    </div>
                </div>
            </div>
        </div>
    `);
  getFleetIcon(planet.fleet, galaxyContentObject.position, planet.planetType);
}
