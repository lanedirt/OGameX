function launchMissiles(response) {
  var data = $.parseJSON(response);
  token = data.newAjaxToken;
  updateOverlayToken("phalanxSystemDialog", data.newAjaxToken);
  updateOverlayToken("phalanxDialog", data.newAjaxToken);

  if (data["status"]) {
    $("#missileValue").html(data["rockets"]);
    getAjaxEventbox();
  }

  errorBoxAsArray(data["errorbox"]);
  $("#rocketattack").closest(".ui-dialog-content").remove();
}
