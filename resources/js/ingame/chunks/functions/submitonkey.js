function submitOnKey(keyCode) {
  selectShipsPerFleet("0");

  if (keyCode === "ArrowLeft") {
    system = system > 1 ? parseInt(system) - 1 : maxSystems;

    if (isMobile) {
      loadContent(galaxy, system);
    } else {
      loadContentNew(galaxy, system);
    }
  } else if (keyCode === "ArrowRight") {
    system = system < maxSystems ? parseInt(system) + 1 : 1;

    if (isMobile) {
      loadContent(galaxy, system);
    } else {
      loadContentNew(galaxy, system);
    }
  } else if (keyCode === "ArrowDown") {
    galaxy = galaxy > 1 ? parseInt(galaxy) - 1 : maxGalaxies;

    if (isMobile) {
      loadContent(galaxy, system);
    } else {
      loadContentNew(galaxy, system);
    }
  } else if (keyCode === "ArrowUp") {
    galaxy = galaxy < maxGalaxies ? parseInt(galaxy) + 1 : 1;

    if (isMobile) {
      loadContent(galaxy, system);
    } else {
      loadContentNew(galaxy, system);
    }
  }
}
