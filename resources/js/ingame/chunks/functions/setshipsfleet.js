function setShipsFleet(ships, tempName, techId) {
  $("#template_id").val(techId);
  $("#template_name").val(tempName);

  for (var techID in ships) {
    $("#ship" + techID).val(ships[techID]);
  }
}
