function renderEventSpaceObjects(galaxyContentObject, systemData) {
  galaxyContentObject.planets.map((planet) => {
    switch (planet.planetType) {
      case 1:
        renderEventPlanet(planet, galaxyContentObject, systemData);
        break;

      case 2:
        renderEventDebris(planet, galaxyContentObject, systemData);
        break;
    }
  });
}
