function showGalaxy(galaxy, system, planet) {
  openParentLocation(
    "index.php?page=ingame&component=galaxy&no_header=1&galaxy=" + galaxy + "&system=" + system + "&planet=" + planet,
  );
}
