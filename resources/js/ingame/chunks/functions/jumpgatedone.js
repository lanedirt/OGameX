function jumpgateDone(data) {
  var data = $.parseJSON(data);

  if (data["status"]) {
    planet = data["targetMoon"];
    $(".overlayDiv").dialog("destroy");
  }

  errorBoxAsArray(data["errorbox"]);

  if (typeof data.newAjaxToken != "undefined") {
    setNewTokenData(data.newAjaxToken);
  }
}
