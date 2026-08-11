function renderPlayer(galaxyContentObject, systemData) {
  let { player } = galaxyContentObject;

  if (player && player.playerId !== 99999) {
    $("#galaxyRow" + galaxyContentObject.position + " .cellPlayerName").html(
      getPlayerName(galaxyContentObject, systemData),
    );
  }
}
