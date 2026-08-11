function unionUser(response) {
  var data = $.parseJSON(response);

  if (data["status"]) {
    addUserToUnionByForm();
  } else {
    errorBoxAsArray(data["errorbox"]);
  }
}
