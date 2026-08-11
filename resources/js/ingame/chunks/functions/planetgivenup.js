function planetGivenup(data) {
  errorBoxAsArray(data["errorbox"]);

  if (typeof data["newAjaxToken"] == "string") {
    $("#planetMaintenanceDelete input[name='_token']").val(data["newAjaxToken"]);
  }

  if (typeof data["password_checked"] != "undefined" && data["password_checked"]) {
    $("#planetMaintenanceDelete").attr("action", data["intent"]);
  }
}
