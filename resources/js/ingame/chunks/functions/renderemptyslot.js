function renderEmptySlot(galaxyContentObject, systemData, reservedPlanets) {
  if (galaxyContentObject.availableMissions) {
    let planetNameCell = $("#galaxyRow" + galaxyContentObject.position + " .cellPlanetName");
    planetNameCell.html("");
    let reservedPlanet = reservedPlanets[galaxyContentObject.position];

    if (reservedPlanet && reservedPlanet.isReserved && parseInt(reservedPlanet.user_id) === systemData.playerId) {
      planetNameCell.append(`
                <span class="planetMoveGalaxyCooldown" id="cooldown-${galaxyContentObject.position}">
                    ${loca.LOCA_ALL_AJAXLOAD}
                </span>
            `);
      buildListCountdowns.push(
        new SimpleCountdownTimer(`#cooldown-${galaxyContentObject.position}`, reservedPlanet.cooldown, toGalaxyLink),
      );
    }

    renderEmptySlotActions(galaxyContentObject, systemData);
  }
}
