function colorNumberInFrontOfFriendsPlanet(galaxyContentObject) {
  let { player } = galaxyContentObject;

  if (player.isBuddy) {
    $("#galaxyRow" + galaxyContentObject.position + " .cellPosition").addClass("status_abbr_buddy");
  }
}
